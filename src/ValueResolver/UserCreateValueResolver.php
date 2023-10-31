<?php

namespace App\ValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;

class UserCreateValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();

        
       
        return [];
    }
}
