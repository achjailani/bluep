<?php
namespace App\Repository\Blog;

use App\Repository\ImageUploader;
use Illuminate\Support\Str;
use App\Models\Blog;

class BlogRepository  {
	
	/**
	 * Define variable for model instace
	 * @var model
	 */
	protected $model;

	/**
	 * Define variable for repository instace
	 * @var uploader
	 */
	protected $uploader;

	/**
	 * Define variable for image url
	 * @var imageUrl
	 */
	protected $imageUrl = null;

	/**
	 * Instantiate a new class instance.
	 * @param \App\Repository\ImageUploader
	 * @param \App\Models\Blog
	 */
    public function __construct(Blog $model, ImageUploader $uploader) 
    {
    	$this->model  	= $model;
    	$this->uploader = $uploader;	
    	$this->imageUrl = '/blog/cover/';
    }


    public function call($data, $id = null) 
    {
    	try {
    		$model = ($id == null) ? new Blog() : Blog::find($id);
    		$model->user_id = $data['user_id'];
            $model->title   = $data['title'];
    		$model->slug 	= strtolower(implode('-', explode(' ', $data['title'])));
            $model->cover   = $this->imageExection($data, $id);
    		$model->content = $data['content'];
    		$model->meta_keywords 	= implode(', ', explode('%', $data['keywords']));
    		$model->meta_description= $data['title'];
    		$model->save();
            
            is_null($id) 
            ? $model->categories()->attach($data['category']) 
            : $model->categories()->sync($data['category']);
          
    		return ['status' => true, 'message'  => ($id == null) ? 'Data successfully created': 'Data successfully updated', 'data' => $model];
    	} catch (Exception $e) {
    		return ['status' => false, 'message' => $e->getMessage()];
    	}
    }

    public function getAll() 
    {
        try {
            $data = $this->model->with('categories')->orderBy('created_at', 'desc')->paginate(6);
            return ['status' => true, 'message' => 'Data successfully displayed', 'data' => $data];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }

    public function getSingle($param) 
    {
        try {
            $data = is_numeric($param)
                    ? $this->model->with('categories')->find($param) 
                    : $this->model->with('categories')->where('slug', $param)->first();
            if($data == null) {
                return ['status' => false, 'code' => 404, 'data' => []];
            }     
            return ['status' => true, 'message' => 'Data successfully displayed', 'data' => $data];
        } catch (Exception $e) {
            return ['status' => false, 'code' => 500, 'message' => $e->getMessage(), 'data' => []];
        }
    }

    public function destroy($id) 
    {
        try {
            $model = $this->model->find($id);
            if($model != null) {
                if(file_exists(storage_path('app/public').$model->cover)) {
                    unlink(storage_path('app/public').$model->cover);
                }
                $model->categories()->detach();
                $model->delete();
            }
            return ['status' => true, 'data' => [], 'message' => 'Data successfully deleted'];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function imageExection($request, $id = null) 
    {
        $data = $request->all();

        $add_name = Str::random(15).'_'.time();
        $img_name = $this->imageUrl.$add_name.'.'.$data['cover']->getClientOriginalExtension();

        if(!is_null($id)) {
            $model = $this->model->find($id);
            if($request->hasFile('cover')) {
                $this->uploader->up($data['cover'], $this->imageUrl, $disk = 'public', $add_name);
                if(file_exists(storage_path('app/public').$model->cover)) {
                    unlink(storage_path('app/public').$model->cover);
                }
            } else {
                $img_name = $model->cover;
            }
        } 

        if(is_null($id)) {
            $this->uploader->up($data['cover'], $this->imageUrl, $disk = 'public', $add_name);
        }
        
    	return $img_name;
    }
}