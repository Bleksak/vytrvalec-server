<?php

namespace App\Requests;

use App\Attributes\DB;
use App\Validation\TypeSystem;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest
{
    private array $populationErrors = [];

    public function __construct(
        protected ValidatorInterface     $validatorInterface,
        protected EntityManagerInterface $entityManagerInterface,
        protected RequestStack           $requestStack
    )
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = $request->getPayload()->all();

        $this->populate($data);
        $this->populate($request->files->all());
    }

    private static function getRequest(): Request
    {
        return Request::createFromGlobals();
    }

    /**
     * @throws ReflectionException
     */
    protected function populate(array $arrayData): void
    {
        $reflectionClass = new ReflectionClass($this::class);

        foreach ($arrayData as $property => $value) {
            if (property_exists($this, $property)) {
                $reflectionProperty = $reflectionClass->getProperty($property);
                $propertyAttribute = $reflectionProperty->getAttributes(DB::class);
                $type = $reflectionProperty->getType();

                if (!empty($propertyAttribute)) {
                    $this->{$property} = $this->entityManagerInterface->getRepository($reflectionProperty->getType()->getName())->find($value);
                } else if(TypeSystem::canAssign($value, TypeSystem::reflectionTypeToBuiltinType($type->getName()))) {
                    $this->{$property} = $value;
                } else if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                    $reflectionType = $reflectionProperty->getType()->getName();
                    $this->{$property} = new $reflectionType($value);
                } else {
                    $this->populationErrors[] = new ConstraintViolation('bad_type', 'bad_type', [], null, $property, $value);
                }
            }
        }

        foreach($reflectionClass->getMethods(ReflectionMethod::IS_PROTECTED) as $method) {
            if(str_starts_with($method->getName(), 'populate')) {
                $method->invoke($this);
            }
        }

    }

    public function validate(): array
    {
        $messages = [];
        $errors = $this->validatorInterface->validate($this);

        try {
            $reflectionClass = new ReflectionClass($this::class);

            foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PROTECTED) as $method) {
                if (str_starts_with($method->getName(), "validate")) {
                    $error = $method->invoke($this);

                    if ($error !== null) {
                        $messages[$error->getPropertyPath()] = $error->getMessageTemplate();
                    }
                }
            }
        } catch (ReflectionException) {
            $messages[] = 'unrecognized_exception';
        }

        foreach ($this->populationErrors as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessageTemplate();
        }

        foreach ($errors as $message) {
            $messages[$message->getPropertyPath()] = $message->getMessageTemplate();
        }

        return $messages;
    }

    public function getResponse(bool $success, mixed $response = null): Response
    {
        if (!$success) {
            return new JsonResponse([
                'success' => false,
                'errors' => $response
            ]);
        }

        if ($response != null) {
            return new JsonResponse([
                'success' => true,
                'result' => $response
            ]);
        }

        return new JsonResponse([
            'success' => true
        ]);
    }
}