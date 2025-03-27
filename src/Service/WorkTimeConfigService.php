<?php
namespace App\Service;

class WorkTimeConfigService{

    private int $monthlyHoursNorm;
    private float $hourlyRate;
    private float $overtimeRateMultiplier;

    public function __construct(int $monthlyHoursNorm, float $hourlyRate, float $overtimeRateMultiplier)
    {
        $this->monthlyHoursNorm = $monthlyHoursNorm;
        $this->hourlyRate = $hourlyRate;
        $this->overtimeRateMultiplier = $overtimeRateMultiplier;
    }

    public function getMonthlyHoursNorm(): int
    {
        return $this->monthlyHoursNorm;
    }

    public function getHourlyRate(): float
    {
        return $this->hourlyRate;
    }

    public function getOvertimeRateMultiplier(): float
    {
        return $this->overtimeRateMultiplier;
    }

    public function getOvertimeRate(): float
    {
        $overtimeRate = $this->hourlyRate * $this->overtimeRateMultiplier;
        
        return $overtimeRate;
    }
    
}