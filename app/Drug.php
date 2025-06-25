<?php

namespace App;

use App\Enums\DrugStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Drug extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['image_url'];

    const array PRESENTATIONS = [
        'Tableta',
        'Capsula',
        'Jarabe',
        'Suspención',
        'Loción',
        'Crema',
        'Gel',
        'Ampolla',
        'Vial',
        'Gotas',
        'Spray',
        'Polvo',
        'Frasco',
        'Galón',
        'Otros'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search['q'].'%')
                        ->orWhere('laboratory', 'like', '%'.$search['q'].'%');
                });


        }

        return $query;
    }

    protected function casts(): array
    {
        return [
            'status' => DrugStatus::class,
        ];
    }

}
