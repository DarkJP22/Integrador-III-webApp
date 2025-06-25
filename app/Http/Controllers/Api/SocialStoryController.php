<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SocialStoryResource;
use App\SocialStory;
use Illuminate\Http\Request;


use App\Http\Controllers\Controller;

class SocialStoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index()
    {
        $stories = SocialStory::query()
            ->with('user')
            ->where('created_at', '>=', now()->subDays(2))
            ->orderBy('created_at', 'desc')
            ->limit(500)
            ->get();

        return SocialStoryResource::collection($stories);
    }


}
