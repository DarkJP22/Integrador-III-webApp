<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ExamPackage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'photo_url',
    ];

    public function getPhotoUrlAttribute()
    {
        return $this->photo_path
            ? Storage::disk('s3')->url($this->photo_path)
            : null;
    }


    public function scopeSearch($query, $search)
    {
     
        if (isset($search['q']) && $search['q']) {
            return $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search['q'] . '%');
            });
        }

        return $query;
    }

    public function updatePhoto(UploadedFile $photo)
    {
        tap($this->photo_path, function ($previous) use ($photo) {
            $path = $photo->store('exam-package-photos', 's3');
            $this->forceFill([
                'photo_path' => $path,
            ])->save();

            if ($previous) {
                Storage::disk('s3')->delete($previous);
            }
        });
    }

    public function deletePhoto()
    {
      

        if (is_null($this->photo_path)) {
            return;
        }

        Storage::disk('s3')->delete($this->photo_path);

        $this->forceFill([
            'photo_path' => null,
        ])->save();
    }

}
