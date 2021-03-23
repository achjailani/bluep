<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'blogs';

    /**
     * The primary key associated with the table.
     * @var int
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
    	'user_id',
    	'title',
    	'cover',
    	'content',
    	'meta_keywords',
    	'meta_description'
    ];

    /**
     * The model's default values for attributes
     * @var array $attributes
     */
    protected $attributes = [
    	'is_published'	=> false,
    	'published_at'	=> null,
    	'seen'			=> 0
    ];

    /**
     * Get the articles for the category.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_blog', 'blog_id', 'category_id');
    }

    /**
     * Get user for category
     */
    public function user() {
    	return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
