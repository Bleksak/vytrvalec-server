<?php

namespace App\CustomLogic;

use App\Entity\Activity;
use App\Entity\Submission;

class WeeklyElevationExtraPoints implements ExtraPoints
{
    private ?Activity $activity = null;
    private array $users = [];
    private bool $eligible = false;

    public static function getUniqueName(): string
    {
        return 'weekly_elevation';
    }

    public static function acceptsWeek(int $week): bool
    {
        return $week === 3;
    }

    public function requiresActivity(): bool
    {
        return true;
    }

    public function setActivity(Activity $activity): void
    {
        $this->activity = $activity;
    }

    public static function reward(): int
    {
        return 1;
    }

    public function accumulate(Submission $submission): void
    {
        $user = $submission->getUser()->getId();
        $elevation = $submission->getElevation();

        if(!array_key_exists($user, $this->users)) {
            $this->users[$user] = ['faculty' => $submission->getFaculty()->getId(), 'elevation' => 0];
        }

        $this->users[$user]['elevation'] += $elevation;

        if($this->users[$user]['elevation'] >= $this->activity->getMinElevation()) {
            $this->eligible = true;
        }
    }

    public function finalize(): void
    {
    }

    public function getWinners(): array
    {
        if(!$this->eligible) {
            return [];
        }

        $maxElevation = 0;
        $maxUser = -1;
        $maxFaculty = -1;

        foreach($this->users as $user => $values) {
            $distance = $values['elevation'];

            if($distance > $maxElevation) {
                $maxUser = $user;
                $maxElevation = $distance;
                $maxFaculty = $values['faculty'];
            }
        }

        return [
            'name' => self::getUniqueName(),
            'user_id' => $maxUser,
            'elevation' => $maxElevation,
            'faculty' => $maxFaculty,
            'reward' => self::reward(),
        ];
    }
}