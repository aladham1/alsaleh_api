<?php

namespace App\Http\Controllers;

use App\Http\Filters\ManagerFilters;
use App\Http\Requests\ManagerStoreRequest;
use App\Http\Requests\ManagerUpdateRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\DonorsResource;
use App\Http\Resources\ManagerResource;
use App\Models\Category;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Models\UserActivity;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class CategoryController extends Controller
{

    public function index()
    {
        if (\request()->per_page > 0) {
            $categories = Category::paginate(request()->per_page);
        }else{
            $categories = Category::all();

        }

        return CategoryResource::collection($categories);
    }


    /**
     * @param $id
     * @return CategoryResource
     */
    public function show($id): CategoryResource
    {
        $category = Category::with('projects')->where('id', $id)->first();
        return new CategoryResource($category);
    }

    /**
     * @param StoreCategoryRequest $request
     * @return CategoryResource
     */
    public function store(StoreCategoryRequest $request): CategoryResource
    {
        $category = Category::create($request->validated());
        return new CategoryResource($category);
    }

    /**
     * @param StoreCategoryRequest $request
     * @param $id
     * @return CategoryResource
     */
    public function update(StoreCategoryRequest $request, $id): CategoryResource
    {
        $category = Category::find($id);

        $category->update($request->validated());

        return new CategoryResource($category);
    }

    public function destroy($id): Response
    {
        $category = Category::where('id', $id)->first();
        $category->delete();
        return response()->noContent();
    }

}
