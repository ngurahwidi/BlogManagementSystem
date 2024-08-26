<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['title', 'userId', 'content', 'status'];

    
    public function scopeSearch($query, $request){

        if($request->has('search')) {
            $query->where(function($query) use ($request) {
                $query->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('content', 'like', '%'.$request->search.'%');
            });
        }

        if($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if($request->has('category_id')) {
            $query->whereHas('categories', function($query) use ($request) {
                $query->where('article_categories.id', $request->input('category_id'));
            });
        }

        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereDate('created_at', '>=', $request->from_date) 
            ->whereDate('created_at', '<=', $request->to_date);
        }

        return $query;
    }


    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function categories()
    {
        return $this->belongsToMany(ArticleCategory::class, 'article_category_article', 'article_id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_article', 'article_id', 'tag_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getDeletedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }
}
