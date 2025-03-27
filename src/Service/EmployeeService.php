<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;
use App\Entity\Employee;

class EmployeeService
{
    private EntityManagerInterface $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function createEmployee(string $firstName, string $surname): Employee
    {
        $employee = new Employee();
        $employee->setFirstName($firstName);
        $employee->setSurname($surname);
        
        $this->entityManager->persist($employee);
        $this->entityManager->flush();
        
        return $employee;
    }
}