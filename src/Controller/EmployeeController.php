<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Employee;
use App\Service\EmployeeService;
use App\Validator\EmployeeValidator;

#[Route('/api/employee')]
class EmployeeController extends AbstractController
{
    #[Route('/create', name: 'app_employee_create', methods: ['POST'])]
    public function create(
        Request $request, 
        EmployeeService $employeeService,
        EmployeeValidator $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];
        
        if (!isset($data['firstName']) || !isset($data['surname'])) {
            return $this->json([
                'success' => false,
                'error' => 'Błąd: brakuje wymaganych danych (imie, nazwisko)'
            ], 400);
        }
        
        $errors = $validator->validate($data);
        
        if (!empty($errors)) {
            return $this->json([
                'success' => false,
                'errors' => $errors
            ], 400);
        }
        
        $employee = $employeeService->createEmployee(
            $data['firstName'],
            $data['surname']
        );
        
        return $this->json([
            'id' => $employee->getId()->__toString()
        ], 201);
    }
}