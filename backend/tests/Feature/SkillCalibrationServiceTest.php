<?php

namespace Tests\Feature;

use App\Services\SkillCalibrationService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SkillCalibrationServiceTest extends TestCase
{
    private SkillCalibrationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new SkillCalibrationService();
    }

    public function test_neutral_skill_produces_zero_theta(): void
    {
        $result = $this->service->calibrate(0.5);

        $this->assertSame(0.0, $result);
    }

    public function test_high_skill_produces_positive_theta(): void
    {
        $result = $this->service->calibrate(0.7);

        $this->assertSame(0.847298, $result);
    }

    public function test_low_skill_produces_negative_theta(): void
    {
        $result = $this->service->calibrate(0.3);

        $this->assertSame(-0.847298, $result);
    }

    public function test_zero_skill_is_clamped_safely(): void
    {
        $result = $this->service->calibrate(0.0);

        $this->assertSame(-6.906755, $result);
    }

    public function test_perfect_skill_is_clamped_safely(): void
    {
        $result = $this->service->calibrate(1.0);

        $this->assertSame(6.906755, $result);
    }

    public function test_skill_score_outside_range_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->calibrate(1.5);
    }
}
