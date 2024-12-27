<?php

namespace App\Http\Controllers;

use App\Http\Filters\ProjectFilters;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Resources\ProjectBoxResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ManagerResource;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ProjectController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ProjectResource
     */
    public function index(ProjectFilters $projectFilters)
    {
        if (\request()->inHome){
            $projects = Project::with('media')->filterBy($projectFilters)->limit(3)
                ->select('id', 'title', DB::raw("SUBSTRING_INDEX(description, ' ', 20) as summary"))
                ->get();
            return ProjectBoxResource::collection($projects);

        }
        $projects = Project::with(['managers' => function ($q) {
            $q->with('roles');
        }, 'media'])->filterBy($projectFilters);
        $user = auth()->user();

        if ($user && $user->type == 'manager') {
            $projects->whereHas('managers', function ($query) {
                $query->where('user_roles.user_id', auth()->id());
            });
        }

        if (\request()->no_pg){
            $projects = $projects->get();
        }else{
            $projects = $projects->paginate(request('per_page', 10));
        }

        return ProjectResource::collection($projects);
    }
    public function myProjects(ProjectFilters $projectFilters)
    {
        $projects = Project::whereHas('donors', function ($query) {
            $query->where('user_id', auth()->id());
        })->with(['managers' => function ($q) {
            $q->with('roles');
        }, 'media'])->filterBy($projectFilters);
        $user = auth()->user();

        if ($user && $user->type == 'manager') {
            $projects->whereHas('managers', function ($query) {
                $query->where('user_roles.user_id', auth()->id());
            });
        }

        if (\request()->no_pg){
            $projects = $projects->get();
        }else{
            $projects = $projects->paginate(request('per_page', 10));
        }
        return ProjectResource::collection($projects);
    }

    /**
     * @param \App\Http\Requests\ProjectStoreRequest $request
     * @return ProjectResource|JsonResponse
     */
    public function store(ProjectStoreRequest $request)
    {
        $use_type = auth()->user()->type;
        if ($use_type != 'admin') {
            return new JsonResponse([
                "message" => "You Are Not Authorized"
            ], 404);
        }

        $avatar = null;
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar')->store('avatars');
        }
        $project = Project::create(array_merge($request->validated(), ['avatar' => $avatar, 'country' => 'الكويت', 'city' => 'الكويت', 'gov' => 'الكويت']));
        if ($request->donors) {
            $donorIds = explode(',', $request['donors']);
            $donorIds = array_filter($donorIds, 'is_numeric');
            $project->donors()->sync($donorIds);
        }

        $images = collect($request->images)->map(function ($image) {
            return ['path' => $image, 'type' => 'image'];
        });
        $videos = collect($request->videos)->map(function ($video) {
            return ['path' => $video, 'type' => 'video'];
        });

        $project->media()->createMany($images);
        $project->media()->createMany($videos);

        if ($request->super_manager != 'null') {
            $project->managers()->attach([$request->super_manager => ['role_id' => 4]]);
        }

        if ($request->general_manager != 'null') {
            $project->managers()->attach([$request->general_manager => ['role_id' => 5]]);
        }

        if ($request->financial_manager != 'null') {
            $project->managers()->attach([$request->financial_manager => ['role_id' => 6]]);
        }

        if ($request->media_manager != 'null') {
            $project->managers()->attach([$request->media_manager => ['role_id' => 7]]);
        }

        return new ProjectResource($project->load('managers'));
    }

    /**
     * @param \App\Models\Project $project
     * @return \App\Http\Resources\ProjectResource
     */
    public function show(Project $project)
    {
        $project->load(['media', 'comments', 'managers','donors']);
        $project->incomes = $project->incomes()->sum('total');
        $project->expenses = $project->expenses()->sum('total');
        return new ProjectResource($project);
    }

    /**
     * @param \App\Http\Requests\ProjectUpdateRequest $request
     * @param \App\Models\Project $project
     * @return \App\Http\Resources\ProjectResource
     */


    public function update(ProjectUpdateRequest $request, Project $project)
    {
        $user = auth()->user();
        if ($user->type == 'admin') {
            $role = 'admin';
        } else {
            $user_role = UserRole::where(['user_id' => $user->id, 'project_id' => $project->id])->first();
            $role = Role::where('id', $user_role)->value('name');
        }

        if ($role == 'admin') {
            $this->admin($request, $project);
            UserActivity::create([
                'name' => auth()->user()->name,
                'activity' => 'Update Project [ ' . $project->id . ' ] As An Admin Manager',
                'url' => '/projects/' . $project->id
            ]);
        } elseif ($role == 'super_manager') {
            $this->superManager($request, $project);
            UserActivity::create([
                'name' => auth()->user()->name,
                'activity' => 'Update Project [ ' . $project->id . ' ] As A Super Manager',
                'url' => '/projects/' . $project->id
            ]);

        } elseif ($role == 'general_manager') {
            $this->generalManager($request, $project);
            UserActivity::create([
                'name' => auth()->user()->name,
                'activity' => 'Update Project [ ' . $project->id . ' ] As A General Manager',
                'url' => '/projects/' . $project->id
            ]);

        } elseif ($role == 'financial_manager') {
            $this->financialManager($request, $project);
            UserActivity::create([
                'name' => auth()->user()->name,
                'activity' => 'Update Project [ ' . $project->id . ' ] As A Financial Manager',
                'url' => '/projects/' . $project->id
            ]);

        } elseif ($role == 'media_manager') {
            $this->mediaManager($request, $project);
            UserActivity::create([
                'name' => auth()->user()->name,
                'activity' => 'Update Project [ ' . $project->id . ' ] As A Media Manager',
                'url' => '/projects/' . $project->id
            ]);

        }
        // $project->super_manager 	= $project->managers()->where('role_id', 4)->first();
        // $project->general_manager 	= $project->managers()->where('role_id', 5)->first();
        // $project->financial_manager	= $project->managers()->where('role_id', 6)->first();
        // $project->media_manager 	= $project->managers()->where('role_id', 7)->first();
        $user_role = $project->roles()->where('user_id', auth()->user()->id)->first();
        if (!$user_role) {
            $role = 'admin';
        } else {
            $role = Role::where('id', $user_role->id)->get();
            $project->role = $role->name;
        }
        if ($request->donors) {
            $donorIds = explode(',', $request['donors']);
            $donorIds = array_filter($donorIds, 'is_numeric');
            $project->donors()->sync($donorIds);
        }
        $project->load('managers');


        return new ProjectResource($project);
    }

    /**
     * @param \App\Models\Project $project
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $use_type = auth()->user()->type;
        if ($use_type != 'admin') {
            return new JsonResponse([
                "message" => "You Are Not Allowed"
            ], 404);
        }
        UserActivity::create([
            'name' => auth()->user()->name,
            'activity' => 'Delete Project [ ' . $project->id . ' ]',
        ]);
        $project->delete();

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Project $project)
    {
        $project->update(['status' => $project->status == 'archived' ? 'active' : 'archived']);
        UserActivity::create([
            'name' => auth()->user()->name,
            'activity' => 'Archived Project [ ' . $project->id . ' ]',
            'url' => '/projects/' . $project->id
        ]);
        return response()->noContent();
    }

    private function admin($request, $project)
    {

        $avatar = $project->avatar;

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar')->store('avatars');
        }

        $project->update(
            array_merge($request->validated(), ['avatar' => $avatar])
        );

        $images = collect($request->images)->map(function ($image) {
            return ['path' => $image, 'type' => 'image'];
        });
        $videos = collect($request->videos)->map(function ($video) {
            return ['path' => $video, 'type' => 'video'];
        });

        $project->media()->createMany($images);
        $project->media()->createMany($videos);

        if ($request->super_manager != 'null') {
            $role = Role::where('name', 'super_manager')->first();
            $old_sm = UserRole::where(['project_id' => $project->id, 'role_id' => $role->id])->first();
            if ($old_sm) {
                $project->managers()->detach($old_sm->user_id);
            }
            $project->managers()->attach([$request->super_manager => ['role_id' => $role->id]]);
        }

        if ($request->general_manager != 'null') {
            $role = Role::where('name', 'general_manager')->first();
            $old_gm = UserRole::where(['project_id' => $project->id, 'role_id' => $role->id])->first();
            if ($old_gm) {
                $project->managers()->detach($old_gm->user_id);
            }
            $project->managers()->attach([$request->general_manager => ['role_id' => $role->id]]);
        }

        if ($request->financial_manager != 'null') {
            $role = Role::where('name', 'financial_manager')->first();
            $old_fm = UserRole::where(['project_id' => $project->id, 'role_id' => $role->id])->first();
            if ($old_fm) {
                $project->managers()->detach($old_fm->user_id);
            }
            $project->managers()->attach([$request->financial_manager => ['role_id' => $role->id]]);
        }

        if ($request->media_manager != 'null') {
            $role = Role::where('name', 'media_manager')->first();
            $old_mm = UserRole::where(['project_id' => $project->id, 'role_id' => $role->id])->first();
            if ($old_mm) {
                $project->managers()->detach($old_mm->user_id);
            }

            $project->managers()->attach([$request->media_manager => ['role_id' => $role->id]]);
        }
    }

    private function superManager($request, $project)
    {

        $project->update($request->except(['name', 'total_requested', 'avatar', 'super_manager']));

        $images = collect($request->images)->map(function ($image) {
            return ['path' => $image, 'type' => 'image'];
        });
        $videos = collect($request->videos)->map(function ($video) {
            return ['path' => $video, 'type' => 'video'];
        });

        $project->media()->createMany($images);
        $project->media()->createMany($videos);

        if ($request->general_manager != 'null') {
            $role = Role::where('name', 'general_manager')->first();
            $old_gm = UserRole::where(['project_id' => $project->id, 'role_id' => $role->id])->first();
            if ($old_gm) {
                $project->managers()->detach($old_gm->user_id);
            }

            $project->managers()->attach([$request->general_manager => ['role_id' => $role->id]]);
        }

        if ($request->financial_manager != 'null') {
            $role = Role::where('name', 'financial_manager')->first();
            $old_fm = UserRole::where(['project_id' => $project->id, 'role_id' => $role->id])->first();
            if ($old_fm) {
                $project->managers()->detach($old_fm->user_id);
            }

            $project->managers()->attach([$request->financial_manager => ['role_id' => $role->id]]);
        }

        if ($request->media_manager != 'null') {
            $role = Role::where('name', 'media_manager')->first();
            $old_mm = UserRole::where(['project_id' => $project->id, 'role_id' => $role->id])->first();
            if ($old_mm) {
                $project->managers()->detach($old_mm->user_id);
            }
            $project->managers()->attach([$request->media_manager => ['role_id' => $role->id]]);
        }
    }

    private function generalManager($request, $project)
    {
        $project->update($request->except(['name', 'total_requested',
            'avatar', 'super_manager', 'general_manager']));

        $images = collect($request->images)->map(function ($image) {
            return ['path' => $image, 'type' => 'image'];
        });
        $videos = collect($request->videos)->map(function ($video) {
            return ['path' => $video, 'type' => 'video'];
        });

        $project->media()->createMany($images);
        $project->media()->createMany($videos);

        if ($request->financial_manager != 'null') {

            $role = Role::where('name', 'financial_manager')->first();
            $old_fm = UserRole::where(['project_id' => $project->id, 'role_id' => $role->id])->first();
            if ($old_fm) {
                $project->managers()->detach($old_fm->user_id);
            }

            $project->managers()->attach([$request->financial_manager => ['role_id' => $role->id]]);
        }

        if ($request->media_manager != 'null') {

            $role = Role::where('name', 'media_manager')->first();
            $old_mm = UserRole::where(['project_id' => $project->id, 'role_id' => $role->id])->first();
            if ($old_mm) {
                $project->managers()->detach($old_mm->user_id);
            }
            $project->managers()->attach([$request->media_manager => ['role_id' => $role->id]]);
        }
    }

    private function financialManager($request, $project)
    {
        $project->update($request->except(['name', 'total_requested', 'avatar',
            'super_manager', 'general_manager', 'financial_manager', 'media_manager',
            'images', 'videos']));
    }

    private function mediaManager($request, $project)
    {
        $project->update($request->only(['images', 'videos']));

        $images = collect($request->images)->map(function ($image) {
            return ['path' => $image, 'type' => 'image'];
        });
        $videos = collect($request->videos)->map(function ($video) {
            return ['path' => $video, 'type' => 'video'];
        });
        $project->media()->createMany($images);
        $project->media()->createMany($videos);
    }
}
