<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->type == 'admin'){
            return RoleResource::collection(Role::all());
        }
        return response()->json(["message" => "Your Are Not An Admin"],404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoleRequest $request)
    {
        if (auth()->user()->type == 'admin'){
            return new RoleResource(Role::create($request->all()));
        }
        return response()->json(["message" => "Your Are Not An Admin"],404);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        if (auth()->user()->type == 'admin'){
            return new RoleResource($role);
        }
        return response()->json(["message" => "Your Are Not An Admin"],404);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRoleRequest  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        if (auth()->user()->type == 'admin'){
            $role->update([
                'name' => $request->name
            ]);
            return new RoleResource($role);
        }
        return response()->json(["message" => "Your Are Not An Admin"],404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        if (auth()->user()->type == 'admin'){
            $role->delete();
            return new JsonResponse(['message' => 'Role Deleted Successfully'], 200);
        }
        return response()->json(["message" => "Your Are Not An Admin"],404);
    }
}
