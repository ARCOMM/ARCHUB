<?php

namespace App\Http\Controllers\Users;

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

    public function search(Request $request)
    {
        $term = $request->query('term');
        $users = User::select('id', 'username as text')
        ->where('username', 'like', '%'.$term.'%')
        ->orderBy('username', 'ASC')
        ->get();

        return $users;
    }
}
