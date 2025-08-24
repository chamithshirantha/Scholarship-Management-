<?php

namespace App\Services;

use App\Repositories\DisbursementRepository;
use App\Models\Disbursement;
use Illuminate\Database\Eloquent\Collection;
use Exception;
use Illuminate\Support\Facades\Log;

class DisbursementService
{
    protected $disbursementRepository;

    public function __construct(DisbursementRepository $disbursementRepository)
    {
        $this->disbursementRepository = $disbursementRepository;
    }

    public function getDisbursementDetails(int $id): ?Disbursement
    {
        try {
            return $this->disbursementRepository->findById($id);
        } catch (Exception $e) {
            Log::error('Error getting disbursement details: ' . $e->getMessage());
            throw new Exception('Failed to retrieve disbursement');
        }
    }

    public function markAsPaid(int $id, array $paymentDetails): bool
    {
        try {
            return $this->disbursementRepository->markAsPaid($id, $paymentDetails);
        } catch (Exception $e) {
            Log::error('Error marking disbursement as paid: ' . $e->getMessage());
            throw new Exception('Failed to update disbursement status');
        }
    }

    public function getDisbursementsByStatus(string $status): Collection
    {
        try {
            return $this->disbursementRepository->getByStatus($status);
        } catch (Exception $e) {
            Log::error('Error getting disbursements by status: ' . $e->getMessage());
            throw new Exception('Failed to retrieve disbursements');
        }
    }


    public function getAllDisbursements(): Collection
    {
        try {
            return $this->disbursementRepository->getAll();
        } catch (Exception $e) {
            Log::error('Error getting all disbursements: ' . $e->getMessage());
            throw new Exception('Failed to retrieve disbursements');
        }
    }
}