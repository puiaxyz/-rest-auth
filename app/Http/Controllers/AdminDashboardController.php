<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Display the User Management page.
     */
    public function userManagement()
    {
        // Retrieve all users from the data`base
        $users = User::all();
        return view('admin.userManagement', compact('users'));
    }
    public function analytics()
    {
        return view('admin.analytics'); // Ensure you have this view file
    }
}
