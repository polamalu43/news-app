<?php

namespace App\Requests;
use Core\Request;

class LoginRequest extends Request
{
    public function rules(): array
    {
        return [
            'email'     => 'required|email',
            'password'  => 'required|min:8|max:255',
        ];
    }
}
