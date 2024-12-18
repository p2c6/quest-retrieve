<?php

namespace App\Http\Controllers\API\v1\Admin\Entity\Subcategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\Subcategory\StoreSubcategoryRequest;
use App\Http\Requests\Entity\Subcategory\UpdateSubcategoryRequest;
use App\Http\Resources\Entity\Subcategory\SubcategoryCollection;
use App\Http\Resources\Entity\Subcategory\SubcategoryResource;
use App\Models\Subcategory;
use App\Services\Entity\Subcategory\SubCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    /**
     * The SubCategoryService instance.
     * 
     * @var SubCategoryService
     */
    private $service;

    /**
     * The SubcategoryController constructor.
     * 
     * @param SubCategoryService $service The instance of SubCategoryService
     */
    public function __construct(SubCategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * List of all categories.
     * 
     * @param App\Models\Subcategory The model of the subcategory which needs to be retrieved.
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->service->index($request->query('keyword'));
    }

    /**
     * Show a single subcategory.
     * 
     * @param App\Models\Subcategory $subCategory The model of the subcategory which needs to be retrieved.
     * @return App\Http\Resources\Entity\Subcategory\SubcategoryResource
     */
    public function show(Subcategory $subCategory): SubcategoryResource
    {
        return $this->service->show($subCategory);
    }

    /**
     * Handle store Subcategory request.
     * 
     * @param App\Http\Requests\Entity\SubCategory\StoreSubcategoryRequest $request The HTTP request object containing role data.
     * @return Illuminate\Http\JsonResponse
     */
    public function store(StoreSubcategoryRequest $request): JsonResponse
    {
        return $this->service->store($request);
    }

    /**
     * Handle update Subcategory request.
     * 
     * @param App\Models\Subcategory  The model of the subcategory which needs to be updated.
     * @param App\Http\Requests\Entity\SubCategory\UpdateSubcategoryRequest $request The HTTP request object containing role data.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Subcategory $subCategory, UpdateSubcategoryRequest $request): JsonResponse
    {
        return $this->service->update($subCategory, $request);
    }

    /**
     * Handle delete Subcategory.
     * 
     * @param App\Models\Subcategory $subcategory The model of the Subcategory which needs to be deleted.
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Subcategory $subCategory): JsonResponse
    {
        return $this->service->destroy($subCategory);
    }
}
