<?php

namespace App\Repositories;

use App\Models\Award;
use Illuminate\Database\Eloquent\Collection;

class AwardRepository
{
    public function getAll(): Collection
    {
        return Award::with(['user', 'scholarship', 'disbursements'])->get();
    }

    public function findById(int $id): ?Award
    {
        return Award::with(['user', 'scholarship', 'disbursements.costCategory'])->find($id);
    }

    public function getUserAwards(int $userId): Collection
    {
        return Award::with(['scholarship', 'disbursements'])
            ->where('user_id', $userId)
            ->get();
    }

    public function create(array $data): Award
    {
        return Award::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $award = $this->findById($id);
        return $award ? $award->update($data) : false;
    }

    public function getAwardDisbursements(int $awardId): Collection
    {
        return Award::find($awardId)->disbursements()->with('costCategory')->get();
    }
}