<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Requests\RegistrationRequest;

class RegistrationService
{
    public function __construct(
        private UserRepository $repository
    ) {}

    public function registrationUser(RegistrationRequest $request): bool
    {
        $user = new User();
        $user->nickname = $request->input('nickname');
        $user->email = $request->input('email');
        $user->password = $this->hashPassword($request->input('password'));

        return $this->repository->registrationUser($user);
    }

    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
