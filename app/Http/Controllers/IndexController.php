<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portaria;

class IndexController extends Controller
{
    public function index() {
        $portaria = Portaria::all();
        return view("index", ['portarias' => $portaria]);
    }
}
