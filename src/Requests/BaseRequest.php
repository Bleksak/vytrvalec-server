<?php

namespace App\Requests;

use App\Attributes\DB;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest
{
    public function __construct(
        protected ValidatorInterface     $validatorInterface,
        protected EntityManagerInterface $entityManagerInterface
    )
    {
        $request = self::getRequest();

        $this->populate($request->getPayload()->all());
        $this->populate($request->files->all());

        if ($this->autoValidateRequest()) {
            $this->validate();
        }
    }

    protected function autoValidateRequest(): bool
    {
        return true;
    }

    protected function isApi(): bool
    {
        return true;
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

                if (!empty($propertyAttribute)) {
                    $this->{$property} = $this->entityManagerInterface->getRepository($reflectionProperty->getType()->getName())->find($value);
              } else if(!$reflectionProperty->getType()->isBuiltin()) {
                  $reflectionType = $reflectionProperty->getType()->getName();
//                  dd($value);
                  $this->{$property} = new $reflectionType($value);
                } else {
                    $this->{$property} = $value;
                }
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

                    if($error !== null) {
                        $messages[$error->getPropertyPath()] = $error->getMessageTemplate();
                    }
                }
            }
        } catch (ReflectionException) {
            $messages[] = 'unrecognized_exception';
        }

        foreach ($errors as $message) {
            $messages[$message->getPropertyPath()] = $message->getMessageTemplate();
        }

        if ($this->isApi()) {
            if (!empty($messages)) {
                $this->getResponse(false, $messages)->send();
                exit;
            }
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