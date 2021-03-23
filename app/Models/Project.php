<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'projects';

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
    	'description',
    	'thumnail',
    	'url_link',
    	'meta_keywords',
    	'meta_description',
    	'is_portofolio',
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
     * Get user for project
     */
    public function user() {
    	return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get images for project
     */
    public function images() {
    	return $this->hasMany(ProjectImage::class, 'project_id', 'id');
    }
}
