<?php

namespace App\Services;

use InvalidArgumentException;

class SkillCalibrationService
{
    private const EPSILON = 0.001;

    public function calibrate(float $skillScore): float
    {
        if ($skillScore < 0 || $skillScore > 1) {
            throw new InvalidArgumentException(
                'Skill score must be between 0 and 1.'
            );
        }

        $probability = max(
            self::EPSILON,
            min(1 - self::EPSILON, $skillScore)
        );

        $theta = log($probability / (1 - $probability));

        return round($theta, 6);
    }
}
