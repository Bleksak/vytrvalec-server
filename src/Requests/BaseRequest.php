<?php

namespace App\Requests;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest {
  
  public function __construct(
      protected ValidatorInterface $validatorInterface,
      protected EntityManagerInterface $entityManagerInterface
  )
  {
      if($this->isApi()) {
          $this->populate(self::dataFromJson());
      } else {
          $this->populate(self::getRequest()->request->all());
          $this->populate(self::getRequest()->files->all());
      }

      if($this->autoValidateRequest()) {
          $this->validate();
      }
  }

  protected function autoValidateRequest(): bool
  {
      return true;
  }
  
  protected function isApi(): bool
  {
    return false;
  }

  private static function dataFromJson(): array
  {
      return json_decode(file_get_contents("php://input"), true);
  }
  
  private static function getRequest(): Request
  {
      return Request::createFromGlobals();
  }
  
  protected function populate(array $arrayData): void 
  {
      $reflectionClass = new ReflectionClass($this::class);

      foreach($arrayData as $property => $value) {
          if(property_exists($this, $property)) {
              $reflectionProperty = $reflectionClass->getProperty($property);
              $propertyAttribute = $reflectionProperty->getAttributes('App\Requests\DB');

              if(!empty($propertyAttribute)) {
                  $this->{$property} = $this->entityManagerInterface->getRepository($reflectionProperty->getType()->getName())->find($value);
              } else if(!$reflectionProperty->getType()->isBuiltin()) {
                  $reflectionType = $reflectionProperty->getType()->getName();
                  $this->{$property} = new $reflectionType($value);
              } else {
                  $this->{$property} = $value;
              }
          }
      }
  }
  
  public function validate(): array
  {
      $errors = $this->validatorInterface->validate($this);
      $reflectionClass = new ReflectionClass($this::class);
      foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PROTECTED) as $method) {
          if(str_starts_with($method->getName(), "validate")) {
              $method->invoke($this);
          }
      }

      $messages = ['success' => false, 'errors' => []];
      foreach ($errors as $message) {
          $messages['errors'][] = [
              'property' => $message->getPropertyPath(),
              'value' => $message->getInvalidValue(),
              'message' => $message->getMessage(),
              ];
      }

      if($this->isApi()) {
          if (!empty($messages['errors'])) {
              $response = new JsonResponse($messages);
              $response->send();

              exit;
          }
      }

      return $messages;
  }
}