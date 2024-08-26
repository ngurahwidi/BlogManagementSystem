<?php

namespace App\Models;

use App\Models\User;
use App\Models\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'status', 'userId'];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'tag_article', 'tag_id', 'article_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
