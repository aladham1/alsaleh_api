<?php

namespace App\Http\Controllers;

use App\Http\Filters\ManagerFilters;
use App\Http\Requests\ManagerStoreRequest;
use App\Http\Requests\ManagerUpdateRequest;
use App\Http\Resources\ManagerResource;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Models\UserActivity;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class ManagerController extends Controller
{
     /**
      * @param \Illuminate\Http\Request $request
      * @return \App\Http\Resources\ManagerResource
      */
     public function index(ManagerFilters $managerFilters)
     {
          $projects = User::withCount('projects')->whereType('manager')->filterBy($managerFilters)->paginate();

          return ManagerResource::collection($projects);
     }


     /**
      * @param \Illuminate\Http\Request $request
      * @param \App\Models\User $user
      * @return \App\Http\Resources\ManagerResource
      */
     public function show(User $manager)
     {
          return new ManagerResource($manager->load('roles'));
     }

     /**
      * @param \App\Http\Requests\ManagerStoreRequest $request
      * @return \App\Http\Resources\ManagerResource
      */
     public function store(ManagerStoreRequest $request)
     {
          $avatar = null;
          if ($request->hasFile('avatar')) {
               $avatar = $request->file('avatar')->store('avatars');
          }
          $data = array_merge(
               $request->validated(),
               [
                    'type'     => 'manager',
                    'avatar'   => $avatar,
                    'password' => Crypt::encryptString($request->password)
               ]
          );
          $manager = User::create($data);
          return new ManagerResource($manager);
     }

     /**
      * @param \App\Http\Requests\ManagerStoreRequest $request
      * @return \App\Http\Resources\ManagerResource
      */
     public function update(ManagerUpdateRequest $request, User $manager)
     {
          $avatar = $manager->avatar;

          if ($request->hasFile('avatar')) {
               $avatar = $request->file('avatar')->store('avatars');
          }

          $manager->update(
               array_merge($request->validated(), ['avatar' => $avatar])
          );

          if (request()->password){
               if (auth()->user()->type == 'admin'){
                    $manager->update([
                         'password' => Crypt::encryptString(request()->password),
                         ]);
               }else{
                    return response()->json(['message' => 'You Are Not Allowed To Change Your Password'],404);
               }
          }
          return new ManagerResource($manager);
     }

    public function destroy(User $manager)
    {
        $use_type = auth()->user()->type;
        if ($use_type != 'admin'){
            return new JsonResponse([
                "message" => "You Are Not Allowed"
            ], 404);
        }
        $manager->delete();
        return response()->noContent();
    }
}
