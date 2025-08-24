<?php

namespace App\Repositories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Collection;

class ApplicationRepository
{
    public function getAll(): Collection
    {
        return Application::with(['user', 'scholarship', 'documents'])->get();
    }

    public function findById(int $id): ?Application
    {
        return Application::with(['user', 'scholarship', 'documents', 'logs.performer'])->find($id);
    }

    public function getUserApplications(int $userId): Collection
    {
        return Application::with(['scholarship', 'documents'])
            ->where('user_id', $userId)
            ->get();
    }

    public function create(array $data): Application
    {
        return Application::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $application = $this->findById($id);
        return $application ? $application->update($data) : false;
    }

    public function getApplicationLogs(int $applicationId): Collection
    {
        return Application::find($applicationId)->logs()->with('performer')->get();
    }

    public function getApplicationsByStatus(string $status): Collection
    {
        return Application::with(['user', 'scholarship'])
            ->where('status', $status)
            ->get();
    }
}