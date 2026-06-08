<?php

namespace App\Requests;
use Core\Request;

class RegistrationRequest extends Request
{
    public function rules(): array
    {
        return [
            'nickname'     => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|password',
            'pass_confirm'  => 'required|same_password',
        ];
    }
}
