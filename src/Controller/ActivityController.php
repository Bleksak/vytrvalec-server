<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ActivityController extends AbstractController {
  #[Route('/api/activities', name: 'api_categories', methods: ['GET'])]
  public function categoryList(ActivityRepository $activityRepository) 
  {
    return $this->json($activityRepository->findAll());
  }
}