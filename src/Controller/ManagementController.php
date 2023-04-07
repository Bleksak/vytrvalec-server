<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[IsGranted('ROLE_STAFF')]
class ManagementController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/management/users/admin', name: 'management_users_admin', methods: ['POST'])]
    public function user_admin(Request $request): Response 
    {
        $id = $request->get('user_id');

        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->find($id);

        if($user === null) {
            throw $this->createNotFoundException("User not found");
        }

        $roles = $user->getRoles();
        $index = array_search("ROLE_STAFF", $roles);

        if($index === false) {
            $roles[] = "ROLE_STAFF";
        } else {
            unset($roles[$index]);
        }

        $user->setRoles($roles);
        $this->em->flush();

        return $this->json('');
    }

    #[Route('/management/users/ban', name: 'management_users_ban', methods:['POST'])]
    public function user_ban(Request $request): Response 
    {
        $id = $request->get('user_id');

        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->find($id);

        if($user === null) {
            throw $this->createNotFoundException("User not found");
        }

        $ban = !$user->isBanned();
        $user->setBanned($ban);
        $this->em->flush();

        // if($ban) {
        //     $this->addFlash('notice', 'management_user_banned');
        // } else {
        //     $this->addFlash('notice', 'management_user_unbanned');
        // }

        return $this->json('');
    }

    #[Route('/management/users', name: 'management_users')]
    public function users(): Response
    {
        $users = $this->em->getRepository(User::class)->findAll();

        return $this->render('management/users.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/api/management/users', name: 'api_management_users', methods: ['GET'])]
    public function users_list(SerializerInterface $serializer): Response
    {
            
        $users = $this->em->getRepository(User::class)->findAll();
        $data = $serializer->serialize($users, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/management/season', name: 'management_season')]
    public function season(): Response 
    {
        return $this->render('management/season.html.twig', []);
    }
}
