<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Uid\Uuid;
use DateTime;
use App\Entity\WorkTime;
use App\Repository\EmployeeRepository;
use App\Repository\WorkTimeRepository;
use App\Validator\WorkTimeValidator;

class WorkTimeService
{
    private WorkTimeRepository $workTimeRepository;
    private EmployeeRepository $employeeRepository;
    private WorkTimeValidator $workTimeValidator;
    private WorkTimeConfigService $configService;
    
    public function __construct(
        WorkTimeRepository $workTimeRepository,
        EmployeeRepository $employeeRepository,
        WorkTimeValidator $workTimeValidator,
        WorkTimeConfigService $configService,
    ) {
        $this->workTimeRepository = $workTimeRepository;
        $this->employeeRepository = $employeeRepository;
        $this->workTimeValidator = $workTimeValidator;
        $this->configService = $configService;
    }
    
    /**
     * @throws BadRequestException
     */
    public function registerWorkTime(string $employeeId, DateTime $startDateTime, DateTime $endDateTime): void
    {
        $employee = $this->employeeRepository->find(Uuid::fromString($employeeId));
        if (!$employee) {
            throw new BadRequestException('Pracownik o podanym ID nie istnieje.');
        }
        
        $errors = $this->workTimeValidator->validateDetails($employee, $startDateTime, $endDateTime);
        if (!empty($errors)) {
            throw new BadRequestException(implode(' ', $errors));
        }
        
        $workTime = new WorkTime();
        $workTime->setEmployee($employee);
        $workTime->setStartDateTime($startDateTime);
        $workTime->setEndDateTime($endDateTime);
        
        $this->workTimeRepository->saveWorkTime($workTime);
    }

    /**
     * @throws BadRequestException
     */
    public function generateSummary(string $employeeId, string $dateString): array
    {
        $employee = $this->employeeRepository->find(Uuid::fromString($employeeId));
        if (!$employee) {
            throw new BadRequestException('Pracownik o podanym ID nie istnieje.');
        }
        
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            $date = new DateTime($dateString);
            $workTimeRecords = $this->workTimeRepository->findByEmployeeAndDay($employee, $date);
            $summaryType = 'day';
        } elseif (preg_match('/^\d{4}-\d{2}$/', $dateString)) {
            $date = new DateTime($dateString . '-01');
            $workTimeRecords = $this->workTimeRepository->findByEmployeeAndMonth($employee, $date);
            $summaryType = 'month';
        } else {
            throw new BadRequestException('Nieprawidłowy format daty. Dozwolone formaty: YYYY-MM-DD lub YYYY-MM.');
        }
        
        if (empty($workTimeRecords)) {
            return [
                'employeeId' => $employeeId,
                'date' => $dateString,
                'summaryType' => $summaryType,
                'totalHours' => 0,
                'standardHours' => 0,
                'overtimeHours' => 0,
                'standardRate' => $this->configService->getHourlyRate(),
                'overtimeRate' => $this->configService->getOvertimeRate(),
                'standardPay' => 0,
                'overtimePay' => 0,
                'totalPay' => 0
            ];
        }
        
        $totalMinutes = 0;
        
        foreach ($workTimeRecords as $workTime) {
            $minutes = $this->calculateRoundedMinutes($workTime);
            $totalMinutes += $minutes;
        }
        
        $totalHours = $totalMinutes / 60;
        
        $monthlyHoursNorm = $this->configService->getMonthlyHoursNorm();
        $standardHours = min($totalHours, $monthlyHoursNorm);
        $overtimeHours = max(0, $totalHours - $monthlyHoursNorm);
        
        $standardRate = $this->configService->getHourlyRate();
        $overtimeRate = $this->configService->getOvertimeRate();
        
        $standardPay = $standardHours * $standardRate;
        $overtimePay = $overtimeHours * $overtimeRate;
        $totalPay = $standardPay + $overtimePay;
        
        return [
            'employeeId' => $employeeId,
            'date' => $dateString,
            'summaryType' => $summaryType,
            'totalHours' => round($totalHours, 2),
            'standardHours' => round($standardHours, 2),
            'overtimeHours' => round($overtimeHours, 2),
            'standardRate' => $standardRate,
            'overtimeRate' => $overtimeRate,
            'standardPay' => round($standardPay, 2),
            'overtimePay' => round($overtimePay, 2),
            'totalPay' => round($totalPay, 2)
        ];
    }

    private function calculateRoundedMinutes(WorkTime $workTime): int
    {
        $start = $workTime->getStartDateTime();
        $end = $workTime->getEndDateTime();
        
        $diffInSeconds = $end->getTimestamp() - $start->getTimestamp();
        $diffInMinutes = $diffInSeconds / 60;
        
        $remainder = $diffInMinutes % 30;
        
        if ($remainder < 15) {
            return $diffInMinutes - $remainder;
        } else {
            return $diffInMinutes + (30 - $remainder);
        }
    }
}