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
    
    public function __construct(
        WorkTimeRepository $workTimeRepository,
        EmployeeRepository $employeeRepository,
        WorkTimeValidator $workTimeValidator
    ) {
        $this->workTimeRepository = $workTimeRepository;
        $this->employeeRepository = $employeeRepository;
        $this->workTimeValidator = $workTimeValidator;
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
}