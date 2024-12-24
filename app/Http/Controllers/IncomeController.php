<?php

namespace App\Http\Controllers;

use App\Http\Filters\IncomeFilters;
use App\Http\Requests\IncomeUpdateRequest;
use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Http\Resources\IncomeResource;
use App\Models\Income;
use App\Models\UserRole;
use App\Models\Role;
use App\Models\UserActivity;

use Illuminate\Http\Request;

class IncomeController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\IncomeResource
     */
    public function index(IncomeFilters $incomeFilters)
    {
        $incomes = Income::with('media')->filterBy($incomeFilters)->paginate();
        return  IncomeResource::collection($incomes);
    }

    /**
     * @param \App\Http\Requests\IncomeStoreRequest $request
     * @return \App\Http\Resources\IncomeResource
     */
    public function store(StoreIncomeRequest $request)
    {
        if (in_array(auth()->user()->type,['admin','manager'])){
            if(auth()->user()->type == 'manager'){
                $user_role      = UserRole::where(['project_id' => $request->project_id , 'user_id' => auth()->user()->id])->first();
                if($user_role){
                    $role           = Role::where('id', $user_role->role_id)->first();
                    if ($role->name == 'finacial_manager'){
                        return response()->json(['message' => 'Sorry But You Are Not Allowed To Create News'],400);
                    }
                }else{
                    return response()->json(['message' => 'Sorry But You Are Not Included In This Project'],400);
                }
            }
        }
    
        $income = Income::create($request->validated());
        $images = collect($request->images)->map(function ($image) {
            return ['path' => $image, 'type' => 'image'];
        });
        $income->media()->createMany($images);
        UserActivity::create([
            'name'      => auth()->user()->name,
            'activity'  => 'Add Incomes From Project [ '.$income->project_id.' ]',
            'url'       => '/projects/'.$income->project_id
        ]);
        return new IncomeResource($income);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Income $income
     * @return \App\Http\Resources\IncomeResource
     */
    public function show(Request $request, Income $income)
    {

        if (in_array(auth()->user()->type,['admin','manager'])){
            if(auth()->user()->type == 'manager'){
                $user_role      = UserRole::where(['project_id' => $income->project_id , 'user_id' => $income->user_id])->first();
                if($user_role){
                    $role           = Role::where('id', $user_role->role_id)->first();
                    if ($role->name == 'finacial_manager'){
                        return response()->json(['message' => 'Sorry But You Are Not Allowed To Create News'],400);
                    }
                }else{
                    return response()->json(['message' => 'Sorry But You Are Not Included In This Project'],400);
                }
            }
        }

        $income->load('media');
        return new IncomeResource($income);
    }

    /**
     * @param \App\Http\Requests\IncomeUpdateRequest $request
     * @param \App\Models\Income $income
     * @return \App\Http\Resources\IncomeResource
     */
    public function update(UpdateIncomeRequest $request, Income $income)
    {

        if (in_array(auth()->user()->type,['admin','manager'])){
            if(auth()->user()->type == 'manager'){
                $user_role      = UserRole::where(['project_id' => $income->project_id , 'user_id' => $income->user_id])->first();
                if($user_role){
                    $role           = Role::where('id', $user_role->role_id)->first();
                    if ($role->name == 'finacial_manager'){
                        return response()->json(['message' => 'Sorry But You Are Not Allowed To Create News'],400);
                    }
                }else{
                    return response()->json(['message' => 'Sorry But You Are Not Included In This Project'],400);
                }
            }
        }
        $income->fill($request->validated());

        $income->save();

        $images = collect($request->images)->map(function ($image) {
            return ['path' => $image, 'type' => 'image'];
        });


        $income->media()->createMany($images);
        UserActivity::create([
            'name'      => auth()->user()->name,
            'activity'  => 'Update Incomes From Project [ '.$income->project_id.' ]',
            'url'       => '/projects/'.$income->project_id
        ]);
        return new IncomeResource($income);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Income $income
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Income $income)
    {

        if (in_array(auth()->user()->type,['admin','manager'])){
            if(auth()->user()->type == 'manager'){
                $user_role      = UserRole::where(['project_id' => $income->project_id , 'user_id' => $income->user_id])->first();
                if($user_role){
                    $role           = Role::where('id', $user_role->role_id)->first();
                    if ($role->name == 'finacial_manager'){
                        return response()->json(['message' => 'Sorry But You Are Not Allowed To Create News'],400);
                    }
                }else{
                    return response()->json(['message' => 'Sorry But You Are Not Included In This Project'],400);
                }
            }   
            UserActivity::create([
                'name'      => auth()->user()->name,
                'activity'  => 'Delete Incomes From Project [ '.$income->project_id.' ]',
                'url'       => '/projects/'.$income->project_id
            ]);
            $income->delete();
            return response()->noContent();
        }else{
                return response()->json(['message' => 'Not Allowed'],404);
        }
    }
}
