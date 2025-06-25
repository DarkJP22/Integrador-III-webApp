<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SocialImageResource;
use App\Http\Resources\SocialStoryResource;
use App\SocialImage;
use App\SocialStory;
use Illuminate\Http\Request;


use App\Http\Controllers\Controller;

class SocialPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    
    public function index()
    {

        $images = SocialImage::query()
            ->latest()
            ->latest('id')
            ->paginate();

        return SocialImageResource::collection($images);


    }

    

}
