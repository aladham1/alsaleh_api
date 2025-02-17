<?php

namespace App\Http\Controllers;

use App\Http\Filters\ExpenseFilters;
use App\Http\Requests\ExpenseStoreRequest;
use App\Http\Requests\ExpenseUpdateRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\UserRole;
use App\Models\Role;
use App\Models\UserActivity;

use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ExpenseResource
     */
    public function index(ExpenseFilters $expenseFilters)
    {
        if (\request()->per_page > 0) {
            $expenses = Expense::with('media')->filterBy($expenseFilters)->paginate(request()->per_page);
        }else{
            $expenses = Expense::with('media')->filterBy($expenseFilters)->get();

        }
        return  ExpenseResource::collection($expenses);
    }

    /**
     * @param \App\Http\Requests\ExpenseStoreRequest $request
     * @return \App\Http\Resources\ExpenseResource
     */
    public function store(ExpenseStoreRequest $request)
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
        $expense = Expense::create($request->validated());
        $images = collect($request->images)->map(function ($image) {
            return ['path' => $image, 'type' => 'image'];
        });


        $expense->media()->createMany($images);

        UserActivity::create([
            'name'      => auth()->user()->name,
            'activity'  => 'Add Expenses To Project [ '.$request->project_id.' ]',
            'url'       => '/projects/'.$request->project_id
        ]);
        return new ExpenseResource($expense);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Expense $expense
     * @return \App\Http\Resources\ExpenseResource
     */
    public function show(Request $request, Expense $expense)
    {

        if (in_array(auth()->user()->type,['admin','manager'])){
            if(auth()->user()->type == 'manager'){
                $user_role      = UserRole::where(['project_id' => $expense->project_id , 'user_id' => $expense->user_id])->first();
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
        $expense->load('media');
        return new ExpenseResource($expense);
    }

    /**
     * @param \App\Http\Requests\ExpenseUpdateRequest $request
     * @param \App\Models\Expense $expense
     * @return \App\Http\Resources\ExpenseResource
     */
    public function update(ExpenseUpdateRequest $request, Expense $expense)
    {

        if (in_array(auth()->user()->type,['admin','manager'])){
            if(auth()->user()->type == 'manager'){
                $user_role      = UserRole::where(['project_id' => $expense->project_id , 'user_id' => $expense->user_id])->first();
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
        $expense->update($request->validated());


        $images = collect($request->images)->map(function ($image) {
            return ['path' => $image, 'type' => 'image'];
        });


        $expense->media()->createMany($images);
        UserActivity::create([
            'name'      => auth()->user()->name,
            'activity'  => 'Update Expenses To Project [ '.$expense->project_id.' ]',
            'url'       => '/projects/'.$expense->project_id.'/expenses/'
        ]);
        return new ExpenseResource($expense);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Expense $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Expense $expense)
    {
        if (in_array(auth()->user()->type,['admin','manager'])){
            if(auth()->user()->type == 'manager'){
                $user_role      = UserRole::where(['project_id' => $expense->project_id , 'user_id' => $expense->user_id])->first();
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
                'activity'  => 'Delete Expenses From Project [ '.$expense->project_id.' ]',
                'url'       => '/projects/'.$expense->project_id.'/expenses/'
            ]);
            $expense->delete();
            return response()->noContent();
        }else{
                return response()->json(['message' => 'Not Allowed'],404);
        }
    }
}
