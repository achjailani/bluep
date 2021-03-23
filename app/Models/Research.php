<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'researches';

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
    	'file',
    	'meta_keywords',
    	'meta_description',
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

    public function user() {
    	return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
