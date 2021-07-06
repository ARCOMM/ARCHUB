<?php

namespace App\Http\Controllers\Users;

use App\Discord;
use App\Models\Portal\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return view('user.admin.index', compact('users'));
    }
}
