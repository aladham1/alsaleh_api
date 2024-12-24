<?php

namespace App\Http\Controllers;

use App\Http\Filters\ProjectFilters;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class OPController extends Controller
{
    public function show(Project $project)
    {
        $project->load(['media',
                        'managers' => function ($q) {
                                                    $q->with('roles');
                                                    },
                        'comments'
                      ]);
        return new ProjectResource($project);
    }
}
