<?php

namespace App\Controller;

use App\Entity\Charity;
use App\Entity\Season;
use App\Entity\User;
use App\Repository\CharityRepository;
use App\Requests\CharityEditRequest;
use App\Requests\SeasonCreateRequest;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_STAFF')]
class ManagementController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Route('api/management/users/admin', name: 'management_users_admin', methods: ['POST'])]
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

        return $this->json(['success' => true]);
    }

    #[Route('api/management/users/ban', name: 'management_users_ban', methods:['POST'])]
    public function user_ban(Request $request): Response 
    {
        $id = $request->get('user_id');

        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->find($id);

        if($user === null) {
          return $this->json([
            'success' => false,
            'message' => 'User not found'
          ]);
        }

        $ban = !$user->isBanned();
        $user->setBanned($ban);
        $this->em->flush();

        return $this->json(['success' => true]);
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
    public function userList(): Response
    {
        return $this->json($users = $this->em->getRepository(User::class)->findAll());
    }

    #[Route('/api/management/season/new', name: 'api_management_season_new', methods: ['POST'])]
    public function seasonCreate(SeasonCreateRequest $request): Response
    {
        $charity = new Charity();
        $charity->setName($request->getCharityName());
        $charity->setDescription($request->getCharityDescription());

        $season = new Season();
        $season->setStart($request->getBeginDate());
        $season->setCharity($charity);

        $this->em->persist($charity);
        $this->em->persist($season);

        $this->em->flush();

        return $this->json(
            ['success' => 1, 'id' => $season->getId()]
        );
    }

    #[Route('/management/seasons', name: 'management_seasons')]
    public function seasons(): Response 
    {
        return $this->render('management/seasons.html.twig', []);
    }

    #[Route('/management/season/{season}', name: 'management_season')]
    public function season(): Response 
    {
        return $this->render('management/season.html.twig', []);
    }


    #[Route('/api/management/charity/edit/{charity}', name:'management_edit_charity', methods: ['POST'])]
    public function editCharity(CharityEditRequest $request, CharityRepository $charityRepository): Response
    {
      $charity = $request->getCharity();
      
      if($request->getCharityName() != null) {
        $charity->setName($request->getCharityName());
      }
      
      if($request->getCharityDescription() != null) {
        $charity->setDescription($request->getCharityDescription());
      }
      
      $charityRepository->save($charity, true);
      
      return $this->json(
        ['success' => true]
      );
    }
}
