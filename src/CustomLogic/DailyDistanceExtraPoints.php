<?php

namespace App\CustomLogic;

use App\Entity\Submission;

class DailyDistanceExtraPoints implements ExtraPoints
{
    private array $users = [];
    private array $max = [];
    private ?\DateTimeInterface $lastDate = null;

    public static function getUniqueName(): string
    {
        return 'daily_distance';
    }

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

    private function process(\DateTimeInterface $date): void
    {
        $this->finalize();

        $this->users = [];
        $this->lastDate = $date;
    }

    public function accumulate(Submission $submission): void
    {
        $distance = $submission->getDistance();
        $user = $submission->getUser()->getId();
        $date = $submission->getDate();

        if ($this->lastDate === null || $date->diff($this->lastDate)->days !== 0) {
            $this->process($date);
        }

        if (!array_key_exists($user, $this->users)) {
            $this->users[$user] = ['faculty' => $submission->getFaculty()->getId(), 'distance' => 0];
        }

        $this->users[$user]['distance'] += $distance;
    }

    public function finalize(): void
    {
        if (!empty($this->users)) {
            $maxUsers = [];
            $maxDistance = 0;

            foreach($this->users as $user => $values) {

                if($values['distance'] === $maxDistance) {
                    $maxUsers[$user] = $values['faculty'];
                }

                if($values['distance'] > $maxDistance) {
                    $maxDistance = $values['distance'];
                    $maxUsers = [$user => $values['faculty']];
                }
            }

            $this->max[] = [
                'users' => $maxUsers,
                'distance' => $maxDistance
            ];
        }
    }

    public function getWinners(): array
    {
        if (empty($this->max)) {
            return [];
        }

        $maxDistance = 0;
        $maxUsers = [];

        foreach ($this->max as $day) {
            if($day['distance'] === $maxDistance) {
                $maxUsers = array_merge($maxUsers, $day['users']);
            }

            if ($day['distance'] > $maxDistance) {
                $maxUsers = $day['users'];
                $maxDistance = $day['distance'];
            }
        }

        return [
            'name' => self::getUniqueName(),
            'users' => $maxUsers,
            'value' => $maxDistance,
            'reward' => self::reward(),
        ];
    }

}