<?php

namespace App\ValueResolver;

use App\Entity\Faculty;
use App\Repository\FacultyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;


#[AsTargetedValueResolver('faculty_id')]
class FacultyValueResolver implements ValueResolverInterface
{
    public function __construct(private readonly FacultyRepository $facultyRepository)
    {
        dd("testing testing");
    }
    
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        dd("test");

        if($argument->getType() !== Faculty::class) {
            return [];
        }

        $value = $request->attributes->get($argument->getName());

        if(!is_int($value)) {
            return [];
        }

        return [$this->facultyRepository->find($value)];
    }
}
