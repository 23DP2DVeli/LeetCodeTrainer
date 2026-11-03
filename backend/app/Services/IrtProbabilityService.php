<?php

namespace App\Services;

use InvalidArgumentException;

class IrtProbabilityService
{
    /**
     * Calculate the probability of solving a problem
     * using the two-parameter logistic IRT model.
     *
     * P = 1 / (1 + e^(-a(theta - b)))
     */
    public function calculate(
        float $theta,
        float $difficulty,
        float $discrimination = 1.0
    ): float {
        if ($discrimination <= 0) {
            throw new InvalidArgumentException(
                'Discrimination must be greater than zero.'
            );
        }

        $exponent = $discrimination * ($theta - $difficulty);

        // Prevent numerical overflow for extreme values.
        $exponent = max(-700, min(700, $exponent));

        $probability = 1 / (1 + exp(-$exponent));

        return round($probability, 6);
    }
}
