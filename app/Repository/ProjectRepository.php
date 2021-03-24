<?php
namespace App\Repository;

use App\Repository\ImageUploader;
use Illuminate\Support\Str;
use App\Models\Project;

class ProjectRepository {

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
	 * Instantiate a new class instance.
	 * @param \App\Repository\ImageUploader
	 * @param \App\Models\Project
	 */
    public function __construct(Project $model, ImageUploader $uploader) 
    {
    	$this->model  	= $model;
    	$this->uploader = $uploader;	
    	$this->imageUrl = '/peoject/thumnail/';
    }

    public function call($data, $id = null) 
    {
    	try {
    		$model = ($id == null) ? new Project() : Project::find($id);
    		$model->uuid_code= strtolower(Str::random(13));
    		$model->user_id  = $data['user_id'];
            $model->title    = $data['title'];
    		$model->description = $data['description'];
            $model->thumnail = $this->imageExection($data, $id);
            $model->url_link = $data['link'];
    		$model->meta_keywords 	= implode(', ', explode('%', $data['keywords']));
    		$model->meta_description= $data['title'];
    		$model->is_portofolio	= $data['is_portofolio']; 
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

        if(!is_null($id)) {
            $model = $this->model->find($id);
            if($request->hasFile('thumnail')) {
            	$img_name = $this->imageUrl.$add_name.'.'.$data['thumnail']->getClientOriginalExtension();
                $this->uploader->up($data['thumnail'], $this->imageUrl, $disk = 'public', $add_name);
                if(file_exists(storage_path('app/public').$model->thumnail)) {
                    unlink(storage_path('app/public').$model->thumnail);
                }
            } else {
                $img_name = $model->thumnail;
            }
        } 

        if(is_null($id)) {
        	$img_name = $this->imageUrl.$add_name.'.'.$data['thumnail']->getClientOriginalExtension();
            $this->uploader->up($data['thumnail'], $this->imageUrl, $disk = 'public', $add_name);
        }
        
    	return $img_name;
    }
}