<?php

namespace App\Http\Controllers\API\v1\Admin\Entity\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\Category\StoreCategoryRequest;
use App\Http\Requests\Entity\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\Entity\Category\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * The CategoryService instance.
     * 
     * @var CategoryService
     */
    private $service;

    /**
     * The CategoryController constructor.
     * 
     * @param CategoryService $service The instance of CategoryService
     */
    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * List of all categories.
     * 
     * @param App\Models\Category The model of the category which needs to be retrieved.
     * @return  App\Http\Resources\CategoryCollection
     */
    public function index(): CategoryCollection
    {
        return $this->service->index();
    }

    /**
     * Show a single category.
     * 
     * @param App\Models\Category $role The model of the category which needs to be retrieved.
     * @return App\Http\Resources\CategoryResource
     */
    public function show(Category $role): CategoryResource
    {
        return $this->service->show($role);
    }

    /**
     * Handle store category request.
     * 
     * @param App\Http\Requests\Entity\Role\StoreRoleRequest $request The HTTP request object containing role data.
     * @return Illuminate\Http\JsonResponse
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        return $this->service->store($request);
    }

    /**
     * Handle update category request.
     * 
     * @param App\Models\Category  The model of the category which needs to be updated.
     * @param \Illuminate\Http\Request $request The HTTP request object containing role data.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Category $category, UpdateCategoryRequest $request): JsonResponse
    {
        return $this->service->update($category, $request);
    }

    /**
     * Handle delete category.
     * 
     * @param App\Models\Category $category The model of the category which needs to be deleted.
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        return $this->service->delete($category);
    }
}
