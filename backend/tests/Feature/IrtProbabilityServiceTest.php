<?php

namespace Tests\Feature;

use App\Services\IrtProbabilityService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class IrtProbabilityServiceTest extends TestCase
{
    private IrtProbabilityService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new IrtProbabilityService();
    }

    public function test_equal_skill_and_difficulty_produce_fifty_percent(): void
    {
        $result = $this->service->calculate(0.0, 0.0);

        $this->assertSame(0.5, $result);
    }

    public function test_higher_skill_increases_probability(): void
    {
        $result = $this->service->calculate(1.0, 0.0);

        $this->assertSame(0.731059, $result);
    }

    public function test_higher_difficulty_decreases_probability(): void
    {
        $result = $this->service->calculate(0.0, 1.0);

        $this->assertSame(0.268941, $result);
    }

    public function test_discrimination_controls_sensitivity(): void
    {
        $normal = $this->service->calculate(1.0, 0.0, 1.0);
        $high = $this->service->calculate(1.0, 0.0, 2.0);

        $this->assertSame(0.731059, $normal);
        $this->assertSame(0.880797, $high);
    }

    public function test_discrimination_must_be_positive(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->calculate(1.0, 0.0, 0.0);
    }
}
