<?php

namespace App\Requests;
use Core\Request;

class FavoriteRequest extends Request
{
    public function rules(): array
    {
        return [
            'user_id'  => 'required',
            'news_id' => 'required',
        ];
    }
}
