<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        $posts = Post::count();
        $comments = Comment::count();
        $users = User::count();
        $categories = NewsCategory::count();
        return view("dashboard.home.index", compact("posts", "comments", "users", "categories"));
    }
}
