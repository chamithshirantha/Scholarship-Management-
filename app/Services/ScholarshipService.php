<?php

namespace App\Services;

use App\Repositories\ScholarshipRepository;
use App\Models\Scholarship;
use Illuminate\Database\Eloquent\Collection;
use Exception;
use Illuminate\Support\Facades\Log;

class ScholarshipService
{
    protected $scholarshipRepository;

    public function __construct(ScholarshipRepository $scholarshipRepository)
    {
        $this->scholarshipRepository = $scholarshipRepository;
    }

    public function getActiveScholarships(): Collection
    {
        try {
            return $this->scholarshipRepository->getActiveScholarships();
        } catch (Exception $e) {
            Log::error('Error getting active scholarships: ' . $e->getMessage());
            throw new Exception('Failed to retrieve scholarships');
        }
    }

    public function getScholarshipById(int $id): ?Scholarship
    {
        try {
            return $this->scholarshipRepository->findById($id);
        } catch (Exception $e) {
            Log::error('Error getting scholarship by ID: ' . $e->getMessage());
            throw new Exception('Failed to retrieve scholarship');
        }
    }

    public function createScholarship(array $data): Scholarship
    {
        try {
            $data['created_by'] = auth()->id();
            return $this->scholarshipRepository->create($data);
        } catch (Exception $e) {
            Log::error('Error creating scholarship: ' . $e->getMessage());
            throw new Exception('Failed to create scholarship');
        }
    }

    public function updateScholarship(int $id, array $data): bool
    {
        try {
            return $this->scholarshipRepository->update($id, $data);
        } catch (Exception $e) {
            Log::error('Error updating scholarship: ' . $e->getMessage());
            throw new Exception('Failed to update scholarship');
        }
    }

    public function deleteScholarship(int $id): bool
    {
        try {
            return $this->scholarshipRepository->delete($id);
        } catch (Exception $e) {
            Log::error('Error deleting scholarship: ' . $e->getMessage());
            throw new Exception('Failed to delete scholarship');
        }
    }

    public function getScholarshipWithBudgets(int $id): ?Scholarship
    {
        try {
            return $this->scholarshipRepository->getWithBudgets($id);
        } catch (Exception $e) {
            Log::error('Error getting scholarship with budgets: ' . $e->getMessage());
            throw new Exception('Failed to retrieve scholarship details');
        }
    }
}