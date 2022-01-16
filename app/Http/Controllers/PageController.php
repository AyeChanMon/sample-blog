<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function article(){
        return "article page";
    }

    public function about(){
        return "about";
    }
}
