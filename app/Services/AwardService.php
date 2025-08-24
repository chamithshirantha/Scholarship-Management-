<?php

namespace App\Services;

use App\Repositories\AwardRepository;
use App\Models\Award;
use Illuminate\Database\Eloquent\Collection;
use Exception;
use Illuminate\Support\Facades\Log;

class AwardService
{
    protected $awardRepository;

    public function __construct(AwardRepository $awardRepository)
    {
        $this->awardRepository = $awardRepository;
    }

    public function getUserAwards(int $userId): Collection
    {
        try {
            return $this->awardRepository->getUserAwards($userId);
        } catch (Exception $e) {
            Log::error('Error getting user awards: ' . $e->getMessage());
            throw new Exception('Failed to retrieve awards');
        }
    }

    public function getAwardById(int $id): ?Award
    {
        try {
            return $this->awardRepository->findById($id);
        } catch (Exception $e) {
            Log::error('Error getting award by ID: ' . $e->getMessage());
            throw new Exception('Failed to retrieve award');
        }
    }

    public function getAwardDisbursements(int $awardId): Collection
    {
        try {
            return $this->awardRepository->getAwardDisbursements($awardId);
        } catch (Exception $e) {
            Log::error('Error getting award disbursements: ' . $e->getMessage());
            throw new Exception('Failed to retrieve disbursements');
        }
    }

    public function createAward(array $data): Award
    {
        try {
            return $this->awardRepository->create($data);
        } catch (Exception $e) {
            Log::error('Error creating award: ' . $e->getMessage());
            throw new Exception('Failed to create award');
        }
    }
}