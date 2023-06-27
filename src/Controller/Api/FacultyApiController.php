<?php

namespace App\Controller\Api;

use App\Repository\FacultyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class FacultyApiController extends AbstractController
{
    public function __construct(private readonly FacultyRepository $facultyRepository)
    {
    }
    #[Route('/api/faculties/list', name:'api_faculties_list', methods: ['GET'])]
    public function list(SerializerInterface $serializer): Response
    {
        return $this->json($serializer->normalize($this->facultyRepository->findBy(['visible' => true]), null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['visible'],
        ]));
    }
}