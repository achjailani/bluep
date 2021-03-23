<?php
namespace App\Repository;

use App\Repository\ImageUploader;
use Illuminate\Support\Str;
use App\Models\Research;

class ResearchRepository {

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
	 * Define variable for image file name
	 * @var $fileField
	 */
	protected $fieldName;

	/**
	 * Instantiate a new class instance.
	 * @param \App\Repository\ImageUploader
	 * @param \App\Models\Blog
	 */
    public function __construct(Research $model, ImageUploader $uploader) 
    {
    	$this->model  	= $model;
    	$this->uploader = $uploader;	
    	$this->imageUrl = '/research/thumnail/';
    	$this->fileUrl  = '/research/file/';
    }

    public function call($data, $id = null) 
    {
    	try {
    		$model = ($id == null) ? new Research() : Research::find($id);
    		$model->uuid_code= strtolower(Str::random(13));
    		$model->user_id  = $data['user_id'];
            $model->title    = $data['title'];
    		$model->slug 	 = strtolower(implode('-', explode(' ', $data['title'])));
            $model->thumnail = $this->imageExection($data, $id);
            $model->file 	 = $this->fileExection($data, $id);
    		$model->description = $data['description'];
    		$model->meta_keywords 	= implode(', ', explode('%', $data['keywords']));
    		$model->meta_description= $data['title'];
    		$model->save();
          
    		return ['status' => true, 'message'  => ($id == null) ? 'Data successfully created': 'Data successfully updated', 'data' => $model];
    	} catch (Exception $e) {
    		return ['status' => false, 'message' => $e->getMessage()];
    	}
    }

    public function getAll() 
    {
        try {
            $data = $this->model->with('user')->orderBy('created_at', 'desc')->paginate(6);
            return ['status' => true, 'message' => 'Data successfully displayed', 'data' => $data];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }

    public function getSingle($param) 
    {
        try {
            $data = is_numeric($param)
                    ? $this->model->with('user')->find($param) 
                    : $this->model->with('user')->where('uuid_code', $param)->first();
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
                if(file_exists(storage_path('app/public').$model->thumnail)) {
                    unlink(storage_path('app/public').$model->thumnail);
                }
                if(file_exists(storage_path('app/public').$model->file)) {
                    unlink(storage_path('app/public').$model->file);
                }
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
        $img_name = $this->imageUrl.$add_name.'.'.$data['thumnail']->getClientOriginalExtension();

        if(!is_null($id)) {
            $model = $this->model->find($id);
            if($request->hasFile('thumnail')) {
                $this->uploader->up($data['thumnail'], $this->imageUrl, $disk = 'public', $add_name);
                if(file_exists(storage_path('app/public').$model->thumnail)) {
                    unlink(storage_path('app/public').$model->thumnail);
                }
            } else {
                $img_name = $model->thumnail;
            }
        } 

        if(is_null($id)) {
            $this->uploader->up($data['thumnail'], $this->imageUrl, $disk = 'public', $add_name);
        }
        
    	return $img_name;
    }

    public function fileExection($request, $id = null) 
    {
        $data = $request->all();

        $add_name = Str::random(15).'_'.time();
        $img_name = $this->fileUrl.$add_name.'.'.$data['file']->getClientOriginalExtension();

        if(!is_null($id)) {
            $model = $this->model->find($id);
            if($request->hasFile('file')) {
                $this->uploader->up($data['file'], $this->fileUrl, $disk = 'public', $add_name);
                if(file_exists(storage_path('app/public').$model->file)) {
                    unlink(storage_path('app/public').$model->file);
                }
            } else {
                $img_name = $model->file;
            }
        } 

        if(is_null($id)) {
            $this->uploader->up($data['file'], $this->fileUrl, $disk = 'public', $add_name);
        }
        
    	return $img_name;
    }
}