<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Cms;


class BlogController extends Controller
{
    public function __construct()
    {
        
    }

    public function index(){
        $blogs = Blog::where('status',1)->get();
        $pageInfo = Cms::find(15);
        return view("frontend.blog.blog-listing", compact('blogs','pageInfo'));
    }

    public function blogDetails($id){
        $id = base64_decode($id);
        $blog_details = Blog::where('status',1)->where('id',$id)->first();
        return view("frontend.blog.blog-details", compact('blog_details'));
    }
}
