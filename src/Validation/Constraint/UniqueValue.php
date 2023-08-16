<?php

namespace App\Validation\Constraint;

use App\Validation\UniqueValueValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;
use function is_array;
use function is_string;


#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class UniqueValue extends Constraint
{
    public string $message = 'not_unique';
    public array $fields = [];
    public ?string $em = null;
    public ?string $service = null;
    public ?string $entityClass = null;
    public ?string $errorPath = null;
    public bool|string|array $ignoreNull = true;
    public string $repositoryMethod = 'findBy';

    public function __construct(
        $fields,
        string $em,
        string $message = null,
        string $service = null,
        string $entityClass = null,
        string $repositoryMethod = null,
        string $errorPath = null,
        bool|string|array $ignoreNull = null,
        array $groups = null,
        $payload = null,
        array $options = []
    ) {
        if (is_array($fields) && is_string(key($fields))) {
            $options = array_merge($fields, $options);
        } elseif (null !== $fields) {
            $options['fields'] = $fields;
        }

        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->service = $service ?? $this->service;
        $this->em = $em;
        $this->entityClass = $entityClass ?? $this->entityClass;
        $this->repositoryMethod = $repositoryMethod ?? $this->repositoryMethod;
        $this->errorPath = $errorPath ?? $this->errorPath;
        $this->ignoreNull = $ignoreNull ?? $this->ignoreNull;
    }

    public function getRequiredOptions(): array
    {
        return ['fields'];
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getDefaultOption(): string
    {
        return 'fields';
    }

    public function validatedBy(): string
    {
        return UniqueValueValidator::class;
    }
}