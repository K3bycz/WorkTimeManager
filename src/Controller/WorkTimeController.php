<?php
namespace App\Controller;

use App\Service\WorkTimeService;
use App\Validator\WorkTimeValidator;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/work-time')]
class WorkTimeController extends AbstractController
{
    #[Route('/register', name: 'app_work_time_register', methods: ['POST'])]
    public function register(
        Request $request,
        WorkTimeService $workTimeService,
        WorkTimeValidator $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];
        
        if (!isset($data['employeeId']) || !isset($data['startDateTime']) || !isset($data['endDateTime'])) {
            return $this->json([
                'success' => false,
                'error' => 'Błąd: brakuje wymaganych danych (employeeId, startDateTime, endDateTime)'
            ], 400);
        }
        
        try {
            $employeeId = $data['employeeId'];
            $startDateTime = new DateTime($data['startDateTime']);
            $endDateTime = new DateTime($data['endDateTime']);
            
            $workTimeService->registerWorkTime($employeeId, $startDateTime, $endDateTime);
            
            return $this->json([
                'success' => true,
                'message' => 'Czas pracy został dodany!'
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}