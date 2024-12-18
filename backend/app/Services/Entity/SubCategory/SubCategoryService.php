<?php

namespace App\Services\Entity\SubCategory;

use App\Filters\FilterSubcategory;
use App\Http\Resources\Entity\SubCategory\SubCategoryCollection;
use App\Http\Resources\Entity\SubCategory\SubCategoryResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SubCategoryService
{
    /**
     * List of all subcategories.
     * 
     * @return App\Http\Resources\Entity\SubCategory\SubCategoryCollection
     * @return Illuminate\Http\JsonResponse
     */
    public function index($keyword): JsonResponse
    {
        $category = QueryBuilder::for(Subcategory::class)
        ->select(
            'id', 
            'category_id', 
            'name'
        )
        ->with(['category' => function($q) {
            $q->select('id', 'name');
        }])
        ->allowedIncludes('category')
        ->allowedFilters([
            AllowedFilter::custom('keyword', new FilterSubcategory)
        ])
        ->paginate(5)
        ->appends($keyword);

        return response()->json($category);
    }

    /**
     * Show a single category.
     * 
     * @param App\Models\Subcategory $subCategory The model of the subcategory which needs to be retrieved.
     * @return App\Http\Resources\Entity\SubCategory\SubCategoryResource
     */
    public function show(Subcategory $subCategory)
    {
        return new SubCategoryResource($subCategory);
    }
    
    /**
     * Handle subcategory store request.
     * 
     * @param App\Models\Subcategory $subCategory The model of the subcategory which needs to be store.
     * @param  App\Http\Requests\Entity\SubCategory\StoreSubcategoryRequest $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function store($request): mixed
    {
        try {
            if (!$this->categoryExists($request->category_id)) {
                return response()->json(['message' => 'Cannot store subcategory. Category not found.'], 404);
            }

            Subcategory::create(['category_id' => $request->category_id, 'name' => $request->name]);

            return response()->json(['message' => 'Successfully Subcategory Created.'], 201);

        } catch (ValidationException $validationException) {
            info("Validation Error on store Subcategory: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch(\Throwable $th) {
            info("Error on storing Category Subcategory: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during creating Subcategory.'
            ], 500);
        }
    }

    /**
     * Handle subcategory update request.
     * 
     * @param App\Models\Subcategory $subCategory The model of the subcategory which needs to be updated.
     * @param  App\Http\Requests\Entity\SubCategory\UpdateSubcategoryRequest $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function update(Subcategory $subCategory, $request): mixed
    {
        try {
            if (!$this->categoryExists($request->category_id)) {
                return response()->json(['message' => 'Cannot update subcategory. Category not found.'], 404);
            }

            $subCategory->update(['category_id' => $request->category_id, 'name' => $request->name]);

            return response()->json(['message' => 'Successfully Subcategory Updated.'], 200);
            
        } catch (ValidationException $validationException) {
            info("Validation Error on update Subcategory: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch(\Throwable $th) {
            info("Error on storing Subcategory: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during updating Subcategory.'
            ], 500);
        }
    }

    /**
     * Handle delete subcategory request.
     * 
     * @param App\Models\Subcategory $subCategory The model of the subcategory which needs to be deleted.
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Subcategory $subCategory): JsonResponse
    {
        try {
            if ($this->subCategoryHasRecord($subCategory)) {
                return response()->json([
                    'message' => 'Cannot delete subcategory. There are posts associated with this subcategory.'
                ], 409);
            }
        
            $subCategory->delete();
        
            return response()->json([
                'message' => 'Successfully Subcategory Deleted.'
            ], 200);
        } catch (\Throwable $th) {
            info("Error on deleting Subcategory: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during deleting category.'
            ], 500);
        }
    }

    /**
     * Check if category exists
     * 
     * @param int $category The category id which needs to be checked.
     * @return bool
     */
    public function categoryExists(int $categoryId): bool
    {
        return Category::where('id', $categoryId)->exists();
    }

    /**
     * Check if posts have a record associated with the given subcategory.
     * 
     * @param App\Models\Subcategory $subCategory The subcategory model which needs to be checked.
     * @return bool
     */
    public function subCategoryHasRecord(Subcategory $subCategory): bool
    {
        return Post::where('subcategory_id', $subCategory->id)->exists();
    }
}
