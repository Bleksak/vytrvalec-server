<?php

namespace App\CustomLogic;

use App\Entity\Submission;

class WeeklyDistanceExtraPoints implements ExtraPoints
{
    private array $users = [];

    public static function acceptsWeek(int $week): bool
    {
        return $week === 2;
    }

    public static function reward(): int
    {
        return 1;
    }

    public function requiresActivity(): bool
    {
        return false;
    }

    public function accumulate(Submission $submission): void
    {
        $user = $submission->getUser()->getId();
        $distance = $submission->getDistance();

        if(!array_key_exists($user, $this->users)) {
            $this->users[$user] = ['faculty' => $submission->getFaculty()->getId(), 'distance' => 0];
        }

        $this->users[$user]['distance'] += $distance;
    }

    public function finalize(): void
    {
    }

    public function getWinners(): array
    {
        if(empty($this->users)) {
            return [];
        }

        $maxDistance = 0;
        $maxUser = -1;
        $maxFaculty = -1;

        foreach($this->users as $user => $values) {
            $distance = $values['distance'];

            if($distance > $maxDistance) {
                $maxUser = $user;
                $maxDistance = $distance;
                $maxFaculty = $values['faculty'];
            }
        }

        return [
            'name' => 'weekly_distance',
            'user_id' => $maxUser,
            'distance' => $maxDistance,
            'faculty' => $maxFaculty,
            'reward' => self::reward(),
        ];
    }
}