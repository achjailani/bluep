<?php

namespace App\Http\Controllers\API\V1\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Repository\Blog\BlogRepository;
use App\Models\Blog;

class BlogController extends Controller
{
	/**
	 * Define variable for respository instance
	 * @var $repository 
	 */
	protected $repository;

	/**
	 * Instantiate a new controller instance.
	 * @param \App\Repository\Blog\BlogRepository
	 */
	public function __construct(BlogRepository $repository) 
	{
		$this->repository = $repository;
	}

	/**
	 * Get listing of blog
	 * @return \Illuminate\Http\Response
	 */
	public function index() 
	{
		$response = $this->repository->getAll();
		if($response['status'] == true) {
			return $this->restApi($response['data'], true, $response['message']);
		}
		return $this->apiInternalServerErrorResponse($response['message']);
	}
	
	/**
	 * Get single data
	 * @param $var
	 * @return \Illuminate\Http\Response
	 */
	public function showSingle($var) 
	{
		$response = $this->repository->getSingle($var);
		if($response['status'] == true) {
			return $this->restApi($response['data'], false, $response['message']);
		}
		return $this->apiInternalServerErrorResponse($response['message']);
	} 

	/**
	 * Store new blog
	 * @param \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$validation = $this->validator($request->all());
		if($validation->fails()) {
			return $this->apiUnprocessableEntityResponse($validation->errors());
		}
		$response = $this->repository->call($request);
		if($response['status'] == true) {
			return $this->restApi($response['data'], false, $response['message']);
		}

		return $this->apiInternalServerErrorResponse($response['message']);
	}

	/**
	 * Update specific data
	 * @param int $id
	 * @param \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) 
	{
		$validation = $this->validator($request->all(), $id);
		if($validation->fails()) {
			return $this->apiUnprocessableEntityResponse($validation->errors());
		}

		$response = $this->repository->call($request, $id);
		if($response['status'] == true) {
			return $this->restApi($response['data'], false, $response['message']);
		}
		return $this->apiInternalServerErrorResponse($response['message']);
	}

	/**
	 * Remove specific data
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function delete($id)
	{
		$response = $this->repository->destroy($id);
		if($response['status'] == true) {
			return $this->restApi($response['data'], false, $response['message']);
		}
		return $this->apiInternalServerErrorResponse($response['message']);
	}

	/**
	 * Validate before stored or updated
	 * @param array $data
	 * @param int $id
	 * @return \Illuminate\Support\Facades\Validator
	 */
	private function validator(array $data, $id = null)
	{
		$imageRule = ($id == null) ? 'required':'nullable';
		return Validator::make($data, [
			'title'	=> 'required|string|max:255',
			'category'	=> 'required',
			'cover'	=> $imageRule.'|mimes:jpg,png,jpeg|max:2048',
			'meta_keywords'	=> 'nullable|string',
		]);
	}
}
