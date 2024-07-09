<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Post extends Model
{
    use HasFactory;

    protected $fillable = [
                        'title',
                        'slug',
                        'thumbnail',
                        'body',
                        'active',
                        'published_at',
                        'user_id',
                        'meta_title',
                        'meta_description'
                    ];
    protected $casts =[
        'published_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
         return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function shortBody($words=30): String
    {
        return Str::words(strip_tags($this->body),$words);
    }

    public function getFormatedDate(): String
    {
        return $this->published_at->format('F jS Y');
    }

    public function getThumbnail(): String
    {
        if(str_starts_with($this->thumbnail,'http')){
            return $this->thumbnail;
        }
        return '/storage/'.$this->thumbnail;
    }

    public function humanReadTime(): Attribute
    {
        return new Attribute(
            get: function ($value, $attributes) {
                $words = Str::wordCount(strip_tags($attributes['body']));
                $minutes = ceil($words / 200);

                return $minutes . ' ' . str('min')->plural($minutes);
            }
        );
    }

}
