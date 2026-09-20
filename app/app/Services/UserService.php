<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;

class UserService
{
    public function getAllUsers(): Collection
    {
        return User::orderBy('name')->get();
    }

    public function createUser(array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        return User::create($data);
    }

    public function updateUser(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        $user->update($data);
        return $user;
    }

    public function deleteUser(User $user): void
    {
        if ($user->id === 1) {
            throw new \Exception('Cannot delete the System user.');
        }
        
        if ($user->id === auth()->id()) {
            throw new \Exception('Cannot delete your own user account.');
        }
        
        if (\App\Models\Timer::where('user_id', $user->id)->exists()) {
            throw new \Exception('Cannot delete a user who has active or logged timers.');
        }
        
        $user->delete();
    }
}
