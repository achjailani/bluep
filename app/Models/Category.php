<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'categories';

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
    	'name',
    	'slug',
    ];

    /**
     * Get the articles for the category.
     */
    public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'category_blog', 'blog_id', 'category_id');
    }
}
