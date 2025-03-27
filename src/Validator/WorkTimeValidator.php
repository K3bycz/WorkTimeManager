<?php
namespace App\Validator;

use App\Entity\Employee;
use App\Repository\WorkTimeRepository;
use DateTime;

class WorkTimeValidator
{
    private WorkTimeRepository $workTimeRepository;
    
    public function __construct(WorkTimeRepository $workTimeRepository)
    {
        $this->workTimeRepository = $workTimeRepository;
    }
    
    /**
     * 
     * @return array
     */
    public function validate(array $data): array
    {
        $errors = [];
        
        return $errors;
    }
    
    /**
     * 
     * @return array
     */
    public function validateDetails(Employee $employee, DateTime $startDateTime, DateTime $endDateTime): array
    {
        $errors = [];
        
        if ($endDateTime <= $startDateTime) {
            $errors[] = 'Data zakończenia musi być późniejsza niż data rozpoczęcia.';
        }
        
        $startDay = clone $startDateTime;
        $startDay->setTime(0, 0, 0);
        
        if ($this->workTimeRepository->checkWorkTimeForDay($employee, $startDay)) {
            $errors[] = 'Pracownik może posiadać tylko jeden przedział czasu pracy w danym dniu.';
        }
        
        $interval = $startDateTime->diff($endDateTime);
        $hoursWorked = $interval->h + ($interval->days * 24);
        
        if ($hoursWorked > 12) {
            $errors[] = 'Czas pracy nie może przekraczać 12 godzin w jednym przedziale.';
        }
        
        return $errors;
    }
}