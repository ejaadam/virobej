<?php

namespace App\Http\Controllers\Seller;
use App\Http\Controllers\SupplierBaseController;
use App\Http\Controllers\MyImage;
use App\Models\Seller\Account;
use App\Models\Memberauth;
use App\Models\Commonsettings;
use App\Helpers\CommonNotifSettings;
use Config;
use Lang;
use Redirect;
use Session;
use Input;
use Response;
use File;
use Request; 
use CommonLib;

class AccountController extends SupplierBaseController
{
	public function __construct ()
	{
		parent::__construct();
		$this->imgObj = new MyImage();        
		$this->accObj = new Account();  
		$this->commonObj = new Commonsettings();	
	}   
	public function shipping_information ()
	{
		if (Request::ajax()) {
				$postdata = Input::all();    
		} else {
			return View('seller.account_settings.shipping_information');
		}
	}

	public function get_tax_info(){
		$data['id_proof']		= $this->accObj->get_IDProof();	
		$data['address_proof']	= $this->accObj->get_AdressProof();		
		$data['details']	= $this->accObj->get_tax_information($this->userSess->supplier_id,$this->userSess->account_type_id);
		$data['proof_name'] = $this->accObj->get_proof_name($this->userSess->supplier_id,$this->userSess->account_type_id);
		$data['path']		= $this->config->get('path.SELLER.PROOF_DETAILS.ORIGINAL');
		$data['fields'] 	= CommonNotifSettings::getHTMLValidation('seller.account-settings.tax-information');	
		$data['gst_fields'] = CommonNotifSettings::getHTMLValidation('seller.account-settings.gst-information');	
		$op['content']  	= View('seller.account_settings.tax_information', $data)->render();
		$op['status']  		= 'ok';
		return Response::json($op, Config('httperr.SUCCESS'), $this->headers, $this->options);
	}
    public function tax_information ()
    {
        $data = [];	
		if (Request::ajax()) {
			$op = [];
			$postdata = Input::all();
			$postdata['relative_post_id'] = $this->userSess->supplier_id;
            $postdata['account_type_id'] = $this->userSess->account_type_id;
			if(!empty($postdata['pan_card_upload'])){
				$attachment = $postdata['pan_card_upload'];
				$size = $attachment->getSize();
				if ($size < 2049133)
				{
					$filename = '';
					if ($attachment != '')
					{
						$folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';				

						$path = $this->config->get('path.SELLER.PROOF_DETAILS.LOCAL');
                        $org_path = $this->config->get('path.SELLER.PROOF_DETAILS.ORIGINAL');
                      
						if (File::exists($path.'/'.getGTZ('Y')))
						{
							if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($path.'/'.getGTZ('Y'));
							File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (File::exists($org_path.'/'.getGTZ('Y')))
						{
							if (!File::exists($org_path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($org_path.'/'.getGTZ('Y'));
							File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (in_array(strtolower($attachment->getClientOriginalExtension()), array('jpg', 'jpeg', 'png','pdf')))
						{
							$org_name = $attachment->getClientOriginalName();
							$ext = $attachment->getClientOriginalExtension();
							$file_extentions = strtolower($ext);
							$filtered_name = $this->slug($org_name);
							$file_name = explode('_', $filtered_name);
							$file_name = $file_name[0];
							$file_name = $file_name.'.'.$ext;							
							$filename = getGTZ('dmYHis').$file_name;
							
							$uploaded_file = $attachment->move($org_path.$folder_path, $filename);
                            $this->imgObj->imageresize($org_path.$folder_path.$filename, $path.$folder_path.$filename, 200, 200);
							if ($uploaded_file) {								
								$postdata['details']['pan_card_image'] = $folder_path.$filename;
							}
						}
						else
						{
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							$op['msg'] = 'Invalid Profile ImageFile Formate';
							return Response::json($op, $this->statusCode, $this->headers, $this->options);
						}
					}					
				}
				else
				{					
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					$op['msg'] = 'Document size must be less than 2 MB';
					return Response::json($op, $this->statusCode, $this->headers, $this->options);
				}				
			}
			$res = $this->accObj->UpdateTax_info($postdata);					
			if ($res) 
			{		       
                $this->accObj->UpdateCompletedSteps(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.TAX_INFO'),
													'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),
													'supplier_id'=>$this->supplier_id,
													'account_id'=>$this->account_id]); 
				$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
                $op['msg'] = 'Tax Information Updated Successfully';
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}			
		} 
		
    }
	
	public function gst_information()
	{
		$postdata = Input::all();
		$postdata['relative_post_id'] = $this->userSess->supplier_id;
		$postdata['account_type_id'] = $this->userSess->account_type_id;

		   if (!empty($postdata['gstin_image']))
			{
				$attachment = $postdata['gstin_image'];
				$size = $attachment->getSize();
				if ($size < 2049133)
				{
					$filename = '';
					if ($attachment != '')
					{
						$folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';				

						$path = $this->config->get('path.SELLER.PROOF_DETAILS.LOCAL');
						$org_path = $this->config->get('path.SELLER.PROOF_DETAILS.ORIGINAL');
					  
						if (File::exists($path.'/'.getGTZ('Y')))
						{
							if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($path.'/'.getGTZ('Y'));
							File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (File::exists($org_path.'/'.getGTZ('Y')))
						{
							if (!File::exists($org_path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($org_path.'/'.getGTZ('Y'));
							File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (in_array(strtolower($attachment->getClientOriginalExtension()), array('jpg', 'jpeg', 'png')))
						{
							$org_name = $attachment->getClientOriginalName();
							$ext = $attachment->getClientOriginalExtension();
							$file_extentions = strtolower($ext);
							$filtered_name = $this->slug($org_name);
							$file_name = explode('_', $filtered_name);
							$file_name = $file_name[0];
							$file_name = $file_name.'.'.$ext;							
							$filename = getGTZ('dmYHis').$file_name;
							
							$uploaded_file = $attachment->move($org_path.$folder_path, $filename);
							$this->imgObj->imageresize($org_path.$folder_path.$filename, $path.$folder_path.$filename, 200, 200);
							if ($uploaded_file) {								
								$postdata['details']['gstin_image'] = $folder_path.$filename;
							}

						}
						else
						{
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							$op['msg'] = 'Invalid  ImageFile Formate';
							return Response::json($op, $this->statusCode, $this->headers, $this->options);
						}
					}					
				}
				else
				{					
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					$op['msg'] = 'Document size must be less than 2 MB';
					return Response::json($op, $this->statusCode, $this->headers, $this->options);
				}	
		 }  // GST Image Upload End
		

		 if (!empty($postdata['tan_image']))
			{
				
				$attachment = $postdata['tan_image'];
				$size = $attachment->getSize();
				if ($size < 2049133)
				{
					$filename = '';
					if ($attachment != '')
					{
						$folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';				

						$path = $this->config->get('path.SELLER.PROOF_DETAILS.LOCAL');
						$org_path = $this->config->get('path.SELLER.PROOF_DETAILS.ORIGINAL');
					  
						if (File::exists($path.'/'.getGTZ('Y')))
						{
							if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($path.'/'.getGTZ('Y'));
							File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (File::exists($org_path.'/'.getGTZ('Y')))
						{
							if (!File::exists($org_path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($org_path.'/'.getGTZ('Y'));
							File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (in_array(strtolower($attachment->getClientOriginalExtension()), array('jpg', 'jpeg', 'png')))
						{
							$org_name = $attachment->getClientOriginalName();
							$ext = $attachment->getClientOriginalExtension();
							$file_extentions = strtolower($ext);
							$filtered_name = $this->slug($org_name);
							$file_name = explode('_', $filtered_name);
							$file_name = $file_name[0];
							$file_name = $file_name.'.'.$ext;							
							$filename = getGTZ('dmYHis').$file_name;
							
							$uploaded_file = $attachment->move($org_path.$folder_path, $filename);
							$this->imgObj->imageresize($org_path.$folder_path.$filename, $path.$folder_path.$filename, 200, 200);
							if ($uploaded_file) {								
								$postdata['details']['tan_image'] = $folder_path.$filename;
							}

						}
						else
						{
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							$op['msg'] = 'Invalid  ImageFile Formate';
							return Response::json($op, $this->statusCode, $this->headers, $this->options);
						}
					}					
				}
				else
				{					
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					$op['msg'] = 'Document size must be less than 2 MB';
					return Response::json($op, $this->statusCode, $this->headers, $this->options);
				}			 

	  }

	    $res = $this->accObj->UpdateGstInfo($postdata);		
	    if ($res) 
		{
			$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
			$op['msg'] = 'Information Updated Successfully';
			return Response::json($op, $this->statusCode, $this->headers, $this->options);
		}			  
	
	}
	public function seller_general_info()
	{	
		$data['account_id']  = $this->account_id;
		$data['country_id']  = $this->userSess->country_id;    
		$data['acc_type_id'] = $this->account_type_id;
		$data['suppiler_id'] = $this->supplier_id;
		$data['post_type'] 	 = Config::get('constants.ADDRESS_POST_TYPE.SELLER');
		$data['fields'] = CommonNotifSettings::getHTMLValidation('seller.account-settings.general-details');
		$data['profile_pin_verify_fileds'] = CommonNotifSettings::getHTMLValidation('seller.verify-profile-pin');
	
		$data['profile_pin_forgot_fileds'] = CommonNotifSettings::getHTMLValidation('seller.profile-settings.profile-pin.reset');		
		$data['change_email_newemail_fileds'] = CommonNotifSettings::getHTMLValidation('seller.profile-settings.change-email.new-email-otp');
		$data['change_email_confirm_fileds'] = CommonNotifSettings::getHTMLValidation('seller.profile-settings.change-email.confirm');
		
		$data['change_email_newemail_fileds'] = CommonNotifSettings::getHTMLValidation('seller.profile-settings.change-email.new-email-otp');
		$data['change_email_confirm_fileds'] = CommonNotifSettings::getHTMLValidation('seller.profile-settings.change-email.confirm');
		$data['supplier_details'] 			 = $this->commonObj->getSupplierAccountDetails($data);	
		$data['store_images'] 				 = $this->accObj->get_store_images(['suppiler_id'=>$this->supplier_id]);		
		$data['business_filing_status'] = $this->accObj->GetBussinessFilingStatus();	
		$op['content'] = View('seller.account_settings.business_details', $data)->render();	
		$op['status'] = 'ok';
		return Response::json($op, 200, $this->headers, $this->options);			
	}
	

    public function General_Details ()
    {  
	    $data = [];		
		if (Request::ajax()) {		
			$op = [];
			$postdata = Input::all();   
			$postdata['account_id'] = $this->userSess->account_id;    
			$postdata['supplier_id'] = $this->userSess->supplier_id;  
			$postdata['account_type'] = $this->userSess->account_type_id;    
			if (!empty($postdata['profile_image']))
			{
				$attachment = $postdata['profile_image'];
				$size = $attachment->getSize();
				$filename = '';
					
				if ($size < 2049133)
				{
					$folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';				

					$path = $this->config->get('path.SELLER.PROFILE_IMG_PATH.LOCAL');
					$org_path = $this->config->get('path.SELLER.PROFILE_IMG_PATH.ORIGINAL');
				  
					if (!File::exists($path.'/'.getGTZ('Y')))
					{
						File::makeDirectory($path.'/'.getGTZ('Y'));
						File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
                    }
					
						$ext = $attachment->getMimeType();
						$mine_type = array(
							'image/jpeg'=>'jpg',
							'image/jpg'=>'jpg',
							'image/png'=>'png',
							'image/gif'=>'gif');
						$ext = $mine_type[$ext];
					if (in_array(strtolower($ext), $mine_type))
					{
						$org_name = $attachment->getClientOriginalName();
						$file_extentions = strtolower($ext);
						$filtered_name = $this->slug($org_name);
						$file_name = explode('_', $filtered_name);
						$file_name = $file_name[0];
						$file_name = $file_name.'.'.$ext;							
						$filename = getGTZ('dmYHis').$file_name;
						
						$uploaded_file = $attachment->move($path.$folder_path, $filename);
						//$this->imgObj->imageresize($org_path.$folder_path.$filename, $path.$folder_path.$filename, 200, 200);
						if ($uploaded_file) {								
							$postdata['details']['profile_img'] = $folder_path.$filename;
						}
					}
					else
					{
						$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
						$op['msg'] = 'Invalid  ImageFile Formate';
						return Response::json($op, $this->statusCode, $this->headers, $this->options);
					 }
										
				}else{
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					$op['msg'] = 'File size should not exists 2MB';
					return Response::json($op, $this->statusCode, $this->headers, $this->options);
				}
			}
			$res = $this->accObj->UpdateGeneralDetails($postdata);					
			if($res) 
			{
				$this->accObj->UpdateCompletedSteps(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.GENERAL_DETAILS'),
													'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),
													'supplier_id'=>$this->userSess->supplier_id,
													'account_id'=>$this->userSess->account_id]); 
				$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
				$op['msg'] = 'Information Updated Successfully';
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}else{
				$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
				$op['msg'] 		  = 'No changes update';
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}	
	    }
    }
/* 	public function sellet_general_info(){
		$data['account_id']  = $this->account_id;
		$data['country_id']  = $this->userSess->country_id;    
		$data['acc_type_id'] = $this->account_type_id;
		$data['suppiler_id'] = $this->supplier_id;
		$data['post_type'] 	 = Config::get('constants.ADDRESS_POST_TYPE.SELLER');
		$data['fields'] = CommonNotifSettings::getHTMLValidation('seller.account-settings.general-details');
		//$data['fields'] 	 = CommonNotifSettings::getHTMLValidation('seller.account-settings.business-details');
		$data['profile_pin_verify_fileds'] = CommonNotifSettings::getHTMLValidation('seller.verify-profile-pin');
		$data['supplier_details'] = $this->commonObj->getSupplierAccountDetails($data);		
		$data['store_images'] = $this->accObj->get_store_images(['suppiler_id'=>$this->supplier_id]);		
		$data['business_filing_status'] = $this->accObj->GetBussinessFilingStatus();	
		$op['content'] = View('seller.account_settings.business_details', $data)->render();	
		$op['status'] = 'ok';
		return Response::json($op, 200, $this->headers, $this->options);			
	}
 */
   /* public function General_Details ()
=======
	public function General_Details ()
>>>>>>> .r455
    {
		$data = [];	
		if (Request::ajax()) {
			$op = [];
			$postdata = Input::all();       
			$postdata['account_id'] = $this->userSess->account_id;    
			$postdata['supplier_id'] = $this->userSess->supplier_id;  
			$postdata['account_type'] = $this->userSess->account_type_id;    
			if (!empty($postdata['profile_image']))
			{
				$attachment = $postdata['profile_image'];
				$size = $attachment->getSize();
			   if ($size < 2049133)
				{
					$filename = '';
					if ($attachment != '')
					{
						$folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';				

						$path = $this->config->get('path.SELLER.PROFILE_IMG_PATH.LOCAL');
						$org_path = $this->config->get('path.SELLER.PROFILE_IMG_PATH.ORIGINAL');
					  
						if (File::exists($path.'/'.getGTZ('Y')))
						{
							//if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))

							if (File::exists($org_path.'/'.getGTZ('Y')))

							{
								if (!File::exists($org_path.'/'.getGTZ('Y').'/'.getGTZ('m')))
								{
									File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
								}
							}
							else
							{
								File::makeDirectory($org_path.'/'.getGTZ('Y'));
								File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
							
							if (in_array(strtolower($attachment->getClientOriginalExtension()), array('jpg', 'jpeg', 'png')))
							{
								$org_name = $attachment->getClientOriginalName();
								$ext = $attachment->getClientOriginalExtension();
								$file_extentions = strtolower($ext);
								$filtered_name = $this->slug($org_name);
								$file_name = explode('_', $filtered_name);
								$file_name = $file_name[0];
								$file_name = $file_name.'.'.$ext;							
								$filename = getGTZ('dmYHis').$file_name;
								
								$uploaded_file = $attachment->move($org_path.$folder_path, $filename);
								$this->imgObj->imageresize($org_path.$folder_path.$filename, $path.$folder_path.$filename, 200, 200);
								if ($uploaded_file) {								
									$postdata['details']['gstin_image'] = $folder_path.$filename;
								}

							}
							else
							{
								$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
								$op['msg'] = 'Invalid  ImageFile Formate';
								return Response::json($op, $this->statusCode, $this->headers, $this->options);
							 }
						 }					
						}
				}
				$res = $this->accObj->UpdateGstInfo($postdata);		
				if ($res) 
				{
					$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
					$op['msg'] = 'Information Updated Successfully';
					return Response::json($op, $this->statusCode, $this->headers, $this->options);
				}			   
			}
		}
    } */


	/* public function sellet_general_info(){
		
		$data['account_id']  = $this->account_id;
		$data['country_id']  = $this->userSess->country_id;    
		$data['acc_type_id'] = $this->account_type_id;
		$data['suppiler_id'] = $this->supplier_id;
		$data['post_type'] 	 = Config::get('constants.ADDRESS_POST_TYPE.SELLER');
		$data['fields'] = CommonNotifSettings::getHTMLValidation('seller.account-settings.general-details');
		//$data['fields'] 	 = CommonNotifSettings::getHTMLValidation('seller.account-settings.business-details');
		$data['profile_pin_verify_fileds'] = CommonNotifSettings::getHTMLValidation('seller.verify-profile-pin');
		$data['supplier_details'] = $this->commonObj->getSupplierAccountDetails($data);		
		$data['store_images'] = $this->accObj->get_store_images(['suppiler_id'=>$this->supplier_id]);		
		$data['business_filing_status'] = $this->accObj->GetBussinessFilingStatus();	
		$op['content'] = View('seller.account_settings.business_details', $data)->render();	
		$op['status'] = 'ok';
		return Response::json($op, 200, $this->headers, $this->options);			
		
	} */

	public function Business_Details ()
    {
		$data = [];	
		$postdata = $this->request->all(); 
		$op = [];
		$postdata['account_id'] = $this->userSess->account_id;    
		$postdata['supplier_id'] = $this->userSess->supplier_id;  
		$postdata['account_type'] = $this->userSess->account_type_id;    

		if (!empty($postdata['shop_image']))
		{
			$attachment = $postdata['shop_image'];
			$size = $attachment->getSize();
			$path = Config('path.SELLER.STORE_IMG_PATH.LOCAL');
			$folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';
			if ($size < 2049133)
			{				
				$filename = '';
				if (!File::exists($path.'/'.getGTZ('Y')))
				{
					File::makeDirectory($path.'/'.getGTZ('Y'));
				}
				if (File::exists($path.'/'.getGTZ('Y')))
				{
					if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))
					{
						File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
					}
					$ext = $attachment->getMimeType();
						$mine_type = array(
							'image/jpeg'=>'jpg',
							'image/jpg'=>'jpg',
							'image/png'=>'png',
							'image/gif'=>'gif');
						$ext = $mine_type[$ext];
					if (in_array(strtolower($ext), $mine_type))
					{
						$org_name = $attachment->getClientOriginalName();
						$file_extentions = strtolower($ext);
						$filtered_name = $this->slug($org_name);
						$file_name = explode('_', $filtered_name);
						$file_name = $file_name[0];
						$file_name = $file_name.'.'.$ext;							
						$filename = getGTZ('dmYHis').$file_name;
						
						$uploaded_file = $attachment->move($path.$folder_path, $filename);
						if ($uploaded_file) {								
							$postdata['mrmst']['banner'] = $folder_path.$filename;
						}
					}
					else
					{
						$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
						$op['msg'] = 'Invalid Banner Image File Formate';
						return Response::json($op, $this->statusCode, $this->headers, $this->options);
					}
				}					
			}
			else
			{					
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
				$op['msg'] = 'Banner Image Size is greater than 1 MB';
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}				
		}
		$postdata['primary_store_id'] = $this->primary_store_id;
		$res = $this->accObj->UpdateBusinessDetails($postdata);					
		if ($res) 
		{
			$this->accObj->UpdateCompletedSteps(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.BUSSINESS_DETAILS'),
												'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),
												'supplier_id'=>$this->supplier_id,
												'account_id'=>$this->account_id]); 	
			$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
			$op['msg'] = 'Account Updated Successfully';
			return Response::json($op, $this->statusCode, $this->headers, $this->options);
		}
    }
	
    public function add_store_images(){
		$postdata 	= $this->request->all();
		//print_R($postdata);exit;
		$attachment = $postdata['images'];
			$size 	= $attachment->getSize();
			if($size < 2049133)
			{
				$filename 		 = '';
				if($attachment != '')
				{
					$folder_path 			= '/'.getGTZ('Y').'/'.getGTZ('m').'/';						
					$path 		 			= $this->config->get('path.SELLER.STORE_IMG_PATH.IMAGES');
					if (File::exists($path.'/'.getGTZ('Y')))
					{
						if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))
						{
							File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
					}
					else
					{
						File::makeDirectory($path.'/'.getGTZ('Y'));
						File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
					}
					
					$ext = $attachment->getMimeType();
						$mine_type = array(
							'image/jpeg'=>'jpg',
							'image/jpg'=>'jpg',
							'image/png'=>'png',
							'image/gif'=>'gif');
						$ext = $mine_type[$ext];
					if (in_array(strtolower($ext), $mine_type))
					{
						$org_name = $attachment->getClientOriginalName();
						$file_extentions = strtolower($ext);
						$filtered_name = $this->slug($org_name);
						$file_name  = explode('_', $filtered_name);
						$file_name  = $file_name[0];
						$file_name  = $file_name.'.'.$ext;							
						$filename 	= getGTZ('dmYHis').$file_name;
						$uploaded_file = $attachment->move($path.$folder_path, $filename);
						//$this->imgObj->imageresize($org_path.$folder_path.$filename, $path.$folder_path.$filename, 300, 300);
						if ($uploaded_file) {								
							$data['file_path'] = $folder_path.$filename;
						}
						$data['supplier_id'] = $this->supplier_id;
						//$data['store_id'] 	 = $this->store_id;
						$data['status'] 	 = config('constants.ON');
						$res = $this->accObj->save_store_images($data);
						if(!empty($res)){
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							$op['msg'] = 'File Successfully Uploaded';
						}else{
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							$op['msg'] = 'File upload Faild';
						}
					}
					else
					{
						$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
						$op['msg'] = 'Invalid Shop Image File Formate';
					}
				}
			}
			else
			{					
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				$op['msg'] = 'Image Size must be less than 2 MB';
			}
		return Response::json($op, $this->statusCode, $this->headers, $this->options);
	}
	
	public function get_bank_details(){		
		$data['account_id'] = $this->account_id;
		$data['acc_type_id'] = $this->account_type_id;
		$data['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.SELLER');		
		$data['supplier_id'] = $this->userSess->supplier_id;  
		$data['fields'] = CommonNotifSettings::getHTMLValidation('seller.account-settings.bank-details');	
		$data['bank_account_details'] = $this->accObj->GetBankAccountDetails($data);				
		$op['content'] 		= View('seller.account_settings.bank_details', $data)->render();
		$op['status'] 			= 'ok';
		//echo"<pre>"; print_r($data['fields']);exit;
		return Response::json($op,$this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
	}
	
	public function Bank_Details ()
    {
		$data = $op = [];	
		if (Request::ajax()) {			
			$postdata = $this->request->all(); 
		    $postdata['payment_setings']['beneficiary_name'] = trim($postdata['payment_setings']['beneficiary_name']);
			$postdata['account_id'] = $this->userSess->account_id;    
			$postdata['supplier_id'] = $this->userSess->supplier_id;  
			$postdata['account_type'] = $this->userSess->account_type_id; 		
			$res = $this->accObj->UpdateBankDetails($postdata);					
			if ($res) 
			{		       
				$this->accObj->UpdateCompletedSteps(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.BANK_DETAILS'),
													'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),
													'supplier_id'=>$this->supplier_id,
													'account_id'=>$this->account_id]); 														
				$op['msg'] = 'Account Updated Successfully';					
				$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
			}			
		} 
		return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

	public function Get_Ifsc_Bank_Details ()
	{	
		 $op = [];
		 $postdata = Input::all();     
		 $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		 $res = CommonLib::validetIFSC($postdata['ifsc']);
		 $res = '{"ifsc":"MAHB0001821","micr":"444014548","bank":"BANK OF MAHARASHTRA","branch":"CHANDUR BAZAR","address":"NEAR KALPANA GAS SERVICE, PLOT NO.128, WARD NO.12, BELORA ROAD, CHANDUR BAZAR, DIST AMRAVATI, PIN CODE 444704","city":"AMRAVATI","district":"CHANDUR BAZAR","state":"MAHARASHTRA","contact":"243232","valid":"true"}';
		 if(!empty($res)){
			 $op['data'] = json_decode($res,true);
			 $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
		 }		
		return Response::json($op, $this->statusCode, $this->headers, $this->options);	
	}
	
	public function pickup_address()
	{			
		$data = [];	
		$data['country_id']	= $this->country_id;
		$pickup_address		=	$this->accObj->pickup_address_check($this->userSess->supplier_id,$this->config->get('constants.ADDRESS.PICKUP'),$this->userSess->account_type_id);
		$location_details   = $this->accObj->location_details($this->userSess->supplier_id,$this->config->get('constants.ADDRESS.PICKUP'),$this->userSess->account_type_id);
		$data['country'] = $this->commonObj->getCountryName($this->country_id);
		if(!empty($pickup_address)){
			$data['pickup_adrress']=$pickup_address;
			$data['location_details']=$location_details;
		}
		$data['mobile_validation'] = $this->user_details->mobile_validation;
		$op['content'] = View('seller.account_settings.pickup_address', $data)->render();
		$op['status'] = 'ok';
		return Response::json($op, $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
	}
	
	public function update_pickup_address(){
		$data = [];	
		$data['country_id']	= $this->country_id;
		$pickup_address		=	$this->accObj->pickup_address_check($this->userSess->supplier_id,$this->config->get('constants.ADDRESS.PICKUP'),$this->userSess->account_type_id);
		$location_details   = $this->accObj->location_details($this->userSess->supplier_id,$this->config->get('constants.ADDRESS.PICKUP'),$this->userSess->account_type_id);
		$data['country'] = $this->commonObj->getCountryName($this->country_id);
		if(!empty($pickup_address)){
			$data['pickup_adrress']		= $pickup_address;
			$data['location_details']	= $location_details;
		}
		if (Request::ajax()) {
			$op = [];
			$postdata = Input::all();  
	
			$postdata['relative_post_id'] = $this->userSess->supplier_id;  
			$postdata['post_type'] = $this->userSess->account_type_id; 		
			$postdata['address_type_id'] = $this->config->get('constants.ADDRESS.PICKUP');
			$postdata['country_id'] = $this->userSess->country_id;

			$res = $this->accObj->Update_Pickup_address($postdata);					
			if ($res) 
			{
				$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
				$op['msg'] = 'Pickup Address Updated Successfully';
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}	 		
		}
	}		
	
	/* Change Password */
	public function update_password ()
    {   	    
		if (Request::ajax()) 
		{
			$postdata = $this->request->all();
			$this->account_id = $this->userSess->account_id;
			$data['password'] = $this->accObj->old_password_check($this->account_id);
			if ($data['password'] == md5($postdata['oldpassword']))
			{			
				if ($data['password'] != md5($postdata['newpassword']))
				{
	                $data['update'] = $this->accObj->update_new_password($postdata['newpassword'], $this->account_id);				
					if ($data['update'])
					{
				        $this->userSess->pass_key = md5($postdata['newpassword']);
                        $this->config->set('app.accountInfo', $this->userSess);
                        $this->session->set($this->sessionName, $this->userSess);
						CommonNotifSettings::notify('CHANGE_PASSWORD', $this->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['name'=>$this->userSess->full_name], true, true, false, true, false);			
						$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
						$op['msg'] = 'Your password has been changed successfully';
						$op['url'] = route('seller.logout');
						$op['paths']=$this->commonObj->logoutAllDevices($this->userSess->account_id,$this->userSess->account_log_id);
					}
                }
				else
				{
					$op['msg'] = 'Your new password cannot be same as old password';
					$this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
				}
			}
			else 
			{				
				$op['msg'] = 'Current Password is incorrect, please try again';
				$this->statusCode = $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}
		}
		return Response::json($op, $this->statusCode, $this->headers, $this->options);		
	}
	
	public function change_password(){
		$data['change_pwd_fields'] = CommonNotifSettings::getHTMLValidation('seller.account-settings.update-password');			
		$op['content'] = View('seller.account_settings.change_password',$data)->render();
		$op['status'] 	= 'ok';
		return Response::json($op, $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
	}

	/* public function change_password ()
	{
		if (Request::ajax()) {
			$postdata = Input::all(); 
			$data['password'] = $this->accObj->old_password_check($this->account_id);						
			if ($data['password'] == md5($postdata['oldpassword']))
			{				
				$data['update'] = $this->accObj->update_new_password($postdata['newpassword'], $this->account_id);				
				if ($data['update'])
				{
					//CommonNotifSettings::notify('CHANGE_PASSWORD', $this->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['name'=>"$this->full_name", 'uname'=>"$this->full_name", 'pwd'=>$postdata['newpassword']], true, true, false, true, false);						
					$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
					$op['msg'] = Lang::get('general.new_password_has_been_updated_successfully');
				}
			}
			else
			{				
				$this->statusCode = $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
				$op['msg'] = Lang::get('general.old_password_doest_matched_with_the_new_password');
			}
			return Response::json($op, $this->statusCode, $this->headers, $this->options);
		} else {
			return View('seller.account_settings.change_password');
		}
    } */

public function slug ($text)
{
	$text = preg_replace('/\W|_/', '_', $text);
	// Clean up extra dashes
	$text = preg_replace('/-+/', '-', trim($text, '_')); // Clean up extra dashes
	// lowercase
	$text = strtolower($text);
	if (empty($text))
	{
		return false;
	}
	return $text;
}
public function Manage_cashback ()
{
	 $op['content'] = view('seller.account_settings.manage_cashback')->render();
		$op['status'] = 'ok';
		return Response::json($op, 200, $this->headers, $this->options);   
}

public function update_cashback ()
{
	$data = $this->request->all();		
	$data['supplier_id'] = $this->supplier_id;  
	$result = $this->accObj->update_retailer_settings($data);					
	if (!empty($result))
	{
		$this->statusCode = $op['status'] = 200;
		$op['msg'] = 'Settings Updated Successfully';
	}
	else
	{
		$this->statusCode = $op['status'] = 400;
		$op['msg'] = 'No Changes';
	}
	return Response::json($op, $this->statusCode, $this->headers, $this->options);          
}

public function add_profit_sharing ()
{
	$postdata = Input::all();
	$postdata['supplier_id'] = $this->supplier_id;
	$postdata['created_by'] = $this->account_id;
	//$postdata['bcategory_id'] = $this->bcategory_id;
	$postdata['country_id'] = $this->country_id;
	$postdata['currency_id'] = $this->currency_id;
	$postdata['acc_type_id'] = $this->acc_type_id;
	$res = $this->accObj->add_profit_sharing($postdata);				
	if (!empty($res))
	{
		$this->accObj->UpdateCompletedSteps(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.MANAGE_CASHBACK'),
											'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),
											'supplier_id'=>$this->supplier_id,
											'account_id'=>$this->account_id]);		 
		$this->statusCode = $op['status'] = 200;
		$op['msg'] = 'Commision Added Successfully.';
	}
	else
	{
		$this->statusCode = $op['status'] = 400;
		$op['msg'] = trans('retailer/account.update_setting_faild');
	}
	return Response::json($op, $this->statusCode, $this->headers, $this->options);        
}

	public function verifyProfilePIN ()
	{	
	    $postdata = $this->request->all();	
		$op = [];
		$data = [];
		$data['account_id']=$this->userSess->account_id;
		$data['profile_pin']=$postdata['profile_pin'];
		if (!empty($postdata['profile_pin']))
		{
			 $qry = $this->accObj->Check_user_deetails($data);
			if ($qry->security_pin == md5($this->request->profile_pin))
			{
				$op['propin_session'] = md5($this->userSess->account_id.rand(100000, 999999).getGTZ(null, 'YmdHis'));
				$this->session->set('profilePINHashCode', $op['propin_session']);
				$this->statusCode = $this->config->get('httperr.SUCCESS');
			}
			else
			{
				$op['msg'] = trans('seller/general.invalid', ['which'=>trans('profile_pin')]);
			}
		}
		else
		{
			$op['msg'] = trans('user/account.generate_profile_pin');
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}

	public function changeEmailNewEmailOTP ()
    {
        $op	= $data = $postdata = []; 
		$postdata = $this->request->all();
	    if (!empty($postdata['email']) && !empty($this->userSess->is_email_verified))
        { 
			$data['old_email'] = $this->userSess->email;
			$data['new_email'] = $postdata['email'];
			$data['account_id'] = $this->userSess->account_id;				
			$data['full_name']=$this->userSess->full_name;
			$op['code'] = $data['code'] = rand(100000, 999999);
			$op['hash_code'] = $data['hash_code'] = md5($data['code']);		
			$this->session->set('changeEmailID', $data);				
			$token = $this->session->getId().'.'.$op['hash_code'];
			$op['link'] = $verify_link = url('seller/verifyEmailLink/'.$token);	
			CommonNotifSettings::notify('CHANGE_EMAIL_OTP',$this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['code'=>$data['code'],'email_verify_link'=>$verify_link, 'email'=>$data['old_email'], 'full_name'=>$data['full_name']],
			true, false, false, true, false);
			$this->statusCode = $this->config->get('httperr.SUCCESS');
			$op['msg'] = trans('seller/account.code_sent_to_email', ['email'=>maskEmail($data['old_email'])]);	
			
		} else if(!empty($postdata['email']) && empty($this->userSess->is_email_verified)) 
		{
			$this->session->forget('email_verification');
			$data['email'] = $postdata['email'];
			$data['account_id'] = $this->userSess->account_id;
			$data['supplier_id'] = $this->userSess->supplier_id;
			$data['full_name'] = $this->userSess->full_name;	
			$op['code'] = $data['code'] = rand(100000, 999999);
			$op['hash_code'] = $data['hash_code'] = md5($data['code']);			
			$this->session->set('email_verification', $data);		 //changeNewEmailIDConfirm		
			$token = $this->session->getId().'.'.$data['hash_code'];
			$op['link'] = $verify_link = url('seller/verifyNewEmailLink/'.$token);			
			CommonNotifSettings::notify('SUPPLIER_VERIFY_EMAIL',$data['account_id'], Config::get('constants.ACCOUNT_TYPE.SELLER'),['code'=>$data['code'], 'email_verify_link'=>$verify_link, 'email'=>$data['email'], 'full_name'=>$data['full_name']],
			true, false, false, true, false); 	
			$this->statusCode = $this->config->get('httperr.SUCCESS');
			$email = maskEmail($data['email']);
			$op['msg'] = trans('seller/account.code_sent_to_email', ['email'=>$email]);						
		}else {
		    $op['msg'] = 'Parameter Missing..';
			$op['status']  = $this->statusCode = config('httperr.UN_PROCESSABLE');			
		}
	   return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	
	/* public function sendEmailverification ()
    {	
	    $op = $data = [];	
		$postdata = $this->request->all();		
		if(!empty($postdata)) {		
			$data['email'] = $this->userSess->email;
			$data['account_id'] = $this->userSess->account_id;
			$data['full_name'] = $this->userSess->full_name;	
			$op['code'] = $data['code'] = rand(100000, 999999);
			$op['hash_code'] = $data['hash_code'] = md5($data['code']);		
			$this->session->set('changeNewEmailIDConfirm', $data);				
			$token = $this->session->getId().'.'.$op['hash_code'];
			$op['link'] = $verify_link = url('seller/verifyNewEmailLink/'.$token);
			CommonNotifSettings::notify('CHANGE_NEW_EMAIL_OTP',$data['account_id'], Config::get('constants.ACCOUNT_TYPE.SELLER'),['code'=>$data['code'], 'email_verify_link'=>$verify_link, 'email'=>$data['email'], 'full_name'=>$data['full_name']],
			true, false, false, true, false); 	
			$this->statusCode = $this->config->get('httperr.SUCCESS');
			$email = maskEmail($data['email']);
			$op['msg'] = trans('seller/account.code_sent_to_email', ['email'=>$email]);	
		}
		else
		{
			$op['msg'] = 'Parameter missing';
			$op['staus'] = $this->statusCode = 422;
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	} */
	

 /*  public function maskEmail ($email)
    {
        $email = explode('@', $email);
        $len = strlen($email[0]);
        return substr_replace($email[0], str_repeat('*', $len - ( $len > 5 ? 4 : 2)), $len > 5 ? 2 : 1, $len - ($len > 5 ? 4 : 2)).'@'.$email[1];
    } */ 
	
	public function get_shipping_address(){
		$data = [];	
		$data['country_id']= $this->country_id;
		$pickup_address=$this->accObj->pickup_address_check($this->userSess->supplier_id,$this->config->get('constants.ADDRESS.SHIPPING'),$this->userSess->account_type_id);
		$location_details=$this->accObj->location_details($this->userSess->supplier_id,$this->config->get('constants.ADDRESS.SHIPPING'),$this->userSess->account_type_id);
		$data['country'] = $this->commonObj->getCountryName($this->country_id);
		if(!empty($pickup_address)){
			$data['pickup_adrress']=$pickup_address;
			$data['location_details']=$location_details;
		}
		$op['content'] = View('seller.account_settings.return_address', $data)->render();
		$op['status'] = 'ok';
		return $this->response->json($op, $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
	}
	
	/* Return Address Details */
	public function update_return_address ()
    {
		$data = [];	
		$data['country_id']= $this->country_id;
		$pickup_address=$this->accObj->pickup_address_check($this->userSess->supplier_id,$this->config->get('constants.ADDRESS.SHIPPING'),$this->userSess->account_type_id);
		$location_details=$this->accObj->location_details($this->userSess->supplier_id,$this->config->get('constants.ADDRESS.SHIPPING'),$this->userSess->account_type_id);
		$data['country'] = $this->commonObj->getCountryName($this->country_id);
		if(!empty($pickup_address)){
			$data['pickup_adrress']=$pickup_address;
			$data['location_details']=$location_details;
		}
		
		if (Request::ajax()) {
			$op = [];
			$postdata = Input::all();  
			$postdata['relative_post_id'] = $this->userSess->supplier_id;  
			$postdata['post_type'] = $this->userSess->account_type_id; 		
			$postdata['address_type_id'] = $this->config->get('constants.ADDRESS.SHIPPING');
			$postdata['country_id'] = $this->userSess->country_id;

			$res = $this->accObj->Update_Pickup_address($postdata);					
			if ($res) 
			{
				$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
                $op['msg'] = 'Return Address Updated Successfully';
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}	 		
		}
    }	

    public function changeEmailIDConfirm ()
    {	
		if ($this->session->has('email_verification'))
		{
			$data = $op = [];
			$data = $this->session->get('email_verification');
			
			$data['account_id'] = $this->userSess->account_id;
			
				if ($this->accObj->changeEmailID($data))
				{
					$op['email'] = $this->userSess->email = $data['new_email'];
					$this->config->set('app.accountInfo', $this->userSess);
					$this->session->set($this->sessionName, (array) $this->userSess);
					$this->session->forget('changeEmailID');
					$this->session->forget('email_verification');
					$op['new_email']=$data['new_email'];
					$op['msg'] = trans('seller/account.email_updated');
					$this->statusCode = $this->config->get('httperr.SUCCESS');
				}
				else
				{
					$this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
					$op['msg'] = trans('general.no_changes');
				}
		}
		else
		{
			$op['msg'] = trans('general.not_accessable');
			$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	
    public function changeEmailNewEmailOTPResend ()
    {
        if ($this->session->has('changeEmailID'))
        {
            $data = $op = [];
            $data = $this->session->get('changeEmailID');
            $op['code'] = $data['code'] = rand(100000, 999999);
            $op['hash_code'] = md5($data['code']);
            $this->session->set('changeEmailID', $data);
            CommonNotifSettings::notify('USER.CHANGE_EMAIL_OTP',$this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'),['code'=>$data['code'],
			'email'=>$data['old_email'],'new_email'=>$data['new_email']],true, false, false, true, false);
			$this->statusCode = $this->config->get('httperr.SUCCESS');
			$op['msg'] = trans('seller/account.code_sent_to_email', ['email'=>maskEmail($data['old_email'])]);
        }
        else
        {
            $op['msg'] = trans('general.not_accessable');
            $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
 
	public function forgotProfilePin ()
    {
		$data=[];
		$data['account_id'] = $this->userSess->account_id;
	    $qry = $this->accObj->Check_user_deetails($data);
		 
        if (!empty($qry->security_pin))
        {
            $data = $this->session->has('resetProfilePin') ? $this->session->get('resetProfilePin') : [];
            if (empty($data['code']))
            {
                $data['code'] = rand(100000, 999999);
                $data['hash_code'] = md5($data['code']);
                $data['account_id'] = $this->userSess->account_id;
                $this->session->set('resetProfilePin', $data);
            }
            $op['code'] = $data['code'];
            $op['hash_code'] = $data['hash_code'];
            $op['token'] = $token = $this->session->getId().'.'.$data['hash_code'];
            //$forgotpin_link = url('reset-profile-pin/'.$token);
			CommonNotifSettings::notify('USER.FORGOT_PROFILE_PIN',$this->userSess->account_id,Config::get('constants.ACCOUNT_TYPE.SELLER'),
			['code'=>$data['code'],'email'=>$this->userSess->email,'full_name'=>$this->full_name],true, false, false, true, false);
            $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['msg'] = trans('seller/general.code_sent_to_email', ['email'=>maskEmail($this->userSess->email)]);
        }
        else
        {
            $op['msg'] = trans('seller/general.generate_profile_pin');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
    }
    
    public function resetProfilePin ()
    {
        $data = $sdata = [];
		$data['account_id']=$this->userSess->account_id;
        if ($this->session->has('resetProfilePin'))
        {
            $sdata = $this->session->get('resetProfilePin');
		    if ($sdata['code'] == $this->request->code)
            {
                $data['profile_pin'] = $this->request->profile_pin;
				
				$qry = $this->accObj->Check_user_deetails($data);
				
                if ($qry->security_pin != md5($data['profile_pin']))
                  {
                    $data['account_id'] = $this->userSess->account_id;
                    if ($this->accObj->saveProfilePIN($data))
                    {
                        $this->userSess->profile_pin = md5($this->request->profile_pin);
						$this->config->set('app.accountInfo', $this->userSess);
                        $this->session->set($this->sessionName, $this->userSess);
                        $op['propin_session'] = md5($this->userSess->account_id.rand(100000, 999999).getGTZ(null, 'YmdHis')); //
                        $this->session->set('profilePINHashCode', $op['propin_session']);
                       // CommonLib::notify($this->userSess->account_id, 'change_profile_pin');
                        $this->session->forget('resetProfilePin');
                        $op['msg'] = 'You\'ve successfully reset your Security PIN';
                        $this->statusCode = $this->config->get('httperr.SUCCESS');
                    }
                    else
                    {
                        $op['msg'] = trans('general.something_wrong');
                        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                    }
                }
                else
                {
                    $op['msg'] = trans('seller/general.profile_pin_should_not_be_same');
                    $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                }
            }
            else
            {
                $op['msg'] = trans('general.invalid', ['which'=>trans('general.otp')]);
            }
        }
        else
        {
            $op['msg'] = trans('general.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	

	public function Commisions ()
    {
         $data = $op = [];
           $data['supplier_id'] = $this->supplier_id;        
		 if ($settings = $this->accObj->SupplierCashbackSettings($data))
		 {			
			$op['settings'] = $settings;
			$this->statusCode = 200;
		}       
        return Response::json($op, $this->statusCode, $this->headers, $this->options);  
    }
	
/* Proof details  */

    public function update_Proof_details()
	{
	    $data = [];	
		if (Request::ajax()) {
	        $op = [];
		    $postdata = Input::all();
		    $postdata['relative_post_id'] = $this->userSess->supplier_id;
            $postdata['account_type_id'] = $this->userSess->account_type_id;
	        /* File Upload Start ID Proof */
            if (!empty($postdata['id_image']))
			{
				$attachment = $postdata['id_image'];
				$size = $attachment->getSize();

				if ($size < 2049133)
				{
					$filename = '';
					if ($attachment != '')
					{
						$folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';				

						$path = $this->config->get('path.SELLER.PROOF_DETAILS.LOCAL');
                        $org_path = $this->config->get('path.SELLER.PROOF_DETAILS.ORIGINAL');
						if (File::exists($path.'/'.getGTZ('Y')))
						{
							if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($path.'/'.getGTZ('Y'));
							File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (File::exists($org_path.'/'.getGTZ('Y')))
						{
							if (!File::exists($org_path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($org_path.'/'.getGTZ('Y'));
							File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (in_array(strtolower($attachment->getClientOriginalExtension()), array('jpg', 'jpeg', 'png','pdf')))
						{
							$org_name = $attachment->getClientOriginalName();
							$ext = $attachment->getClientOriginalExtension();
							$file_extentions = strtolower($ext);
							$filtered_name = $this->slug($org_name);
							$file_name = explode('_', $filtered_name);
							$file_name = $file_name[0];
							$file_name = $file_name.'.'.$ext;							
							$filename = getGTZ('dmYHis').$file_name;
							
							$uploaded_file = $attachment->move($org_path.$folder_path, $filename);
                            $this->imgObj->imageresize($org_path.$folder_path.$filename, $path.$folder_path.$filename, 200, 200);
							if ($uploaded_file) {								
								$postdata['details']['id_proof'] = $folder_path.$filename;
							}
						}
						else
						{
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							$op['msg'] = 'Invalid ID Proof  Image File Formate';
							return Response::json($op, $this->statusCode, $this->headers, $this->options);
						}
					}					
				}
				else
				{					
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					$op['msg'] = 'Image Size is greater than 1 MB';
					return Response::json($op, $this->statusCode, $this->headers, $this->options);
				}				
			}
          /* Id Proof End */
		  
		  /* Address Proof  */
		   if (!empty($postdata['address_image']))
			{
				$attachment = $postdata['address_image'];
				$size = $attachment->getSize();

				if ($size < 2049133)
				{
					$filename = '';
					if ($attachment != '')
					{
						$folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';				

						$path = $this->config->get('path.SELLER.PROOF_DETAILS.LOCAL');
                        $org_path = $this->config->get('path.SELLER.PROOF_DETAILS.ORIGINAL');
						if (File::exists($path.'/'.getGTZ('Y')))
						{
							if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($path.'/'.getGTZ('Y'));
							File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (File::exists($org_path.'/'.getGTZ('Y')))
						{
							if (!File::exists($org_path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($org_path.'/'.getGTZ('Y'));
							File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (in_array(strtolower($attachment->getClientOriginalExtension()), array('jpg', 'jpeg', 'png','pdf')))
						{
							$org_name = $attachment->getClientOriginalName();
							$ext = $attachment->getClientOriginalExtension();
							$file_extentions = strtolower($ext);
							$filtered_name = $this->slug($org_name);
							$file_name = explode('_', $filtered_name);
							$file_name = $file_name[0];
							$file_name = $file_name.'.'.$ext;							
							$filename = getGTZ('dmYHis').$file_name;
							
							$uploaded_file = $attachment->move($org_path.$folder_path, $filename);
                            $this->imgObj->imageresize($org_path.$folder_path.$filename, $path.$folder_path.$filename, 200, 200);
							if ($uploaded_file) {								
								$postdata['details']['address_proof'] = $folder_path.$filename;
							}
						}
						else
						{
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							$op['msg'] = 'Invalid Address Proof ImageFile Formate';
							return Response::json($op, $this->statusCode, $this->headers, $this->options);
						}
					}					
				}
				else
				{					
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					$op['msg'] = 'Image Size is greater than 1 MB';
					return Response::json($op, $this->statusCode, $this->headers, $this->options);
				}				
			}
			
			/* Address Proof Upload Ends */
		    $res = $this->accObj->UpdateProofDetails($postdata);	
			if ($res) 
			{
				$op['url'] = $this->accObj->UpdateCompletedSteps(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.TAX_INFO'),
																'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),
																'supplier_id'=>$this->supplier_id,
																'account_id'=>$this->account_id]);		
																
				$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
                $op['msg'] = 'Documents Updated Successfully';
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}	
		}
    } 
	public function get_tags(){
		$data = $this->request->all();
		$op['tags'] = $this->accObj->get_store_tags($data);
		//$op['tags'] = ['a1'=>'c++','a2'=> 'java','a3'=> 'php','a4'=> 'coldfusion','a5'=> 'javascript'];
		return Response::json($op, 200, $this->headers, $this->options);
	}
	
	public function change_security_pin()
	{
		$postdata = $this->request->all();
		if($this->request->ajax()){
			if (!empty($this->user_details->security_pin))
			{
				$data = [];
				$data['profile_pin'] = $this->request->new_pin;
				if (md5($this->request->current_pin) == $this->user_details->security_pin)
				{
					if (md5($this->request->new_pin) != $this->userSess->security_pin)
					{
						$data['account_id'] = $this->account_id;
						if ($this->accObj->saveProfilePIN($data))
						{
							$this->user_details->security_pin = md5($this->request->new_pin);
							$this->userSess->security_pin = md5($this->request->new_pin);
							$this->config->set('app.accountInfo', $this->userSess);
							$this->session->set($this->sessionName, $this->userSess);
							$op['msg'] = trans('seller/general.profile_pin_updated_successfully');
							$this->statusCode = $this->config->get('httperr.SUCCESS');
						}
						else
						{
							$op['msg'] = trans('general.something_wrong');
							$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
						}
					}
					else
					{
						$op['msg'] = trans('seller/general.profile_pin_should_not_be_same');
						$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					}
				}
				else
				{
					$op['msg'] = trans('seller/general.invalid_current_pin');
					$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				}
			}
			else
			{
				$op['msg'] = trans('seller/general.not_accessable');
				$this->statusCode = $this->config->get('httperr.FORBIDDEN');
			}
			return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
				
		}else{
			return view('seller.account_settings.change_security_pin');
		}
	}
	
    public  function ChangeMobile()
    {
        $op = [];
	    $data = [];
	    if(!empty($this->userSess->is_email_verified) && $this->userSess->is_email_verified==1) 
	    {
			if(!empty($this->userSess->email)) {
				$op = $data = [];
				$data['email'] = $this->userSess->email;
				$data['supplier_id'] = $this->userSess->supplier_id;
				$data['uname']=$this->userSess->full_name;
				$op['code'] = $data['code'] = rand(100000, 999999);
				$op['hash_code'] = $data['hash_code'] = md5($data['code']);
				$this->session->set('ChangeMobileVerify', $data);
				$token = $this->session->getId().'.'.$op['hash_code'];
				$verify_link = url('seller/verifyMobileLink/'.$token);
				$op['link']=$verify_link;
				CommonNotifSettings::notify('USER.CHANGE_MOBILE_NO',$this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'),['code'=>$data['code'],'email_verify_link'=>$verify_link,'email'=>$data['email'],'full_name'=>$data['uname']],true, false, false, true, false);
				$this->statusCode = $this->config->get('httperr.SUCCESS');
				$op['msg'] = trans('seller/account.code_sent_to_email', ['email'=>$data['email']]); 
			}
        }
		else{
			$op['err'] ='not_found';
			$op['msg'] = trans('seller/account.email_verifie_link');
		}
	    return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

  /* Change Mobile Verification Link */

    public function verifyMobileLink ($token)
    {  
       $data = $sdata = [];
	   /*Phone Code Data */
	    $data['account_id']  = $this->account_id;
        $data['country_id']  = $this->userSess->country_id;    
		$data['acc_type_id'] = $this->account_type_id;
		$data['suppiler_id'] = $this->supplier_id;
		$data['post_type'] 	 = Config::get('constants.ADDRESS_POST_TYPE.SELLER');
		$data['supplier_details'] = $this->commonObj->getSupplierAccountDetails($data);		
		/* End */
        $data['email_verify'] = false;
        if(!empty($token) && strpos($token, '.'))
        {
            $access_token = explode('.', $token);
            $this->session->setId($access_token[0], true);
          if($this->session->has('ChangeMobileVerify'))
            {  
            $sdata = $this->session->get('ChangeMobileVerify');
            $supplier_id = (isset($this->userSess->supplier_id) && !empty($this->userSess->supplier_id)) ? $this->userSess->supplier_id : ''; 
                if($sdata['supplier_id'])
                {    			        
                    if(!empty($sdata) && ($sdata['hash_code'] == $access_token[1]))
                    {
		             $data['change_mobile_confirm_fileds'] = CommonNotifSettings::getHTMLValidation('seller.profile-settings.change-mobile.confirm');
					    return view('seller.account_settings.change_mobile',$data);
                    }
                    else
                    {
                        $data['msg'] = trans('seller/account.mobile_verifyemail_sess_expire');
                    }
                }
                else
                {
                    $data['msg'] = trans('seller/account.mobile_verifyemail_account_invalid');
                }
            }
            else
            {
				$data['msg'] = trans('seller/account.mobile_verifyemail_sess_expire');                
            }
        }
        else
        {  
			$data['msg'] = trans('seller/account.mobile_verifyemail_sess_expire');          
        }
		return view('seller.account_settings.change_mobile_confirm', (array) $data);	
    }
	public function ChangeMobileOTP(){
	    $op = [];
	    $data = [];
		 $postdata = Input::all();
		 if(!empty($postdata['phone_no'])){
			 
		  if($this->userSess->mobile!=$postdata['phone_no']) {
			$data['phone_number'] = $postdata['phone_no'];
			$op['code'] = $data['code'] = rand(100000, 999999);
			$op['hash_code'] = md5($data['code']);
			$this->session->set('ChangeMobileOTP', $data);
             CommonNotifSettings::notify('USER.CHANGE_MOBILE_OTP',$this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'),['code'=>$data['code'],'mobile'=>$data['phone_number']],false, true, false, true, false);
			$this->statusCode = $this->config->get('httperr.SUCCESS');
			$op['msg'] = trans('seller/account.code_sent_to_mobile', ['mobile'=>$data['phone_number']]);
		}
		else
		{
			$op['msg'] =trans('seller/account.phone_number_valid');
		}
	}
	
	else{
		$op['msg'] =trans('seller/account.phone_number_empty');
	}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	public function ChangeMobileOTPResend(){
	   $op = [];
	    $data = [];

	   if ($this->session->has('ChangeMobileOTP'))
         {
            $data = $this->session->get('ChangeMobileOTP');
            $op['code'] = $data['code'] = rand(100000, 999999);
            $op['hash_code'] = md5($data['code']);
            $this->session->set('ChangeMobileOTP', $data);
            CommonNotifSettings::notify('USER.CHANGE_MOBILE_OTP',$this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'),['code'=>$data['code'],'mobile'=>$data['phone_number']],false, true, false, true, false);
			$this->statusCode = $this->config->get('httperr.SUCCESS');
			$op['msg'] = trans('seller/account.code_sent_to_mobile', ['mobile'=>$data['phone_number']]);
        }
        else
        {
            $op['msg'] = trans('general.not_accessable');
            $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        }
		   
		     return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
		}
	public function ChangeMobileConfirm(){
		
		$postdata = Input::all();
		
		if($this->userSess->mobile!=$postdata['phone_no']){
			
		    if ($this->session->has('ChangeMobileOTP'))
		      {
			$data = $op = [];
			$data = $this->session->get('ChangeMobileOTP');
			
			$data['account_id'] = $this->userSess->account_id;
			 $data['new_phone_no']=$postdata['phone_no']; 
			if ($data['code'] == $this->request->code)
			{
				if ($this->accObj->changeMobile($data))
				{
					$op['mobile'] = $this->userSess->mobile = $data['new_phone_no'];
					$this->config->set('app.accountInfo', $this->userSess);
					$this->session->set($this->sessionName,$this->userSess);
					$this->session->forget('ChangeMobileOTP');
			        $this->session->forget('ChangeMobileVerify');
					$op['new_phone_no']=$data['new_phone_no'];
					$op['url']= route('seller.dashboard');
					$op['msg'] = trans('seller/account.mobile_updated');
					$this->statusCode = $this->config->get('httperr.SUCCESS');
				}
				else
				{
					$this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
					$op['msg'] = trans('general.no_changes');
				}
			}
			else
			{
				$op['msg'] = trans('general.invalid', ['which'=>trans('general.otp')]);
				$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			}
		}
		else
		{
			$op['msg'] = trans('general.not_accessable');
			$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		}
	}
	else
	{
		$op['msg'] =trans('seller/account.already_exist');
		$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
	}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
  
    public function saveProfilePin ()
    {
        $op = $data = [];
	    if (empty($this->userSess->security_pin))
        {
            $data['profile_pin'] = $this->request->profile_pin;
            $data['account_id'] = $this->account_id;
            if (!empty($this->accObj->saveProfilePIN($data)))
            {					
                $this->user_details->security_pin = md5($this->request->profile_pin);
                $this->userSess->security_pin = md5($this->request->profile_pin);
				$this->config->set('app.accountInfo', $this->userSess);
				$this->session->set($this->sessionName, $this->userSess);                
                $op['msg'] = trans('seller/general.profile_pin_updated_successfully');
                $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['msg'] = trans('general.something_wrong');
                $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
			
            $op['msg'] = trans('seller/general.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
       return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	public function account_settings(){
		$data = [];
		$data['profile_pin_verify_fileds'] = CommonNotifSettings::getHTMLValidation('seller.verify-profile-pin');
		//$data['profile_pin_forgot_fileds'] = CommonNotifSettings::getHTMLValidation('seller.profile-settings.profile-pin.reset');		
		$data['security_pin_cfileds'] = CommonNotifSettings::getHTMLValidation('seller.account-settings.change-pin');
		$data['security_pin_sfileds'] = CommonNotifSettings::getHTMLValidation('seller.security-pin.save');	
		$data['security_pin_rfileds'] = CommonNotifSettings::getHTMLValidation('seller.security-pin.reset');
		$data['change_email_newemail_fileds'] = CommonNotifSettings::getHTMLValidation('seller.profile-settings.change-email.new-email-otp');
		$data['change_email_confirm_fileds'] = CommonNotifSettings::getHTMLValidation('seller.profile-settings.change-email.confirm');
		$data['change_mobile_confirm_fileds'] = CommonNotifSettings::getHTMLValidation('seller.profile-settings.change-mobile.confirm');		
		return view('seller.account_settings.settings',$data);
	}
	
	public function verifyEmailLink ($token)
	{
		$data = $sdata = [];
		$data['link']= '';
        $data['change_email'] = false;
        if(!empty($token) && strpos($token, '.'))
        {
            $access_token = explode('.', $token);
            $this->session->setId($access_token[0], true);
			
            if($this->session->has('changeEmailID'))
            { 
                $sdata = $this->session->get('changeEmailID');			  
                $account_id = (isset($this->userSess->account_id) && !empty($this->userSess->account_id)) ? $this->userSess->account_id : '';
				$data['btnMsg'] =($sdata['account_id'] == $account_id) ? 'Click Here to Home' : 'Click Here to Login';
				if($sdata['hash_code'] == $access_token[1])
				{	
					if($this->accObj->changeEmailID($sdata))
					{
				        //$data['btnMsg'] = 'Click Here to Login';                    
						if ($sdata['account_id'] == $account_id)
						{                               
							$this->userSess->email = $sdata['new_email'];
							$this->userSess->is_email_verified = $this->config->get('constants.OFF');
							$this->config->set('app.accountInfo', $this->userSess);
							$this->session->set($this->sessionName, $this->userSess);	
						}	
						
						$data['email'] = $sdata['new_email'];
						$data['account_id'] = $sdata['account_id'];	
						$data['supplier_id'] = $sdata['supplier_id'];	
						$data['full_name'] = $sdata['full_name'];	
						$op['code'] = $data['code'] = rand(100000, 999999);
						$op['hash_code'] = $data['hash_code'] = md5($data['code']);		
						$this->session->set('email_verification', $data);				
						$token = $this->session->getId().'.'.$op['hash_code'];
						$data['link'] = $op['link'] = $verify_link = url('seller/verifyNewEmailLink/'.$token);
						CommonNotifSettings::notify('CHANGE_NEW_EMAIL_OTP',$sdata['account_id'], Config::get('constants.ACCOUNT_TYPE.SELLER'),['code'=>$data['code'], 'email_verify_link'=>$verify_link, 'email'=>$sdata['new_email'], 'full_name'=>$sdata['full_name']],
						true, false, false, true, false); 		
						$data['change_email'] = true;
						$this->statusCode = $this->config->get('httperr.SUCCESS');
						$email = maskEmail($data['email']);
						$op['msg'] = trans('seller/account.code_sent_to_email', ['email'=>$email]);
						$data['msg'] = trans('seller/account.old_email_verify',['email'=>$email]);
					}
					else
					{
						//$data['btnMsg'] = 'Click Here to Home';
						$data['msg'] = trans('seller/account.email_not_verify');
					}	
				}
				else
				{
					$data['change_email'] = false;
					$data['msg'] = trans('seller/account.verifyemail_sess_expire');
				} 
            }
            else
            {
				$data['change_email'] = false;
				$data['msg'] = trans('seller/account.verifyemail_sess_expire');                
            }
        }
        else
        {  
	        $data['change_email'] = false;
			$data['msg'] = trans('seller/account.verifyemail_sess_expire');          
        }		
		return view('seller.account_settings.change_email_confirm', (array) $data);	  
	}
	
	public function verifyNewEmailLink($token)
	{
		$data = $sdata = [];   
        if(!empty($token) && strpos($token, '.'))
        {
            $access_token = explode('.', $token);
            $this->session->setId($access_token[0], true);
            if($this->session->has('email_verification'))
            { 
                $sdata = $this->session->get('email_verification');
                $account_id = (isset($this->userSess->account_id) && !empty($this->userSess->account_id)) ? $this->userSess->account_id : '';
				$data['btnMsg'] =($sdata['account_id'] == $account_id) ? 'Click Here to Home' : 'Click Here to Login';
				if($sdata['hash_code'] == $access_token[1])
				{				  
					if ($this->commonObj->verifyEmail($sdata['account_id']))
					{                            
						//$data['btnMsg'] = trans('retailer/account.login_btn');   
						$this->accObj->UpdateCompletedSteps(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.EMAIL_VERIFICATION') ,
													'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),
													'supplier_id'=>$sdata['supplier_id'],
													'account_id'=>$sdata['account_id']]); 		
						if ($sdata['account_id'] == $account_id)
						{
							$this->userSess->is_email_verified = $this->config->get('constants.ON');
							$this->config->set('app.accountInfo', $this->userSess);
							$this->session->set($this->sessionName, $this->userSess);
							//$data['btnMsg'] = trans('retailer/account.home_btn');
						}
						$this->session->forget('email_verification');  //
						$data['verify_new_email'] = true;
						$data['msg'] = trans('seller/account.emailid_verified');
					}
					else
					{
						//$data['btnMsg'] = trans('retailer/account.home_btn');
						$data['msg'] = trans('seller/account.email_not_verify');
					}
				}				
				else
				{
					$data['verify_new_email'] = false;
					$data['msg'] = trans('seller/account.verifyemail_sess_expire');
				}  
            }
            else
            {
				$data['verify_new_email'] = false;
				$data['msg'] = trans('seller/account.verifyemail_sess_expire'); 
            }
        }
        else
        {  
	        $data['verify_new_email'] = false;
			$data['msg'] = trans('seller/account.verifyemail_sess_expire');          
        }		
		return view('seller.account_settings.change_new_email_confirm', (array) $data);	
	}
	
	/* NOTIFICATIONS  */
	public function getNotifications ()
    {
        $op = $data = $op['notifications'] = [];
        //print_r($this->config->get('session.expire_on_close'));exit;       
        $op['count'] = 0;
        $data['account_id'] = $this->userSess->account_id;
        $data['start'] = 0;
        $data['length'] = 10;
        $op['notifications'] = $this->commonObj->getNotifications($data);
        $op['count'] = $this->commonObj->getNotifications($data, true);
        $this->statusCode = $this->config->get('httperr.SUCCESS');	
		return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	/* Mark As Read Notifications */
	public function markNotificationRead ()
    {
        $op = $data = [];
        $post = $this->request->all();	
        $data['account_id'] = $this->userSess->account_id;
        $data['notification_id'] = $this->request->id;
		if(!empty($data['notification_id']))
		{
			if ($this->commonObj->markNotificationRead($data))
			{
				$data['notification_id'] ='';
				$op['notifications'] = $this->commonObj->getNotifications($data);
				$op['count'] = $this->commonObj->getNotifications($data, true);
				$this->statusCode = $this->config->get('httperr.SUCCESS');
			}
			else
			{
				$op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
				$op['msg'] = 'No records found!';
			}
		}
		else
		{
	        $op['msg'] = 'Parameter Missing..';
			$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
		}
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	/* Update Notification Token */
	public function updateNotificationToken ()
    {
        $op = [];
        $data = $this->request->all();
        if (isset($this->userSess->account_id))
        {
            $data['account_id'] = $this->userSess->account_id;
            $data['account_log_id'] = $this->userSess->account_log_id;
        }
        if ($this->commonObj->updateNotificationToken($data))
        {
            $this->userSess->fcm_registration_id = $data['fcm_registration_id'];
            $this->session->set($this->sessionName, $this->userSess);
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['msg'] = ''; //trans('general.updated', ['which'=>'FCM ID', 'what'=>trans('general.actions.updated')]);
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
            $op['msg'] = ''; //trans('general.already', ['which'=>'FCM ID', 'what'=>trans('general.actions.updated')]);
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }	
}