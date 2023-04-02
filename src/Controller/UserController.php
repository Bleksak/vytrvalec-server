<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    private $em;
    private $hasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher) {
        $this->em = $em;
        $this->hasher = $hasher;
    }

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
            $plainTextPassword = $user->getPassword();

            $user->setPassword($this->hasher->hashPassword($user, $plainTextPassword));

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/user/login', name: 'user_login', methods:['GET', 'POST'])]
    public function login(Request $request, AuthenticationUtils $authUtils): Response {
        if($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('home');
        }

        $error = $authUtils->getLastAuthenticationError();
        $form = $this->createForm(LoginFormType::class);

        $lastUsername = $authUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    #[Route('/user/profile/{user}', name: 'user_profile', methods: ['GET'])]
    #[IsGranted('ROLE_STAFF')]
    public function profile(User $user = null) : Response
    {
        if($user == null) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user
        ]);
    }
}
