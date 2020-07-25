<?php

namespace App\Services;


use App\Helpers\PasswordGenerator;
use App\Models\Agent;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class AgentService implements IUserService
{


    public function create(array $agentData)
    {
        return Agent::query()->create([
            'person_id' => $agentData['person_id'],
            'name' => $agentData['name'],
            'password' => $agentData['password'],
            'email' => $agentData['email'],
            'mini_grid_id' => $agentData['mini_grid_id'],
            'agent_commission_id' => $agentData['agent_commission_id']
        ]);
    }

    public function update($agent, $data)
    {
        return $agent->update($data);
    }

    public function updateDevice($agent, $deviceId)
    {
        $agent->device_id = $deviceId;
        $agent->update();
        $agent->fresh();

    }

    public function resetPassword(string $email)
    {
        try {
            $newPassword = PasswordGenerator::generatePassword();
        } catch (Exception $exception) {
            $newPassword = time();
        }
        try {
            $agent = Agent::query()->where('email', $email)->firstOrFail();
        } catch (ModelNotFoundException $x) {
            $message = 'Invalid email.';
            return $message;
        }

        $agent->password = $newPassword;
        $agent->update();
        $agent->fresh();
        return $newPassword;
    }

    public function list($relations): LengthAwarePaginator
    {
        return Agent::with(['address', 'miniGrid'])->paginate(config('settings.paginate'));
    }

    public function setFirebaseToken($agent, $firebaseToken)
    {
        $agent->fire_base_token = $firebaseToken;
        $agent->update();
        $agent->fresh();
    }

    public function getAgentBalance($agent)
    {
        return $agent->balance;
    }

    public function searchAgent($searchTerm, $paginate)
    {
        if ($paginate === 1) {
            return Agent::with('miniGrid')->WhereHas('miniGrid', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%');
            })->orWhere('name', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')->paginate(15);
        }

        return Agent::with('miniGrid')->WhereHas('miniGrid', function ($q) use ($searchTerm) {
            $q->where('name', 'LIKE', '%' . $searchTerm . '%');
        })->orWhere('name', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')->get();

    }
}
