<?php

namespace App\CustomLogic;

use App\Entity\Submission;

class DailyDistanceExtraPoints implements ExtraPoints
{
    private array $users = [];
    private array $max = [];
    private int $maxUser = -1;
    private ?\DateTimeInterface $lastDate = null;

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

        $this->maxUser = -1;
        $this->users = [];
        $this->lastDate = $date;
    }

    public function accumulate(Submission $submission): void
    {
        $distance = $submission->getDistance();
        $user = $submission->getUser()->getId();
        $date = $submission->getDate();

        if ($date !== $this->lastDate && !empty($this->users)) {
            $this->process($date);
        }

        if (!array_key_exists($user, $this->users)) {
            $this->users[$user] = ['faculty' => $submission->getFaculty()->getId(), 'distance' => 0];
        }

        $this->users[$user]['distance'] += $distance;

        if ($this->maxUser === -1 || $this->users[$user] > $this->users[$this->maxUser]) {
            $this->maxUser = $user;
        }
    }

    public function finalize(): void
    {
        if (!empty($this->users)) {
            $this->max[] = [
                'user' => $this->maxUser,
                'faculty' => $this->users[$this->maxUser]['faculty'],
                'distance' => $this->users[$this->maxUser]['distance']
            ];
        }
    }

    public function getWinners(): array
    {
        if (empty($this->max)) {
            return [];
        }

        $maxDistance = 0;
        $maxUser = -1;
        $maxFaculty = -1;

        foreach ($this->max as $days) {
            if ($days['distance'] > $maxDistance) {
                $maxUser = $days['user'];
                $maxDistance = $days['distance'];
                $maxFaculty = $days['faculty'];
            }
        }

        return [
            'name' => 'daily_distance',
            'user_id' => $maxUser,
            'distance' => $maxDistance,
            'faculty' => $maxFaculty,
            'reward' => self::reward(),
        ];
    }
}