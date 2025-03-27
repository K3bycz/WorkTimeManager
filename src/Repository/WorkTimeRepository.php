<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;
use App\Entity\WorkTime;
use App\Entity\Employee;

/**
 * @extends ServiceEntityRepository<WorkTime>
 */
class WorkTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkTime::class);
    }

    public function checkWorkTimeForDay(Employee $employee, DateTime $startDay): bool
    {
        $result = $this->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->where('w.employee = :employee')
            ->andWhere('w.startDay = :startDay')
            ->setParameter('employee', $employee)
            ->setParameter('startDay', $startDay)
            ->getQuery()
            ->getSingleScalarResult();
            
        return (int)$result > 0;
    }

    public function saveWorkTime(WorkTime $workTime, bool $flush = true): void
    {
        $this->getEntityManager()->persist($workTime);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByEmployeeAndDay(Employee $employee, DateTime $date): array
    {
        $startDay = clone $date;
        $startDay->setTime(0, 0, 0);
        
        return $this->createQueryBuilder('w')
            ->where('w.employee = :employee')
            ->andWhere('w.startDay = :startDay')
            ->setParameter('employee', $employee)
            ->setParameter('startDay', $startDay)
            ->getQuery()
            ->getResult();
    }

    public function findByEmployeeAndMonth(Employee $employee, DateTime $date): array
    {
        $startOfMonth = clone $date;
        $startOfMonth->setDate($date->format('Y'), $date->format('m'), 1)->setTime(0, 0, 0);
        
        $endOfMonth = clone $date;
        $endOfMonth->setDate($date->format('Y'), $date->format('m'), $date->format('t'))->setTime(23, 59, 59);
        
        return $this->createQueryBuilder('w')
            ->where('w.employee = :employee')
            ->andWhere('w.startDateTime >= :startOfMonth')
            ->andWhere('w.startDateTime <= :endOfMonth')
            ->setParameter('employee', $employee)
            ->setParameter('startOfMonth', $startOfMonth)
            ->setParameter('endOfMonth', $endOfMonth)
            ->orderBy('w.startDateTime', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

