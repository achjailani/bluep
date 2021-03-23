<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectImage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'project_images';

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
    	'project_id',
    	'image',
    ];

    /**
     * Get project for image
     */
   	public function project() {
   		return $this->belongsTo(Project::class, 'project_id', 'id');
   	}
}
