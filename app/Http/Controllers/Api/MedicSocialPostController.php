<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SocialImageResource;
use App\Http\Resources\SocialStoryResource;
use App\SocialImage;
use App\SocialStory;
use App\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MedicSocialPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index(User $user)
    {
        $images = SocialImage::query()
            ->where('user_id', $user->id)
            ->when(request('type'), function ($query) {
                $query->where('type', request('type'));
            })
            ->latest()
            ->latest('id')
            ->paginate();

        return SocialImageResource::collection($images)->additional([
            'total_images' => SocialImage::query()
                ->where('user_id', $user->id)
                ->where('type', 'image')
                ->count(),
            'total_videos' => SocialImage::query()
                ->where('user_id', $user->id)
                ->where('type', 'video')
                ->count(),
            'total_videos_in_seconds' => (float)SocialImage::query()
                ->where('user_id', $user->id)
                ->where('type', 'video')
                ->sum('duration'),
        ]);
    }

    public function destroy(User $user, SocialImage $socialImage)
    {
        $this->authorize('delete', $socialImage);

        DB::transaction(function () use ($socialImage) {
            Cloudinary::uploadApi()->destroy($socialImage->public_id);

            SocialStory::where('public_id', $socialImage->public_id)->delete();

            $socialImage->delete();
        });



        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }


}
