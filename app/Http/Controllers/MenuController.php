<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        // Fetch menu items from the database
        $menuItems = MenuItem::all();
        return view('menu', compact('menuItems'));
    }
}
