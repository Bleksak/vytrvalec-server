<?php

namespace App\Validation;

use App\Validation\Constraint\UniqueValue;
use Countable;
use DateTimeInterface;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Iterator;
use IteratorAggregate;
use Stringable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use function count;
use function in_array;
use function is_array;
use function is_bool;
use function is_object;
use function is_string;

class UniqueValueValidator extends ConstraintValidator
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param object $entity
     *
     * @return void
     *
     * @throws UnexpectedTypeException
     * @throws ConstraintDefinitionException
     */
    public function validate(mixed $entity, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueValue) {
            throw new UnexpectedTypeException($constraint, UniqueEntity::class);
        }

        if (!is_array($constraint->fields) && !is_string($constraint->fields)) {
            throw new UnexpectedTypeException($constraint->fields, 'array');
        }

        if (null !== $constraint->errorPath && !is_string($constraint->errorPath)) {
            throw new UnexpectedTypeException($constraint->errorPath, 'string or null');
        }

        $fields = (array) $constraint->fields;

        if (0 === count($fields)) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }

        if (null === $entity) {
            return;
        }

        if (!is_object($entity)) {
            throw new UnexpectedValueException($entity, 'object');
        }

        if ($constraint->em) {
            $em = $this->registry->getManagerForClass($constraint->em);

            if (!$em) {
                throw new ConstraintDefinitionException(sprintf('Object manager "%s" does not exist.', $constraint->em));
            }
        } else {
            $em = $this->registry->getManagerForClass($entity::class);

            if (!$em) {
                throw new ConstraintDefinitionException(sprintf('Unable to find the object manager associated with an entity of class "%s".', get_debug_type($entity)));
            }
        }

        $class = new \ReflectionClass($entity);

        $criteria = [];
        $hasIgnorableNullValue = false;

        foreach ($fields as $fieldName) {
            $fieldValue = $class->getProperty($fieldName)->getValue($entity);

            if (null === $fieldValue && $this->ignoreNullForField($constraint, $fieldName)) {
                $hasIgnorableNullValue = true;

                continue;
            }

            $criteria[$fieldName] = $fieldValue;

            if (null !== $criteria[$fieldName]) {
                /* Ensure the Proxy is initialized before using reflection to
                 * read its identifiers. This is necessary because the wrapped
                 * getter methods in the Proxy are being bypassed.
                 */
                $em->initializeObject($criteria[$fieldName]);
            }
        }

        // validation doesn't fail if one of the fields is null and if null values should be ignored
        if ($hasIgnorableNullValue) {
            return;
        }

        // skip validation if there are no criteria (this can happen when the
        // "ignoreNull" option is enabled and fields to be checked are null
        if (empty($criteria)) {
            return;
        }

        if (null !== $constraint->entityClass) {
            /* Retrieve repository from given entity name.
             * We ensure the retrieved repository can handle the entity
             * by checking the entity is the same, or subclass of the supported entity.
             */
            $repository = $em->getRepository($constraint->entityClass);
            $supportedClass = $repository->getClassName();

            if (!$entity instanceof $supportedClass) {
                throw new ConstraintDefinitionException(sprintf('The "%s" entity repository does not support the "%s" entity. The entity should be an instance of or extend "%s".', $constraint->entityClass, $class->getName(), $supportedClass));
            }
        } else {
            $repository = $em->getRepository($constraint->em);
        }

        $arguments = [$criteria];

        /* If the default repository method is used, it is always enough to retrieve at most two entities because:
         * - No entity returned, the current entity is definitely unique.
         * - More than one entity returned, the current entity cannot be unique.
         * - One entity returned the uniqueness depends on the current entity.
         */
        if ('findBy' === $constraint->repositoryMethod) {
            $arguments = [$criteria, null, 2];
        }

        $result = $repository->{$constraint->repositoryMethod}(...$arguments);

        if ($result instanceof IteratorAggregate) {
            $result = $result->getIterator();
        }

        /* If the result is a MongoCursor, it must be advanced to the first
         * element. Rewinding should have no ill effect if $result is another
         * iterator implementation.
         */
        if ($result instanceof Iterator) {
            $result->rewind();
            if ($result instanceof Countable && 1 < count($result)) {
                $result = [$result->current(), $result->current()];
            } else {
                $result = $result->valid() && null !== $result->current() ? [$result->current()] : [];
            }
        } elseif (is_array($result)) {
            reset($result);
        } else {
            $result = null === $result ? [] : [$result];
        }

        /* If no entity matched the query criteria or a single entity matched,
         * which is the same as the entity being validated, the criteria is
         * unique.
         */
        if (!$result || (1 === count($result) && current($result) === $entity)) {
            return;
        }

        $errorPath = $constraint->errorPath ?? $fields[0];
        $invalidValue = $criteria[$errorPath] ?? $criteria[$fields[0]];

        $this->context->buildViolation($constraint->message)
            ->atPath($errorPath)
            ->setInvalidValue($invalidValue)
            ->setCode(UniqueEntity::NOT_UNIQUE_ERROR)
            ->setCause($result)
            ->addViolation();
    }

    private function ignoreNullForField(UniqueValue $constraint, string $fieldName): bool
    {
        if (is_bool($constraint->ignoreNull)) {
            return $constraint->ignoreNull;
        }

        return in_array($fieldName, (array) $constraint->ignoreNull, true);
    }

    private function formatWithIdentifiers(ObjectManager $em, ClassMetadata $class, mixed $value): string
    {
        if (!is_object($value) || $value instanceof DateTimeInterface) {
            return $this->formatValue($value, self::PRETTY_DATE);
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        if ($class->getName() !== $idClass = $value::class) {
            // non-unique value might be a composite PK that consists of other entity objects
            if ($em->getMetadataFactory()->hasMetadataFor($idClass)) {
                $identifiers = $em->getClassMetadata($idClass)->getIdentifierValues($value);
            } else {
                // this case might happen if the non-unique column has a custom doctrine type and its value is an object
                // in which case we cannot get any identifiers for it
                $identifiers = [];
            }
        } else {
            $identifiers = $class->getIdentifierValues($value);
        }

        if (!$identifiers) {
            return sprintf('object("%s")', $idClass);
        }

        array_walk($identifiers, function (&$id, $field) {
            if (!is_object($id) || $id instanceof DateTimeInterface) {
                $idAsString = $this->formatValue($id, self::PRETTY_DATE);
            } else {
                $idAsString = sprintf('object("%s")', $id::class);
            }

            $id = sprintf('%s => %s', $field, $idAsString);
        });

        return sprintf('object("%s") identified by (%s)', $idClass, implode(', ', $identifiers));
    }
}