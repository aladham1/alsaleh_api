<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserActivity;
use App\Http\Resources\UserActivityResource;
use App\Http\Filters\ActivityFilters;
class UserActivityController extends Controller
{
     /**
      * @param \Illuminate\Http\Request $request
      * @return \App\Http\Resources\UserActivityResource
      */
      public function index(ActivityFilters $activityFilters)
      {
           $logs = UserActivity::filterBy($activityFilters)->orderBy('created_at','desc')->paginate(request('limite'));
           return UserActivityResource::collection($logs);
      }

}
