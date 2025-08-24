<?php

namespace App\Repositories;

use App\Models\Scholarship;
use Illuminate\Database\Eloquent\Collection;

class ScholarshipRepository
{
    public function getAll(): Collection
    {
        return Scholarship::all();
    }

    public function getActiveScholarships(): Collection
    {
        return Scholarship::where('status', 'active')->get();
    }

    public function findById(int $id): ?Scholarship
    {
        return Scholarship::find($id);
    }

    public function create(array $data): Scholarship
    {
        return Scholarship::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $scholarship = $this->findById($id);
        return $scholarship ? $scholarship->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $scholarship = $this->findById($id);
        return $scholarship ? $scholarship->delete() : false;
    }

    public function getWithBudgets(int $id): ?Scholarship
    {
        return Scholarship::with('budgets.costCategory')->find($id);
    }
}