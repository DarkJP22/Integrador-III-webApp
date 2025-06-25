<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class QuoteOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'photo_url',
        'quote_url',
        'fullPhone'
    ];

    public function getFullPhoneAttribute()
    {
        return ($this->phone_country_code ?? '+506') . $this->phone_number;
    }
    
    public function getQuoteUrlAttribute()
    {
        return $this->quote_path
            ? Storage::disk('s3')->url($this->quote_path)
            : null;
    } 
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
                $query->where('ide', 'like', '%' . $search['q'] . '%');
                $query->orWhere('name', 'like', '%' . $search['q'] . '%');
            });
        }

        if (isset($search['start']) && $search['start']) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);

            $query = $query->where([
                ['created_at', '>=', $start],
                ['created_at', '<=', $end->endOfDay()]
            ]);
        }

        return $query;
    }

    public function updatePhoto(UploadedFile $photo)
    {
        tap($this->photo_path, function ($previous) use ($photo) {
            $path = $photo->store('quote-orders-photos', 's3');
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

    public function updateQuote(UploadedFile $file)
    {
        tap($this->quote_path, function ($previous) use ($file) {
            $path = $file->store('quote-orders-files', 's3');
            $this->forceFill([
                'quote_path' => $path,
            ])->save();

            if ($previous) {
                Storage::disk('s3')->delete($previous);
            }
        });
    }

    public function deleteQuote()
    {
      

        if (is_null($this->quote_path)) {
            return;
        }

        Storage::disk('s3')->delete($this->quote_path);

        $this->forceFill([
            'quote_path' => null,
        ])->save();
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
