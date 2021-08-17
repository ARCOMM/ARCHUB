<?php

namespace App\Http\Controllers;

use App\JoinRequest;
use App\Http\Requests;
use App\Models\Portal\User;
use Illuminate\Http\Request;
use App\Models\Operations\Operation;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    /**
     * Displays the homepage.
     *
     * @return view
     */
	public function index()
    {
        $banners = Storage::disk('images')->allFiles('banners');
        $banners = collect($banners)->map(function($file) {
            return url('images/'.$file);
        });

        return view('home.index', compact('banners'));
	}

    public function roster()
    {
        $members = User::all();

        return view('roster.index', compact('members'));
    }
}
