<?php

namespace App\Http\Controllers\API\V1\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Repository\Blog\CategoryRepository;
use App\Models\Category;

class CategoryController extends Controller
{
	/**
	 * Define variable for model instace
	 * @var model
	 */
	protected $model;

    /**
     * Define variable for respository instace
     * @var model
     */
    protected $repository;

	/**
	 * Instantiate a new controller instance.
	 * @param \App\Models\Category
	 */
    public function __construct(Category $model, CategoryRepository $repository) 
    {
    	$this->model       = $model;
        $this->repository  = $repository;
    }

    /**
     * Get listing of category
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        $data = $this->repository->getAll();
        if($data['status'] === true) {
            return $this->restApi($data['data'], true);
        }
        return $this->apiInternalServerErrorResponse($data['message']);
    }

    /**
     * Get single data
     * @param $param
     */
    public function showSingle($param) {
        $response = $this->repository->getSingle($param);
        if($response['status'] === true) {
            return $this->restApi($response['data'], false, $response['message']);
        }

        if($response['code'] == 404) {
            return $this->apiNotFoundResponse($response['data']);
        }
        return $this->apiInternalServerErrorResponse($response['message']);
    }

    /**
     * Store new data
     * @param  \Illuminate\Http\StoreRequest
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req) 
    {
    	$validation = $this->validator($req->all());
    	if($validation->fails()) {
    		return $this->apiUnprocessableEntityResponse($validation->errors());
    	}
        
        $response = $this->repository->call($req->all());
        if($response['status'] === true) {
            return $this->restApi($response['data'], false, $response['message']);
        }    	
    	return $this->apiInternalServerErrorResponse($response['message']);
    }

    /**
     * Update specific data
     * @param  \Illuminate\Http\StoreRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $id) 
    {
        $validation = $this->validator($req->all(), $id);
        if($validation->fails()) {
            return $this->apiUnprocessableEntityResponse($validation->errors());
        }
        
        $response = $this->repository->call($req->all(), $id);
        if($response['status'] === true) {
            return $this->restApi($response['data'], false, $response['message']);
        }       
        return $this->apiInternalServerErrorResponse($response['message']);

    }

    /**
     * Delete specific data
     * @param int id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)  
    {
    	$response = $this->repository->destroy($id);
        if($response['status'] === true) {
            return $this->restApi($response['data'], false, $response['message']);
        }
    	return $this->apiInternalServerErrorResponse($response['message']);	
    }

    /**
     * Validate field's values before created or updated
     * @param array $data
     * @param int $id
     */
    public function validator(array $data, $id = null ) 
    {
        return Validator::make($data, [
            'name'  => ($id == null) ? 'required|string|max:255|unique:categories' : 'required|string|max:255'
        ]);
    }
}
