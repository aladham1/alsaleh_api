<?php

namespace App\Http\Controllers;

use App\Http\Filters\ManagerFilters;
use App\Http\Requests\ManagerStoreRequest;
use App\Http\Requests\ManagerUpdateRequest;
use App\Http\Resources\DonorsResource;
use App\Http\Resources\ManagerResource;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Models\UserActivity;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class DonorController extends Controller
{
     /**
      * @param \Illuminate\Http\Request $request
      * @return \App\Http\Resources\ManagerResource
      */
     public function index(ManagerFilters $managerFilters)
     {
          $donors = User::withCount('projects')->whereType('visitor')->filterBy($managerFilters)->paginate();

          return DonorsResource::collection($donors);
     }
     public function donorsRequests(ManagerFilters $managerFilters)
     {
          $donors = User::whereType('visitor')->whereApproved(0)->filterBy($managerFilters)->paginate();

          return DonorsResource::collection($donors);
     }


     /**
      * @param \Illuminate\Http\Request $request
      * @param \App\Models\User $user
      */
     public function show($id)
     {
         $user = User::with('projectsDonors')->where('id', $id)->first();
          return new DonorsResource($user);
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
                    'type'     => 'visitor',
                    'avatar'   => $avatar,
                    'password' => $request->password
               ]
          );
          $manager = User::create($data);
         if ($request->projects) {
             $projectsIds = explode(',', $request['projects']);
             $projectsIds = array_filter($projectsIds, 'is_numeric');
             $manager->projectsDonors()->sync($projectsIds);
         }
          return new DonorsResource($manager);
     }

     /**
      * @param \App\Http\Requests\ManagerStoreRequest $request
      * @return \App\Http\Resources\ManagerResource
      */
     public function update(ManagerUpdateRequest $request, $id)
     {
         $manager = User::find($id);
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
                         'password' => request()->password,
                         ]);
               }else{
                    return response()->json(['message' => 'You Are Not Allowed To Change Your Password'],404);
               }
          }
         if ($request->projects) {
             $projectsIds = explode(',', $request['projects']);
             $projectsIds = array_filter($projectsIds, 'is_numeric');
             $manager->projectsDonors()->sync($projectsIds);
         }
          return new DonorsResource($manager);
     }

    public function destroy($id)
    {
        $manager = User::where('id', $id)->first();
        $use_type = auth()->user()->type;
//        if ($use_type != 'admin'){
//            return new JsonResponse([
//                "message" => "You Are Not Allowed"
//            ], 404);
//        }
        $manager->delete();
        return response()->noContent();
    }
}
