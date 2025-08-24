<?php

namespace App\Services;

use App\Repositories\ApplicationRepository;
use App\Models\Application;
use Illuminate\Database\Eloquent\Collection;
use Exception;
use Illuminate\Support\Facades\Log;

class ApplicationService
{
    protected $applicationRepository;

    public function __construct(ApplicationRepository $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
    }

    public function createApplication(int $userId, array $data): Application
    {
        try {
            $data['user_id'] = $userId;
            return $this->applicationRepository->create($data);
        } catch (Exception $e) {
            Log::error('Error creating application: ' . $e->getMessage());
            throw new Exception('Failed to create application');
        }
    }

    public function getUserApplications(int $userId): Collection
    {
        try {
            return $this->applicationRepository->getUserApplications($userId);
        } catch (Exception $e) {
            Log::error('Error getting user applications: ' . $e->getMessage());
            throw new Exception('Failed to retrieve applications');
        }
    }

    public function getApplicationById(int $id): ?Application
    {
        try {
            return $this->applicationRepository->findById($id);
        } catch (Exception $e) {
            Log::error('Error getting application by ID: ' . $e->getMessage());
            throw new Exception('Failed to retrieve application');
        }
    }

    public function getAllApplications(?string $status = null, ?int $scholarshipId = null): Collection
    {
        try {
            if ($status) {
                return $this->applicationRepository->getApplicationsByStatus($status);
            }
            return $this->applicationRepository->getAll();
        } catch (Exception $e) {
            Log::error('Error getting all applications: ' . $e->getMessage());
            throw new Exception('Failed to retrieve applications');
        }
    }

    public function getApplicationLogs(int $applicationId): Collection
    {
        try {
            return $this->applicationRepository->getApplicationLogs($applicationId);
        } catch (Exception $e) {
            Log::error('Error getting application logs: ' . $e->getMessage());
            throw new Exception('Failed to retrieve application logs');
        }
    }

    public function reviewApplication(int $id, string $status, string $comments, int $reviewerId): Application
    {
        try {
            $application = $this->applicationRepository->findById($id);
            if (!$application) {
                throw new Exception('Application not found');
            }

            $updateData = [
                'status' => $status,
                'review_notes' => $comments,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now()
            ];

            $this->applicationRepository->update($id, $updateData);

            // Create application log
            $this->createApplicationLog(
                $id,
                'status_update',
                "Application status changed to {$status}",
                $reviewerId
            );

            return $this->applicationRepository->findById($id);
        } catch (Exception $e) {
            Log::error('Error reviewing application: ' . $e->getMessage());
            throw new Exception('Failed to review application');
        }
    }

    private function createApplicationLog(int $applicationId, string $action, string $description, int $performerId): void
    {
        // Implementation for creating application logs
    }
}