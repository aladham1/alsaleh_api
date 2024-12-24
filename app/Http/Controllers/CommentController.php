<?php

namespace App\Http\Controllers;

use App\Http\Filters\CommentFilters;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Project;
use App\Models\UserRole;
use App\Models\Role;
use App\Models\UserActivity;

use Illuminate\Http\Request;


class CommentController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\CommentResource
     */
    public function index(CommentFilters $commentFilters)
    {
        $comments = Comment::filterBy($commentFilters)->with('user')->paginate();
        return  CommentResource::collection($comments);
    }

    /**
     * @param \App\Http\Requests\CommentStoreRequest $request
     * @return \App\Http\Resources\CommentResource
     */
    public function store(CommentStoreRequest $request)
    {

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'project_id' => $request->project_id,
            'content' => $request->content
        ]);
        
        $comment->load('user');
        return new CommentResource($comment);
    }



    /**
     * @param \App\Http\Requests\CommentUpdateRequest $request
     * @param \App\Models\Comment $comment
     * @return \App\Http\Resources\CommentResource
     */
    public function update(CommentUpdateRequest $request, Comment $comment)
    {
        if (in_array(auth()->user()->type,['admin','manager'])){
            if(auth()->user()->type == 'admin'){
                $comment->update($request->validated());
                UserActivity::create([
                    'name'      => auth()->user()->name,
                    'activity'  => 'Update Comment [ '.$comment->id.' ] In Project [ '.$comment->project_id.' ]',
                    'url'       => '/projects/'.$comment->project_id
                ]);

                return new CommentResource($comment);
            }else{
                    $user_role      = UserRole::where(['project_id' => $comment->project_id , 'user_id' => $comment->user_id])->first();
                    $role           = Role::where('id', $user_role->role_id)->first();
                if ($role->name == 'finacial_manager'){
                    return response()->json(['message' => 'Sorry But You Are Not Allowed To Update Comments'],400);
                }

                UserActivity::create([
                    'name'      => auth()->user()->name,
                    'activity'  => 'Update Comment [ '.$comment->id.' ] In Project [ '.$comment->project_id.' ]',
                    'url'       => '/projects/'.$comment->project_id
                ]);
                $comment->update($request->validated());
                return new CommentResource($comment);
            }
        }
        return response()->json(['message' => 'Sorry But You Are Not Allowed To Make This'],404);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Comment $comment)
    {

        if (in_array(auth()->user()->type,['admin','manager'])){
            if(auth()->user()->type == 'admin'){
                UserActivity::create([
                    'name'      => auth()->user()->name,
                    'activity'  => 'Delete Comment [ '.$comment->id.' ] From Project [ '.$comment->project_id.' ]',
                    'url'       => '/projects/'.$comment->project_id
                ]);
                $comment->delete();
                return response()->noContent();
            }else{
                $user_role      = UserRole::where(['project_id' => $comment->project_id , 'user_id' => $comment->user_id])->first();
                $role           = Role::where('id', $user_role->role_id)->first();
                if ($role->name == 'finacial_manager'){
                    return response()->json(['message' => 'Sorry But You Are Not Allowed To Update Comments'],400);
                }
                UserActivity::create([
                    'name'      => auth()->user()->name,
                    'activity'  => 'Delete Comment [ '.$comment->id.' ] From Project [ '.$comment->project_id.' ]',
                    'url'       => '/projects/'.$comment->project_id
                ]);
                $comment->delete();
                return response()->noContent();
            }
        }
        return response()->json(['message' => 'Sorry But You Are Not Allowed To Make This'],404);
    }
}
