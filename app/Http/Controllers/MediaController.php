<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Dilab\Network\SimpleRequest;
use Dilab\Network\SimpleResponse;
use Dilab\Resumable;

use Illuminate\Support\Facades\File;
use App\Models\UserActivity;
use App\Models\Project;
class MediaController extends Controller
{



    public function store()
    {
        $tmpPath    = storage_path() . '/tmp';
        $uploadPath = storage_path('app/public/media');
        if (!File::exists($tmpPath)) {
            File::makeDirectory($tmpPath, $mode = 0777, true, true);
        }

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, $mode = 0777, true, true);
        }

        $simpleRequest              = new SimpleRequest();
        $simpleResponse             = new SimpleResponse();

        $resumable                  = new Resumable($simpleRequest, $simpleResponse);
        $resumable->tempFolder      = $tmpPath;
        $resumable->uploadFolder    = $uploadPath;

        $result = $resumable->process();

        switch ($result) {
            case 200:
                return response([
                    'message' => 'OK',
                ], 200);
                break;
            case 201:
                return response([
                    'message' => 'OK',
                ], 200);
                break;
            case 204:
                return response([
                    'message' => 'Chunk not found',
                ], 204);
                break;
            default:
                return response([
                    'message' => 'An error occurred',
                ], 404);
        }
        UserActivity::create([
            'name'      => auth()->user()->name,
            'activity'  => 'Add Media From Project [ '.$media->mediable.' ]',
            'url'       => '/projects/'.$media->mediable
        ]);
    }


    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Media $media
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Media $media)
    {
        $name = explode('\\',$media->mediable_type);
        UserActivity::create([
            'name'      => auth()->user()->name,
            'activity'  => 'Delete Media From '.end($name).' [ '.$media->mediable_id.' ]',
            'url'       => '/'.strtolower(end($name)).'s/'.$media->mediable_id
        ]);
        $media->delete();
        return response()->noContent();
    }
}
