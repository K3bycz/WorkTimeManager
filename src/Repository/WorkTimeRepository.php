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

}

