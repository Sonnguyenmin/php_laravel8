<?php

namespace App\Http\Controllers;
use App\Models\Slide;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index(){
        $slide = Slide::latest()->get();
        return view('Frontend.layout',compact('slide'));
    }
    public function contact(){
        return view('Frontend.contact');
    }
    public function cart(){
        return view('Frontend.cart');
    }
}
