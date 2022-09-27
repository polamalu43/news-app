<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Article;

class UsersController extends Controller
{
    public function index()
    {
        $articles = new Article();

        $authStatus = Auth::check();
        $likedByUser = $articles->liked_by_user;

        return view('index', compact('authStatus', 'likedByUser'));
    }
}
