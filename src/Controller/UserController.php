<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct() {}

    #[Route('/user/register', name: 'user_register', methods:['GET', 'POST'])]
    public function register(): Response
    {
        return $this->render('user/register.html.twig');
    }

    #[Route('/user/login', name: 'user_login', methods:['GET', 'POST'])]
    public function login(): Response
    {
        return $this->render('base.html.twig');
    }

    #[Route('/user/profile/{user}', name: 'user_profile', methods: ['GET'])]
    public function profile(User $user = null) : Response
    {
        return $this->render('base.html.twig');
    }
}
