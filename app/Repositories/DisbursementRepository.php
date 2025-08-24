<?php

namespace App\Repositories;

use App\Models\Disbursement;
use Illuminate\Database\Eloquent\Collection;

class DisbursementRepository
{
    public function getAll(): Collection
    {
        return Disbursement::with(['award.user', 'costCategory', 'receipt'])->get();
    }

    public function findById(int $id): ?Disbursement
    {
        return Disbursement::with(['award.user', 'costCategory', 'receipt'])->find($id);
    }

    public function create(array $data): Disbursement
    {
        return Disbursement::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $disbursement = $this->findById($id);
        return $disbursement ? $disbursement->update($data) : false;
    }

    public function markAsPaid(int $id, array $paymentDetails): bool
    {
        $disbursement = $this->findById($id);
        if ($disbursement) {
            return $disbursement->update([
                'status' => 'paid',
                'paid_date' => now(),
                'payment_details' => $paymentDetails,
                'processed_by' => auth()->id()
            ]);
        }
        return false;
    }

    public function getByStatus(string $status): Collection
    {
        return Disbursement::with(['award.user', 'costCategory'])
            ->where('status', $status)
            ->get();
    }

    
    

    

    
}