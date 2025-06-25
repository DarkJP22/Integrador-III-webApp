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

class SocialProfileImageController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');

    }

    public function index()
    {
        $images = SocialImage::query()
            ->where('user_id', request()->user()->id)
            ->latest()
            ->latest('id')
            ->paginate();

        return SocialImageResource::collection($images);
    }

    public function store()
    {
        $user = request()->user();

        $data = $this->validate(request(), [
            'public_id' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'duration' => ['nullable', 'numeric'],
            'format' => ['nullable', 'string', 'max:255'],
        ]);


        $image = SocialImage::create([
            'user_id' => $user->id,
            'url' => $data['url'],
            'public_id' => $data['public_id'],
            'type' => $data['type'],
            'duration' => $data['duration'] ?? 0,
            'format' => $data['format'] ?? null,
        ]);

        $storyError = null;

        if(request()->boolean('upload_story') && request()->upload_story){
            $limitStoryByDay = Setting::getSetting('limit_story_by_day');

            if($limitStoryByDay !== null && SocialStory::where('user_id', $user->id) ->whereDate('created_at', now())->count() >= ((int)$limitStoryByDay)){
                $storyError = 'Pero solo puedes subir '. $limitStoryByDay .' historias al día';
            }

            $image->user->stories()->create([
                'url' => $data['url'],
                'public_id' => $data['public_id'],
                'type' => $data['type'],
                'duration' => $data['duration'] ?? 0,
                'format' => $data['format'] ?? null,
            ]);
        }


        return [
            'image' => $image,
            'story_error' => $storyError
        ];


    }


}
