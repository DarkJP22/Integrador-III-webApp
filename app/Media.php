<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $guarded = [];
    protected $table = 'media';

    public function setTagsAttribute($value)
    {
        $data = json_encode($value, JSON_UNESCAPED_UNICODE);

        $this->attributes['tags'] = $data;
    }
    public function getTagsAttribute($value)
    {
        return json_decode($value);
    }

    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search['q'] . '%')
                    ->orWhere('description', 'like', '%' . $search['q'] . '%')
                    ->orWhere('tags', 'like', '%' . $search['q'] . '%');
            });
        }


        return $query;
    }

    public function scopeTags($query, $tag)
    {
         
        if (isset($tag) && $tag) {

           $tagsArray = explode(",", $tag);

          $query = $query->where(function ($query) use ($tagsArray) {
                foreach ($tagsArray as $key => $value) {
                   
                    $query->orWhere('tags', 'like', '%' . trim($value) . '%');
                    
                }
                
            });
        
        }
       
        return $query;
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
