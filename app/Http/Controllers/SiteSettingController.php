<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Redirect;
use App\Models\SiteSetting;

class SiteSettingController extends Controller
{
    

	public function __construct(){
        parent::__construct();       
    }


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{			

		$site_settings = SiteSetting::findOrFail($id);				
		//dd($banners);
		return View('admin.site_settings.edit',compact('site_settings'));
		
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$data=request()->except('_method','_token');		
		
		$validator=Validator::make($data,
			array(				
				'name'=>'required',
				'email'=>'required|email',
				'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
				'pdf_header_img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
				'pdf_footer_img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',				
				)
		);

		if($validator->fails()){
			return Redirect::route("site_settings.edit",$id)
					->withErrors($validator)
					->withInput();
		}	

		//dd($data);
		$imageName=null;
		$file=request()->file('logo');	
		$old_image_name = $data['old_image'];	
			if($file != null){				
				$imageName = time().'_'.get_file_name($file->getClientOriginalName());				
				$data['logo']=$imageName;						
		}

		$pdf_header_img=null;
		$file_header_img=request()->file('pdf_header_img');	
		$old_pdf_header_img = $data['old_pdf_header_img'];	
			if($file_header_img != null){				
				$pdf_header_img = time().'_'.get_file_name($file_header_img->getClientOriginalName());				
				$data['pdf_header_img']=$pdf_header_img;						
		}

		$pdf_footer_img=null;
		$file_footer_img=request()->file('pdf_footer_img');	
		$old_pdf_footer_img = $data['old_pdf_footer_img'];	
			if($file_footer_img != null){				
				$pdf_footer_img = time().'_'.get_file_name($file_footer_img->getClientOriginalName());				
				$data['pdf_footer_img']=$pdf_footer_img;						
		}

		if (request()->has('pdf_no_header_footer')) {
		    $data['pdf_no_header_footer'] = 1;
		}else{
			$data['pdf_no_header_footer'] = 0;
		}
		
		
		unset($data['old_image']);
		unset($data['old_pdf_header_img']);
		unset($data['old_pdf_footer_img']);	
		$site_settings_data = SiteSetting::where('id',$id)->update($data);		

		if($site_settings_data){													
				if($imageName!=null){
					if($old_image_name !=null){
						\Storage::delete('admin/site_settings/'.$old_image_name);
					}
					//dd();
					$file->storeAs('admin/site_settings',$imageName);					
				}
				if($pdf_header_img!=null){
					if($old_pdf_header_img !=null){
						\Storage::delete('admin/site_settings/'.$old_pdf_header_img);
					}
					$file_header_img->storeAs('admin/site_settings',$pdf_header_img);					
				}
				if($pdf_footer_img!=null){
					if($old_pdf_footer_img !=null){
						\Storage::delete('admin/site_settings/'.$old_pdf_footer_img);
					}
					$file_footer_img->storeAs('admin/site_settings',$pdf_footer_img);					
				}

				$message="You have successfully updated";
				return redirect()->route('site_settings.edit',$id)
					->with('flash_success',$message);
		}else{

			$message="You don't update anything";
				return redirect()->route('site_settings.edit',$id)
					->with('flash_warning',$message);
		}			
	}
}
