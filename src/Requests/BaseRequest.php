<?php

namespace App\Requests;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest {
  public function __construct(protected ValidatorInterface $validatorInterface)
  {
    $this->populate(self::getRequest()->toArray());
    
    if($this->autoValidateRequest()) {
      $this->validate();
    }
  }
  
  protected function autoValidateRequest(): bool
  {
    return true;
  }
  
  public static function getRequest(): Request
  {
    return Request::createFromGlobals();
  }
  
  protected function populate(array $arrayData): void 
  {
    foreach($arrayData as $property => $value) {
      if(property_exists($this, $property)) {
        $this->{$property} = $value;
      }
    }
  }
  
  public function validate()
  {
    $errors = $this->validatorInterface->validate($this);

    $messages = ['success' => false, 'errors' => []];
    foreach ($errors as $message) {
        $messages['errors'][] = [
            'property' => $message->getPropertyPath(),
            'value' => $message->getInvalidValue(),
            'message' => $message->getMessage(),
        ];
    }

    if (!empty($messages['errors'])) {
        $response = new JsonResponse($messages);
        $response->send();

        exit;
    }
  }
}