<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/register', name: 'user_register', methods:['GET', 'POST'])]
    public function register(Request $request): Response
    {
        if($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            dd($user);
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // public function login(): Response {

    // }

    // public function logout(): Response {

    // }
}
