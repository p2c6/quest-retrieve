<?php

namespace App\Http\Controllers\API\v1\Admin\Entity\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\Category\StoreCategoryRequest;
use App\Http\Requests\Entity\Category\UpdateCategoryRequest;
use App\Http\Resources\Entity\Category\CategoryCollection;
use App\Http\Resources\Entity\Category\CategoryResource;
use App\Models\Category;
use App\Models\User;
use App\Services\Entity\Category\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if(Gate::denies('viewAny', Category::class)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }
        
        return $this->service->index($request);
    }

    /**
     * Show a single category.
     * 
     * @param App\Models\Category $role The model of the category which needs to be retrieved.
     * @return App\Http\Resources\Entity\Category\CategoryResource|Illuminate\Http\JsonResponse
     */
    public function show(Category $category): CategoryResource|JsonResponse
    {
        if(Gate::denies('view', $category)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }

        return $this->service->show($category);
    }

    /**
     * Handle store category request.
     * 
     * @param App\Http\Requests\Entity\Category\StoreCategoryRequest $request The HTTP request object containing role data.
     * @return Illuminate\Http\JsonResponse
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        if(Gate::denies('create', Category::class)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }


        return $this->service->store($request);
    }

    /**
     * Handle update category request.
     * 
     * @param App\Models\Category  The model of the category which needs to be updated.
     * @param App\Http\Requests\Entity\Category\UpdateCategoryRequest $request The HTTP request object containing role data.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Category $category, UpdateCategoryRequest $request): JsonResponse
    {
        if(Gate::denies('update', $category)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }

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
        if(Gate::denies('delete', $category)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }

        return $this->service->destroy($category);
    }

    /**
     * List of all categories for dropdown.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function dropdownCategories(): JsonResponse
    {
        return $this->service->dropdownCategories();
    }
}
