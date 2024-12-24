<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCmsRequest;
use App\Http\Requests\StoreAboutRequest;
use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\StoreTitleRequest;
use App\Http\Requests\StoreLogoRequest;
use App\Models\User;
use App\Models\Project;
use App\Http\Resources\CmsResource;
use App\Http\Resources\AboutResource;
use App\Http\Resources\LinkResource;
use App\Http\Resources\TitleResource;
use App\Http\Resources\LogoResource;

class CMSController extends Controller
{
    public function home(StoreCmsRequest $request){

	$user = User::where('id',1)->first();
		if($request->has('projects') && $request->projects != [] ){
		foreach($request->projects as $project){
			if(Project::find($project)->in_home == 1){
				Project::find($project)->update(['in_home' => 0]);
			}else{
				Project::find($project)->update(['in_home' => 1]);
			}
		}}
		$user->update(['cms' => [
			'testimonials' => $request->testimonials,
			'about_us'     => $request->about_us,
			'projects'     => $request->projects
			],
		]);
    $getUser = User::where('id', 1)->first();
    return new CmsResource($getUser->cms);
    }


    public function aboutUs(StoreAboutRequest $request){
		$user = User::where('id', 1);
		$data = ['title' => $request->title,'description' => $request->description];
		$user->update(['about_us' => $data]);
		return new AboutResource($request);
    }

	public function links(StoreLinkRequest $request){
		$user = User::where('id', 1);
		$data = $request->links;
		$user->update(['links' => $data]);
		return new LinkResource($request->links);
	}
	
	public function titles(StoreTitleRequest $request){
		$user = User::where('id', 1)->first();
		$user->update(['titles' => [
							'projects' => $request->projects,
							'about_us' => $request->about_us,
							'archive'  => $request->archive
					 ]]);
		return new TitleResource($user->titles);
	}

    public function logos(StoreLogoRequest $request){
		$user = User::where('id', 1)->first();
		if ($request->hasFile('visitor_logo')) {
			$visitor_path = $request->file('visitor_logo')->store('logos');
	   	}else{
			$visitor_path = $request->visitor_logo;
		}

	   if ($request->hasFile('admin_logo')) {
			$admin_path = $request->file('admin_logo')->store('logos');
   		}else{
			$admin_path = $request->admin_logo;
		}

		$user->update(['logos' => [
								'visitor_title' => $request->visitor_title,
								'visitor_logo' 	=> $visitor_path,
								'admin_logo'  	=> $admin_path
						]]);
		
	 		return new LogoResource($user->logos);
	}

	public function showHome(){
		$user = User::where('id',1)->first();
		if($user->cms){
		return new CmsResource($user->cms);
	}
		else{return response()->json(['message' => "You Don't Have Any CMS Right NOW"],404);}
    }
   public function showAbout(){
	$user = User::where('id',1)->first();
	return new AboutResource($user->about_us);
   }

   public function showLinks(){
	$user = User::where('id',1)->first();
	return new LinkResource($user->links);
   }

   public function showTitles(){
	$user = User::where('id',1)->first();
	if($user->titles == []){
		$user->update(['titles' => [
			'projects' => "",
			'about_us' => "",
			'archive'  => ""
	 ]]);
	}
	return new TitleResource($user->titles);
   }


   public function showLogos(){
	$user = User::where('id',1)->first();
	if($user->logos == []){
		$user->update(['logos' => [
			'visitor_title' => "",
			'visitor_logo' 	=> "",
			'admin_logo'  	=> ""
		]]);
	}
	return new LogoResource($user->logos);
   }
}
