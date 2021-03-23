<?php
namespace App\Repository\Blog;

use App\Models\Category;

class CategoryRepository {

	protected $model;

	public function __construct(Category $model) 
	{
		$this->model = $model;
	}

	public function call($data, $id = null) 
	{
		try {
			$model = ($id == null) ? new Category() : Category::find($id);
			$model->name = $data['name'];
			$model->slug = strtolower(implode('-', explode(' ', $data['name'])));
			$model->save();
			return ['status' => true, 'message' => ($id==null) ? 'Category successfully created' : 'Category successfully updated', 'data' => $model];
		} catch (Exception $e) {
			return ['status' => false, 'message' => $e->getMessage()];
		}
	}


	public function getAll() 
	{
		try {
			$data = $this->model->with('blogs')->orderBy('created_at', 'desc')->paginate(6);
			return ['status' => true, 'message' => 'ok', 'data' => $data];
		} catch (Exception $e) {
			return ['status' => false, 'message' => $e->getMessage(), 'data' => []];
		}
	}

	public function getSingle($param) 
	{
		try {
			$data = is_numeric($param)
					? $this->model->find($param) 
					: $this->model->with('blogs')->where('slug', $param)->first();
			if($data == null) {
				return ['status' => false, 'code' => 404, 'data' => []];
			}
			return ['status' => true, 'message' => 'Display single data', 'data' => $data];
		} catch (Exception $e) {
			return ['status' => false, 'message' => $e->getMessage(), 'data' => []];
		}
	}

	public function destroy($id)
	{
		try {
			$this->model->destroy($id);
			return ['status' => true, 'message' => 'Category successfully deleted', 'data' => []];
		} catch (Exception $e) {
			return ['status' => false, 'message' => $e->getMessage()];
		}
	}
}