<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProfileCacheRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubmissionRepository;
use App\Services\ImagePath;
use App\Utils\FeatureFlag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\LocaleSwitcher;

#[Route('/profile', name: self::ROUTE, methods: [Request::METHOD_GET])]
#[IsGranted(FeatureFlag::ROLE_USER->value)]
final class ProfileController extends AbstractController
{
    public const string ROUTE = 'user:profile';

    public function __construct(
        private readonly SeasonRepository $seasonRepository,
        private readonly ProfileCacheRepository $profileCacheRepository,
        private readonly SubmissionRepository $submissionRepository,
        private readonly LocaleSwitcher $localeSwitcher,
    ) {}

    public function __invoke(
        #[CurrentUser] User $user,
        ImagePath $imagePath,
    ): Response {
        $currentSeason = $this->seasonRepository->findCurrentSeason();

        // NOTE: This fetches all activities, so we don't need to re-fetch them later
        $profileCache = $this->profileCacheRepository->findAllByUserWithActivities(
            $user,
            $this->localeSwitcher->getLocale(),
        );

        $seasonMap = [];
        $submissions = $this->submissionRepository->findAllByUser($user);

        foreach ($submissions as $submission) {
            if (!isset($seasonMap[$submission->season->id])) {
                $seasonMap[$submission->season->id] = [];
            }

            $seasonMap[$submission->season->id][$submission->id] = $submission;
        }

        $seasons = $this->seasonRepository->findAllVisible();

        return $this->render('user/profile.html.twig', [
            'current_season' => $currentSeason,
            'profile_cache' => $profileCache,
            'season_list' => $seasonMap,
            'seasons' => $seasons,
            'image_path' => $imagePath,
            'submissions' => $submissions,
        ]);
    }
}
