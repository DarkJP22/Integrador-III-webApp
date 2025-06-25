<?php

namespace App\Http\Controllers\Api\Medic;


use App\Http\Controllers\Controller;
use App\Http\Resources\SocialImageResource;
use App\Setting;
use App\SocialImage;
use App\SocialStory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class SocialImageStoryController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');

    }


    public function store(SocialImage $socialImage)
    {
        $user = request()->user();

        $limitStoryByDay = Setting::getSetting('limit_story_by_day');

        if($limitStoryByDay !== null && SocialStory::where('user_id', $user->id) ->whereDate('created_at', now())->count() >= (int)$limitStoryByDay){
            throw ValidationException::withMessages([
                'message' => 'Solo puedes subir '. $limitStoryByDay .' historias al día'
            ]);
        }

        $image = SocialImage::findOrFail($socialImage->id);


        $image->user->stories()->create([
            'url' => $image->url,
            'public_id' => $image->public_id,
            'type' => $image->type,
            'duration' => $image->duration,
            'format' => $image->format,
        ]);


        return $image;


    }


}
