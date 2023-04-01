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
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        // $user = new User();
        $form = $this->createForm(LoginFormType::class);
        // $form->handleRequest($request);

        $lastUsername = $authUtils->getLastUsername();

        // if($form->isSubmitted() && $form->isValid()) {
            // $email = $user->getEmail();
            // $plainPassword = $user->getPassword();
            // $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

            // if($user == null || !$this->hasher->isPasswordValid($user, $plainPassword)) {
            //     $error = $translator->trans('bad_login');
            // } else {
            //     $security->login($user);
            //     return $this->redirectToRoute('home');
            // }

            // set session
            // redirect
        // }

        return $this->render('user/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }
}
