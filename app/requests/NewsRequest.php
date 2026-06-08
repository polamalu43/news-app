<?php

namespace App\Requests;
use Core\Request;

class NewsRequest extends Request
{
    public function rules(): array
    {
        return [
            'nickname'  => 'required|min:1|max:255',
            'email' => 'required|email|unique:users,email',
        ];
    }
}
