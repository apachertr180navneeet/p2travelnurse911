<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class SubscribeController extends Controller
{
    public function index() {        
        $subscribes = Subscribe::orderBy("id", "DESC")->paginate(20); 
        return view("dashboard.subscribe.index", compact("subscribes"));
    }

    
}
