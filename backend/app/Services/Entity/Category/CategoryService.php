<?php

namespace App\Services\Entity\Category;

use App\Http\Resources\Entity\Category\CategoryCollection;
use App\Http\Resources\Entity\Category\CategoryResource;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    /**
     * List of all categories.
     * 
     * @return App\Http\Resources\Entity\Category\CategoryCollection
     */
    public function index(): CategoryCollection
    {
        return new CategoryCollection(Category::paginate());
    }

    /**
     * Show a single category.
     * 
     * @param App\Models\Category $category The model of the category which needs to be retrieved.
     * @return App\Http\Resources\Entity\Category\CategoryResource
     */
    public function show(Category $category)
    {
        return new CategoryResource(Category::findOrFail($category->id));
    }
    
    /**
     * Handle category store request.
     * 
     * @param App\Http\Requests\Authentication\LoginRequest $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function store($request)
    {
        try {
            Category::create(['name' => $request->name]);

            return response()->json(['message' => 'Successfully Category Created.'], 201);

        } catch (ValidationException $validationException) {
            info("Validation Error on store Category: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch(\Throwable $th) {
            info("Error on storing Category Category: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during creating Category.'
            ], 500);
        }
    }

    /**
     * Handle category update request.
     * 
     * @param App\Models\Category $Category The model of the Category which needs to be updated.
     * @param \Illuminate\Http\Request $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function update($Category, $request)
    {
        try {
            $Category->update(['name' => $request->name]);

            return response()->json(['message' => 'Successfully Category Updated.'], 200);
            
        } catch (ValidationException $validationException) {
            info("Validation Error on update Category: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch(\Throwable $th) {
            info("Error on storing Category Category: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during updating Category.'
            ], 500);
        }
    }

    /**
     * Handle delete category request.
     * 
     * @param App\Models\Category $Category The model of the category which needs to be deleted.
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            if ($this->categoryHasRecord($category->id)) {
                return response()->json(['message' => 'Cannot delete category. There are subcategories associated with this category.'], 409);
            }
            
            $category->delete();
    
            return response()->json(['message' => 'Successfully Category Deleted.'], 200);
        } catch (\Throwable $th) {
            info("Error on deleting Category: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during deleting category.'
            ], 500);
        }
    }

    /**
     * Check if category already associated with subcategory
     * 
     * @param App\Models\Category $category The model of the category which needs to be checked.
     * @return bool
     */
    public function categoryHasRecord($id) : bool
    {
        return Subcategory::where('category_id', $id)->exists();
    }
}
