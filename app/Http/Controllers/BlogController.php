<?php

namespace App\Http\Controllers;

use App\Http\Filters\BlogFilters;
use App\Http\Requests\BlogStoreRequest;
use App\Http\Requests\BlogUpdateRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Models\UserRole;
use App\Models\Role;
use App\Models\UserActivity;

use Illuminate\Http\Request;


class BlogController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\BlogCollection
     */
    public function index(BlogFilters $blogFilters)
    {
        $blogs = Blog::with(['media','user'])->filterBy($blogFilters)->paginate();

        return BlogResource::collection($blogs);
    }

    /**
     * @param \App\Http\Requests\BlogStoreRequest $request
     * @return \App\Http\Resources\BlogResource
     */
    public function store(BlogStoreRequest $request)
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
        $blog = Blog::create([
            'title'      => $request->title,
            'content'    => $request->content,
            'project_id' => $request->project_id,
            'user_id'    => auth()->user()->id,
        ]);

        $images = collect($request->images)->map(function ($image) {
            return ['path' => $image, 'type' => 'image'];
        });
        $videos = collect($request->videos)->map(function ($video) {
            return ['path' => $video, 'type' => 'video'];
        });
      
        $blog->media()->createMany($images);
        $blog->media()->createMany($videos);
        UserActivity::create([
            'name'      => auth()->user()->name,
            'activity'  => 'Add News To Project [ '.$request->project_id.' ]',
            'url'       => '/projects/'.$request->project_id.'/news'
        ]);
        
        return new BlogResource($blog->load('user'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Blog $blog
     * @return \App\Http\Resources\BlogResource
     */
    public function show(Request $request, Blog $blog)
    {
        $blog->load(['media','user']);
        return new BlogResource($blog);
    }

    /**
     * @param \App\Http\Requests\BlogUpdateRequest $request
     * @param \App\Models\Blog $blog
     * @return \App\Http\Resources\BlogResource
     */
    public function update(BlogUpdateRequest $request, Blog $blog)
    {
        if (in_array(auth()->user()->type,['admin','manager'])){
            if(auth()->user()->type == 'manager'){
                $user_role      = UserRole::where(['project_id' => $blog->project_id , 'user_id' => $blog->user_id])->first();
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

        $blog->update($request->validated());
        $images = collect($request->images)->map(function ($image) {
            return ['path' => $image, 'type' => 'image'];
        });
        $videos = collect($request->videos)->map(function ($video) {
            return ['path' => $video, 'type' => 'video'];
        });
        $blog->media()->createMany($images);
        $blog->media()->createMany($videos);

        UserActivity::create([
            'name'      => auth()->user()->name,
            'activity'  => 'Update News [ '.$blog->id.' ] To Project [ '.$blog->project_id.' ]',
            'url'       => '/projects/'.$blog->project_id.'/news'
        ]);

        return new BlogResource($blog->load('user'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Blog $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Blog $blog)
    {
         if (in_array(auth()->user()->type,['admin','manager'])){
            if(auth()->user()->type == 'manager'){
                $user_role      = UserRole::where(['project_id' => $blog->project_id , 'user_id' => $blog->user_id])->first();
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
                'activity'  => 'Delete News [ '.$blog->id.' ] To Project [ '.$blog->project_id.' ]',
                'url'       => '/projects/'.$blog->project_id.'/news'
            ]);
            $blog->delete();
            return response()->noContent();
        }else{
                return response()->json(['message' => 'Not Allowed'],404);
        }
    }
}
