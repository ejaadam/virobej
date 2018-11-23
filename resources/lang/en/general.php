<?php

return [
	'stores'=>[
		'images'=>[
            'publish'=>'Publish',
            'unpublished'=>'Unpublish',
            'outlet_img'=>'Outlet image',
            'merchant_name'=>'Merchant Name',
            'outlet_imgs'=>'Outlet images',
            'category_name'=>'Category Name',
            'approve'=>'Approve',
            'pending'=>'Pending',
            'verify'=>'Verify',
            'unverify'=>'Unverify',
            'reject'=>'Reject',
            'status'=>[
                1=>'Publish',
                0=>'Unpublish',
            ],
            'is_verified'=>[
                0=>'Pending',
                1=>'Verified',
                2=>'Rejected'
            ],
        ],
	],
	'receipt'=>[
        //'email'=>'<p>Dear Member,</p></br><p>Your are successfully paid :amount of the :bill_amount for :store_name</p> <address>:address<br/>For enquiry: :mobile</address></p>',
        'email'=>'<p>Dear :fname,</p></br><p>You have successfully paid :bill_amount to :store_name, </p>:address<br/><p>Enjoy shopping with PayGyft</p>',
        'share'=>'Your made :amount for :store_name, :address. Contact: :mobile'
    ],
    'actions'=>[
        'already_exist'=>':label Already Exist',
        'added'=>':label Added Successfully',
        'updated'=>':label Updated Successfully',
        'status'=>['1'=>':label Activated Successfully', '0'=>':label Deactivated Successfully','2'=>':label Deactivated Successfully'],
        'verification'=>['1'=>':label Verified Successfully', '0'=>':label Unverified Successfully'],
        'deleted'=>':label deleted successfully',
        'not_avaliable'=>':label Not Avaliable'
    ],
	'already'=>':which already :what',
	'updateds'=>':which :what Successfully...',
	'delete_confirm_msg'=>'Are you sure, you wants to delete?',
	'delete'=>'Delete',
	'deleted'=>'Deleted',
	'location'=>'Location',
	'set_your_location'=>'Please choose your city.',
	'set_location'=>'Please set your location.',	
    'store'=>'Store',
    'not_an_affiliate_user'=>'You are not allowed to redeem. Since you are not an affiliate.',
	'store_count'=>'{0}|{1}Available in 1 location|[2,Inf] Available in :store_count locations',
	'likes'=>'{0} 0 Likes|[1,Inf] :likes Likes',
    'account'=>[
		'block'=>[0=>'Unblocked', 1=>'Blocked'],
        'created'=>'Account has been created successfully. Please check your inbox',
        'updated'=>'Account has been updated successfully.',
        'deactived'=>'Account Has been Deactivated',
        'not_exist'=>'Account ID Not Exists',
        'verified_and_email_sent'=>'Account Verified and email sent',
        'crfm_deactivate'=>'Confirm Deactivation',
        'information_deactivate'=>'  <p><b>When you deactivate your account</b></p>
                        <ul style="list-style-type:circle; padding:15px;">
                            <li>You are logged out of your :site_name Account</li>
                            <li>Your public profile on :site_name is no longer visible</li>
                            <li>Your reviews/ratings are still visible, while your profile information is shown as ‘unavailable’ as a result of deactivation.</li>
                            <li>Your wishlist items are no longer accessible through the associated public hyperlink. Wishlist is shown as ‘unavailable’ as a result of deactivation</li>
                            <li>You will be unsubscribed from receiving promotional emails from :site_name</li>
                            <li>Your account data is retained and is restored in case you choose to reactivate your account</li>
                        </ul>
                        <p ><b>How do I reactivate my :site_name account?</b></p><br />

                        <p>Reactivation is easy.</p><br />
                        <p>Simply login with your registered email id or mobile number and password combination used prior to deactivation. Your account data is fully restored. Default settings are applied and you will be subscribed to receive promotional emails from :site_name.</p><br />
                        <p>:site_name retains your account data for you to conveniently start off from where you left, if you decide to reactivate your account.</p>',
    ],
    'add'=>'Add',
	'ok'=>'OK',
    'adding_combination_failed'=>'Adding Combination Failed',
    'address_deleted_successfully'=>'Address Deleted Successfully',
    'address_marked_primary'=>'Address Marked Primary',
    'amount'=>'Amount',
    'amount_credited_successfully'=>'Amount Credited Successfully',
    'amount_debited_successfully'=>'Amount Debited Successfully',
    'appcode_not_found'=>'Appcode Not Found',
    'avaliable'=>'Available',
    'balanc_avalaible'=>'Balance Available',
    'block_deleted_successfully'=>'Block Deleted Successfully',
    'block_updated_successfully'=>'Block Updated Successfully',
    'call_me_back'=>'Call Me back',
    'cancel_btn'=>'Cancel',
    'package'=>[
        'added'=>'Package Added',
        'created'=>'Package Created Successfully.',
        'deleted'=>'Package Deleted Successfully',
        'purchased'=>'Package Purchased Successfully',
        'renewed'=>'Package Renewed Successfully',
        'updated'=>'Package Updated Successfully',
        'status'=>['1'=>'Package Activated Successfully', '0'=>'Package Deactivated Successfully'],
    ],
    'state'=>[
        'status'=>['1'=>'State Activated Successfully', '0'=>'State Deactivated Successfully'],
    ],
    'seller'=>[
	    'seller'=>'Seller',
		'seller_id'=>'Seller Id',
		'outlet_name'=>'Shop Name',
		'mobile'=>'Mobile',
		'phone'=>'Phone',		
		'manage_staff'=>'User Management',
        'merchant'=>'Merchant',
         'allocate'=>'Allocate',
		'login_access'=>'Login Access',
        'staff_Manage_stores'=>'Staff Managing Stores',
        'staff_list'=>'Manager Users',
		'store'=>'Shop/Code',
		'status'=>[
            2=>'Inactive',
            1=>'Active',
            0=>'Draft',
        ],
		'is_verified'=>[
            0=>'Not Verified',
            1=>'Verified',
            2=>'Rejected'
        ],
        'approved_list'=>'Supplier Approval List',
        'closed_list'=>'Supplier Closed List',
        'created'=>'Suppliers Created Successfully',
        'password_changed_successfully'=>'Supplier Password Changed Successfully',
        'updated'=>'Suppliers Updated Successfully',
        //'status'=>['1'=>'Supplier Activated Successfully', '0'=>'Supplier Deactivated Successfully'],
		'profit-sharing'=>[
            'title'=>'Profit Sharing',
            'profit_sharing'=>'Our Commission',
            'cashback_on_pay'=>'User Cashback on Pay',
            'cashback_on_redeem'=>'User Cashback on Redeem',
            'cashback_on_shop_and_earn'=>'User Cashback on Get Cashback',
            'profit_sharing_err'=>'Our Commission is required',
            'cashback_on_pay_err'=>'Percentage is required',
            'cashback_on_redeem_err'=>'Percentage is required',
            'cashback_on_shop_and_earn_err'=>'Percentage is required',
            'status'=>[
                0=>'Pending',
                1=>'Accept',
                2=>'Reject',
                3=>'Closed'
            ],
        ],		
		'outlet'=>[
			'is_approved'=>[
                0=>'Not Approved',
                1=>'Approved',
                2=>'Rejected'
            ],
            'status'=>[
                2=>'Inactive',
                1=>'Active',
                0=>'Draft',
            ],
			'details'=>'Shop Details',
			'address'=>'Address',
			'working_hours'=>'Working Hours',
			'title'=>'Shop Title',
			'description'=>'Shop Description',
		],		
    ],
    'customer'=>[
        'status'=>['1'=>'Customer Activated Successfully', '0'=>'Customer Deactivated Successfully'],
    ],
    'rating'=>[
        'status'=>['1'=>'Rating Activated Successfully', '0'=>'Rating Deactivated Successfully'],
    ],
    'wallet'=>[
        'status'=>['1'=>'Wallet Activated Successfully', '0'=>'Wallet Deactivated Successfully'],
    ],
    'category'=>[
        'add'=>'Add Category',
    ],
    'product'=>[
        'added'=>'Product Added Sucessfully',
        'updated'=>'Product Details Updated Successfully',
        'deleted'=>'Product Deleted Successfully',
        'status'=>['1'=>'Product Activated Successfully', '0'=>'Product Deactivated Successfully'],
        'invalid'=>'Invalid Product',
        'invalid_code'=>'Invalid Product Code',
        'already_exist'=>'Product Already Exist',
        'already_in_the_wish_list'=>'Product already in the wish list',
        'created'=>'Product Created Successfully',
        'may_not_in_your_wish_list'=>'Product may not in your Wish List',
        'not_exist_in_the_wishlist'=>'Product not exists in the wishlist',
        'not_found'=>'Product Not Found',
        'rating_deleted'=>'Product Rating Deleted',
        'removed'=>'Product Removed Succesfully.',
        'stock_updated_successfully'=>'Product Stock Updated Successfully',
        'added_to_cart'=>'Product Successfully added to cart',
        'added_to_with_list'=>'Product Successfully added to wish list',
        'removed_from_wish_list'=>'Product Successfully removed from your Wish List',
        'country'=>[
            'deleted'=>'Product Country Deleted Successfully',
        ]
    ],
    'brand'=>[
        'already_exist'=>'Brand Already Exist',
        'added'=>'Brand Added Successfully',
        'updated'=>'Brand Updated Successfully',
        'status'=>['1'=>'Brand Activated Successfully', '0'=>'Brand Deactivated Successfully'],
        'verification'=>['1'=>'Brand Verified Successfully', '0'=>'Brand Unverified Successfully'],
        'deleted'=>'Brand Deleted Successfully'
    ],
    'zone'=>[
        'charges'=>[
            'updated'=>'Zone Charge updated Successfully'
        ]
    ],
    'comment'=>[
        'deleted'=>'Comment Deleted Successfully'
    ],
    'menu'=>[
        'created'=>'Menu created successfully',
        'updated'=>'Menu updated Successfully',
        'already_exist'=>'Menu Aldready Exists',
        'deleted'=>'Menu Deleted Successfully',
        'nav'=>[
            'deleted'=>'Menu Navigation Deleted Successfully',
        ]
    ],
    'courier'=>[
        'deleted'=>'Courier Deleted Successfully',
        'mode'=>[
            'deleted'=>'Courier Mode Deleted Successfully',
        ]
    ],
    'combination_update_failed'=>'Combination Update Failed',
    'commission_updated_successfully'=>'Commissions Updated Successfully',
    'configuration_successfully_updated'=>'Configuration successfully Updated',
    'correct_password'=>'Correct Password',
    'could_not_change'=>'Could not change',
    'country_added_successfully'=>'Country Added Successfully',
    'cover_pictuer_updated_successfully'=>'Cover Picture Updated Successfully',
    'create_btn'=>'Create',
    'created_on'=>'Created On',
    'created_successfully'=>'Created Successfully',
    'currency'=>'Currency',
    'currency_deleted_successfully'=>' Currency Deleted Successfully',
    'currency_updated_successfully'=>'Currency Updated Successfully',
    'customer_enquiry'=>'Customer Enquiry',
    'dashboard'=>'Dashboard',
    'delivery_date'=>'Delievery Date',
    'details_not_found'=>'Details Not Found',
    'document'=>[
        'deleted'=>'Document Successfully Deleted',
        'acivated'=>'Document Successfully Activated',
        'deleted'=>'Document Successfully Deleted',
        'uploaded'=>'Document Uploaded Successfully',
    ],
    'review'=>[
        'deleted'=>'Review Deleted Successfully',
    ],
    'property'=>[
        'deleted'=>'Property Deleted Successfully',
        'value'=>[
            'deleted'=>'Property value Deleted Successfully',
        ]
    ],
    'ean_barcode_already_exist'=>'EAN Barcode Already Exists',
    'edit'=>'Edit',
    'edoc'=>'Expected Date of Credit',
    'email'=>'Email',
    'export_btn'=>'Export',
    'faq_are_created_successfully'=>'FAQ are created successfully',
    'feedback_submitted_failed'=>'Feedback Submitted Failed',
    'feedback_submitted_successfully'=>'Feedback Submitted Successfully',
    'frequently_asked_questions'=>'Frequently Asked Questions',
    'from_date'=>'From Date',
    'fund_added_successfully'=>'Fund Added Successfully',
    'geo_zone_created_successfully'=>'Geo Zone Created successfully',
    'home'=>'Home',
    'image'=>[
        'removed'=>'Image removed Successfully',
    ],
    'image_already_exist'=>'Image aldready exist',
    'image_deleted_successfully'=>'Image Deleted Successfully',
    'image_successfully_added'=>'Image Successfully added',
    'image_updated_successfully'=>'Image Settings Updated Successfully',
    'inactive_supplier_list'=>'Inactive Supplier List',
    'incorrect_old_new_password_are_same'=>'Incorrect Old Password/Old and New Password are same',
    'incorrect_password'=>'Incorrect Password',
    'incorrect_username'=>'Invalid Username',
    /* 'invalid_username_or_password'=>'Please enter valid Email/mobile and Password.', */
    'invalid_username_or_password'=>'Please enter valid username or password',
    'information_received'=>'Information Received',
    'insufficient_balance'=>'Insufficient Balance in Your account..!',
    'insufficient_balance_in_the_partner_wallet'=>'Insufficient Balance in partner Wallet',
    'invalid_verification_code'=>'Invalid Email Verification Link (or) Email Already Verified',
    'invalid_verification_code_for_mobile'=>'Enter Valid OTP',
    'no_change'=>'No changes were updated',
    'some_password'=>'Old and New Passwords are Same',
    'issues_related_queries_updated_by_expert_kindly_check_your_emails'=>'Issues Related queries Updated By Expert Kindly Check Your Emails.',
    'item_description'=>'Item Description',
    'items_you_need_assistance_with'=>'Item(s) you need assistance with',
    'logout_success'=>'Logout Successfully...',
    'mail_settings_update_verification_code'=>'Mail Settings Update Verification Code',
    'manage_email_template'=>'Manage E-mail Templates',
    'mandatory_fields_should_not_be_empty'=>'Mandatory fields should not be empty',
    'member'=>'Member',
    'message'=>'Message',
    'mobile_no_already_exist'=>'Mobile No. Aldready Exists',
    'mobile_no_email_not_registered'=>'Please enter a valid username',
    'mobile_no_or_email_already_exist'=>'Mobile and Email Aldready Exists',
    'my_orders'=>'My Orders',
    'new_address_saved_successfully'=>'New Address Saved Successfully',
    'new_password_has_been_updated_successfully'=>'New Password has been updated succesfully',
    'new_email_success'=>'New Email has been updated succesfully',
    'new_email_different'=>'New Email must be different from the old email',
    'no_balance'=>'No Balance',
    'no_fields_found'=>'No Fields Found',
    'no_records_were_found'=>'No records were found',
    'not_avaliable'=>'Not Avaliable...',
    'not_avaliable_to_purchase'=>'Not avaliable to purchase...',
    'old_password_doest_matched_with_the_new_password'=>'Old Password does\'t matched with the new password',
    'order_details'=>' Order\'s Details',
    'order_item_from'=>' Order Items From',
    'page_informations_are_created_successfully'=>'Page Informations are created successfully',
    'paid_amt'=>'Paid Amount',
    'parameteres_missing'=>'Parameters Missing...',
    'partner_account_created_success'=>'Account has been created successfully. Please check your inbox',
    'password_updatation_failed_please_tyr_again'=>'Password Updation Failed. Please try again.',
    'payment_mode'=>'Payment Mode',
    'plan_not_added'=>'Plan Not Added',
    'plan_successfully_added'=>'Plan Successfully Added',
    'please_enter_ean_or_ipc_code'=>'Please enter EAN or IPC Code',
    'please_enter_new_pssword'=>'Please Enter new password',
    'please_enter_the_menu_name'=>'Please Enter Menu Name',
    'please_enter_the_menu_position'=>'Please Select Menu Position',
    'please_login_to_proceed'=>'Please Login to Proceed...',
    'please_select_yoour_payment_mode'=>'Please select payment mode..',
    'please_wait'=>'Please Wait...',
    'price'=>'Price',
    'price_updated_successfully'=>'Price Updated Successfully',
    'print_btn'=>'Print',
    'property_deleted_successfully'=>'Property Deleted Successfully',
    'property_updated_successfully'=>'Property Updated Successfully',
    'remarks'=>'Remarks',
    'replied_successfully'=>'Replied Successfully',
    'request_a_callback'=>'REQUEST A CALLBACK',
    'requested_on'=>'Requested On',
    'reset_btn'=>'Reset',
    'save'=>'Save',
    'updt_email_mob'=>'Update Email/Mobile',
    'updating_new_email_mob'=>'Enter the new Email ID / Mobile number you wish to associate with your  account.',
    'information_email_mob'=>"<p ><b>What happens when I update my email address (or mobile number)?</b><br />
Your login email id (or mobile number) changes, likewise. You'll receive all your account related communication on your updated email address (or mobile number).</p>
<p><b>When will my account be updated with the new email address (or mobile number)?</b><br />
It happens as soon as you confirm the verification code sent to your email (or mobile) and save the changes.</p>
<p><b>What happens to my existing account when I update my email address (or mobile number)?</b><br />
Updating your email address (or mobile number) doesn't invalidate your account. Your account remains fully functional. You'll continue seeing your Order history, saved information and personal details.</p>
<p><b>Does my Seller account get affected when I update my email address?</b><br />
has a 'single sign-on' policy. Any changes will reflect in your Seller account also.</p>",
    'search'=>'Search',
    'save_btn'=>'Save',
    'search_btn'=>'Search',
    'search_ph'=>'Search',
    'search_terms'=>'Search terms',
    'select'=>'Select',
    'select_country'=>'Select Country',
    'select_state'=>'Select State',
    'select_status'=>'Select Status',
    'sku_already_exist'=>'SKU Aldready Exists',
    'slider_updated_successfully'=>'Slider Updated Successfully',
    'something_went_wrong'=>'Something Went Wrong...',
    'state_added_successfully'=>'State Added Successfully',
    'status'=>'Status',
    'submit'=>'Submit',
    'submit_btn'=>'Submit',
    'subscribe_successfully'=>'You are Subscribed Successfully',
    'unsubscribe_successfully'=>'We missed you, You are Unsubscribed Successfully',
    'tax_added_successfully'=>'Tax Added Successfully',
    'tax_class_added_successfully'=>'Tax class added Successfully',
    'tax_class_updated_successfully'=>'Tax class Updated Successfully',
    'tax_deleted_successfully'=>'Tax Deleted Successfully',
    'tax_deleted_successfully'=>'Tax Deleted Successfully', 'zone_deleted_successfully'=>'Zone Deleted Successfully',
    'tax_updated_successfully'=>'Tax Updated Successfully',
    'thank_tou_for_ordering'=>'Thank You for ordering...',
    'thanks_for_abusing'=>'Thanks for Abusing!',
    'thanks_for_submitting_your_enquires'=>'Thanks for submitting your enqueries',
	'no_changes'=>'No changes were made',
    'there_is_no_changes'=>'There is no changes',
    'these_fileds_are_not_found_in_the_templates'=>'These fields are not found in the templates',
    'this_combination_already_exist'=>'This Combination is Already Exist',
    'this_country_is_already_exist'=>'This Country is Already Exist',
    'to_date'=>'To Date',
    'topup_created_successfully'=>'Topup Created Successfully',
    'topup_deleted_successfully'=>'Topup Deleted Successfully',
    'topup_edited_successfully'=>' Topup Edited Successfully',
    'topup_successfully_done'=>'Topup Successfully Done',
    'track'=>'Track',
    'transaction_id'=>'Transaction ID',
    'transferred_on'=>'Transferred On',
    'unlimited'=>'Unlimited',
    'upc_barcode_already_exist'=>'UPC Barcode Already Exists',
    'update_btn'=>'Update',
    'update_your_package_to_add_more_products'=>'Update your package to add more products',
    'updated_successfully'=>'Updated Successfully...',
    'user_avaliable'=>'User Available',
    'verification_code_has_been_sent_to_yo_mobile_no'=>'One Time Password(OTP) has been sent to your Mobile No :mobile.',
    'verification_code_has_been_sent_to_your_email_id'=>'Verification Mail has been sent to your Email ID',
    'verify_the_combination_first'=>'Verify the Combinations First',
    'wallet'=>'Wallet',
    'wallet_updated_successfully'=>'Wallet Updated Successfully',
    'we_cound_not_able_to_process_your_request'=>'We could not able to process your request.',
    'you_can_now'=>'You can now',
    'you_cant_change_this'=>'you cant change this',
    'you_cant_like_this_review'=>'You can\'t like this review',
    'you_cant_unlike_this_review'=>'You can\'t unlike this review',
    'you_have_successfully_purchased_a_new_package'=>'Your have successfully purchased a new package.',
    'your_account_has_been_blocked'=>'Your Account Has Been Blocked',
    'your_account_not_avaliable_or_deleted'=>'Your Account Not Available or deleted',
    'you_are_successfully_logged_in'=>'You are successfully logged in..',
    'your_bank_infomations_saved_successfully'=>'Your Bank information Saved successfully',
    'your_cant_change_this_address'=>'You cant change this address',
    'your_comments_replied_successfully'=>'Your comments Replied successfully.',
    'your_email_id_is_verified'=>'Your Email ID is Verified',
    'your_gross_period_already_expired_if_you_like_to_activate_the_plan_kindly_purchase_the_new_plan_and_try'=>'Your Gross Period Already Expired If You Like To Activate The Plan  Kindly purchase The New Plan And Try It',
    'your_issues'=>'Your Issues',
    'your_order_has_been_cancelled_successfully'=>'Your Order has been Cancelled Successfully',
    'your_order_has_been_placed_successfully'=>'Your order succesfully placed',
    'your_package_has_been_renewed_successfully'=>'Your package has been Renewed Successfully',
    'your_password_has_been_updated_successfully'=>'Your Password has been Updated Successfully',
    'your_phone_number'=>'Your Phone No.',
    'your_product_could_not_be_cancelled'=>'Your Product could not be Cancelled',
    'your_product_has_an_faq'=>'Your product has an Faq',
    'your_product_has_been_cancelled_successfully'=>'Your Product has been Cancelled Successfully',
    'your_product_has_been_reviewed'=>'Your product has been reviewed',
    'your_requested_product_returned_successfully'=>'Your Request Product Return Successfully',
    'zone_updated_successfully'=>'Zone Updated Successfully',
   /*  'not_found'=>'Not Found', */
	'not_found'=>':which not found!',
    'add_fund'=>'Add Fund',
    'correct_password'=>'Correct Password',
    'assoc_update'=>'Association Details Updated Successfully',
    'payment_ins'=>'Payment Types Updated Successfully',
    'mobile_is_already_verified'=>'Your Mobile No. is Already Verified',
    'your_mobile_no_is_verified'=>'Congratulations! Your Mobile No. has been Verified',
    'validation'=>[
        'record_filters'=>[
            'search_term.alpha_num'=>'Invalid character input',
            'from.date_format'=>'Invalid date format, date format must be dd-mm-yyyy',
            'to.date_format'=>'Invalid date format, date format must be dd-mm-yyyy',
            'from.before'=>'Date must be lesser then to date',
            'to.after'=>'Date must be greater then from date'
        ],
        'login'=>[
            'username.required'=>'Username should not be empty',
            'username.regex'=>'Please enter a valid Username',
            'username.min'=>'Username should be min of :min characters',
            'username.max'=>'Username should be max of :max characters',
            'password.required'=>'Password should not be empty',
            'password.min'=>'Password should be min of :min characters',
            'password.max'=>'Password should be max of :max characters'
        ],
        'SUBSCRIBE'=>[
            'subscribe.email_id.required'=>'Email ID should not be empty',
            'subscribe.email_id.email'=>'Please enter a valid email ID',
            'subscribe.email_id.unique'=>'Email ID is already subscribed'
        ]
    ],
    'fields'=>[
        'account_holder_name'=>'Account Holder Name',
		'beneficiary_name'=>'Beneficiary Name',
		//'current_account_number'=>'Current Account Number',
        'full_name'=>'Full Name',
        'account_no'=>'Current Account Number',
        'confirm_account_no'=>'Confirm Account Number',
        'account_type'=>'Account Type',
        'bank_name'=>'Bank Name',
        'branch'=>'Branch Name',
		'branch_name'=>'Branch Name',
		'ifsc_code_details'=>'IFSC Code',
        'brand'=>'Brand',
        'city'=>'City',
        'company_name'=>'Company (Business Name)',
        'company_url'=>'Company URL',
        'store_url'=>'Store URL',
        'country'=>'Country',
        'email'=>'Business Email',
        'gender'=>'Gender',
        'dob'=>'DOB',
        'firstname'=>'First Name',
        'ifsc_code'=>'IFSC Code',
        'landline_no'=>'Landline No',
        'office_phone'=>'Landline No',
        'lastname'=>'Last Name',
        'mobile'=>'Mobile',
        'pan'=>'PAN No.',
        'password'=>'Password',
        'postal_code'=>'Postal Code',
        'reg_company_name'=>'Registered Company Name',
        'state'=>'State',
        'street1'=>'Address Line 1',
        'street2'=>'Address Line 2',
        'supplier_agree'=>'I\'ve read and accepted Terms & Conditions and agree to be contacted by Virob through E-mail, SMS or Phone.',
        'timing'=>'Timing',
        'type_of_bussiness'=>'Type of Business',
        'username'=>'Username',
		'email_or_mobile'=>'Email / Mobile',
        'verification_code'=>'Verification Code',
        'working_days'=>'Working Days',
        'pan_card_name'=>'Name on Pancard',
        'dob_on_pan'=>'DOB on Pancard',
        'pan_card_image'=>'Pancard Image',
        'vat_no'=>'VAT No.',
        'cst_no'=>'CST No.',
        'gstin'=>'GSTIN',
        'auth_person_name'=>'Authorize Person Name',
        'id_proof_type'=>'ID Proof Type',
        'auth_person_id_proof'=>'Upload Photo ID Proof',
        'store_name'=>'Store Name',
        'status'=>'Status',
        'verification_status'=>'Verification Status',
        'enable'=>'Enable',
        'disable'=>'Disable',
        'acc_deactivate'=>'Deactivate Account',
        'created_on'=>'Created On',
        'create_brand'=>'Create Brand',
        'category'=>'Business Category',
        'document'=>'Document',
        'pan_card_no'=>'Pan Card Number',
         
    ],
	'supplier_cashback_settings'=>[
        'pay'=>['label'=>'Accept Payment', 'notes'=>'Virob Members can make bill payment to you through credit/debit card and they get cashback'],
        'shop_and_earn'=>['label'=>'Offer Cashback', 'notes'=>'Virob Members can get Cashback on direct payments to you against the bill amount'],       
		'accept_vim'=>['label'=>'Accept Vi-Money', 'notes'=>'Virob Members can redeem Vi-Money against the bill amount base on the margin'],
        'accept_esp'=>['label'=>'Accept E-Shop Point', 'notes'=>'Virob Members can redeem E-Shop Point against the bill amount base on the margin'],
        'accept_bp'=>['label'=>'Accept Bonus Point', 'notes'=>'Virob Members can redeem Bonus Points against the bill amount base on the margin'],
		'accept_ngo'=>['label'=>'Accept NGO Wallet', 'notes'=>'Virob Members can make bill payment to you through NGO Wallet'],
		'accept_pw'=>['label'=>'Accept Purchase Wallet', 'notes'=>'Virob Members can make bill payment to you through Purchase Wallet'],
        'current_commission'=>[
            'title'=>'Current Running Offer',
            'profit_sharing'=>[
                'label'=>'Commission to Virob',
                'value'=>':value%'
            ],
            'period'=>[
                'label'=>'Period of Offer',
                'value'=>[1=>':from - :to', 0=>'Not Applicable']
            ]
        ],
        'pending_request'=>[
            'title'=>'New Request',
            'status'=>[
                0=>'Pending',
                1=>'Approved',
                2=>'Rejected',
                3=>'Closed',
                4=>'Cancelled'
            ],
            'profit_sharing'=>[
                'label'=>'Commission to Virob',
                'value'=>':value%'
            ],
            'period'=>[
                'label'=>'Period of Offer',
                'value'=>[1=>':from - :to', 0=>'Not Applicable']
            ]
        ],
        'new'=>true,
		'cashback'=>[
			'success'=>'Cashback Added Successfully.'
		]
    ],
	'label'=>[
	    'store'=>'Store',
		'search_term'=>'Search Term',
		'from_date'=>'From Date',
		'to_date'=>'To Date',
		'created_on'=>'Created On',
		'logo'=>'Logo',
		'outlet_name'=>'Shop Name',
		'country'=>'Country',
		'status'=>'Status',
		'approval'=>'Approval',
		'outlet'=>'Shop',
		'updated_by'=>'Updated By',
		'action'=>'Actions',
		'verification_code'=>'Verification Code',
        'verification_OTP'=>'Verification OTP',
	],
	'btn'=>[
		'search'=>'Search',
		'reset'=>'Reset',
		'add'=>'Add',
		'view'=>'View',
		'edit'=>'Edit',
		'activate'=>'Activate',
		'deactivate'=>'Deactivate',
		'close'=>'Close',
		'save'=>'Save',
		'confirm'=>'Confirm',
        'continue'=>'Continue',
        'submit'=>'Submit',
		'assign'=>'Assign',
		'delete'=>'Delete',
	],
	'outlet'=>[
		'images'=>[
            'publish'=>'Publish',
            'unpublished'=>'Unpublish',
            'outlet_img'=>'Shop image',
            'merchant_name'=>'Merchant Name',
            'outlet_imgs'=>'Shop Images',
            'category_name'=>'Category Name',
            'approve'=>'Approve',
            'pending'=>'Pending',
            'verify'=>'Verify',
            'unverify'=>'Unverify',
            'reject'=>'Reject',
            'status'=>[
                1=>'Publish',
                0=>'Unpublish',
            ],
            'is_verified'=>[
                0=>'Pending',
                1=>'Verified',
                2=>'Rejected'
            ],
        ],
	],
	'updated'=>'Updated Succesfully',
	'store_timings_web'=>[
        'always'=>'<ul class="list-group"><li class="list-group-item"><b>Always</b></li></ul>',
        'closed'=>'<span>:days</span> - Closed',
        'splited_close'=>':days',
        'split'=>'<span>:days:</span> :from_time - :to_time',
        'splited_time'=>':days</li><li class="list-group-item"><span>&nbsp;</span> :from_time - :to_time',
    ],
	'affiliates'=>[
        'affliate'=>'Affiliate',
        'affliate_network'=>'Affiliate Network',
        'featured'=>[
            1=>'Featured',
            0=>''
        ],
        'status'=>[
            1=>'Active',
            0=>'Inactive'
        ],
        'network'=>[
            'status'=>[
                1=>'Active',
                0=>'Inactive'
            ]
        ],
    ],
	
	'user'=>[
        'status'=>[
            1=>'Active',
            0=>'Inactive'
        ],
        'network'=>[
            'status'=>[
                1=>'Active',
                0=>'Inactive'
            ]
        ],
    ],
	'in-store'=>'Shop',
	'outlet_details'=>'Shop Details',
	'not_update'=>'Not Update',
	'category_name'=>'Category',
	'phone_lbl'=>'Phone No.',
	'no_outlet_images'=>'No. of Shop Images',
	'outlet_code'=>'Shop Code',
	'email_lbl'=>'Email-ID',
	'mobile_lbl'=>'Mobile No.',
	'approval'=>'Approval',
	'location'=>'Location',
	'something_wrong'=>'Something Went Wrong.',
	'outlet_not_found'=>'Shop not found',
	'payment_modes'=>[1=>'CASH', 2=>'Vi CASH', 3=>'CREDIT CARD', 4=>'DEBIT CARD', 5=>'NET BANKING'],	
	'store_not_offering_this_service'=>'Store not offering this service',
	'email_verified'=>'Email Verified successfully',
	'invalid_otp'=>'Invalid OTP',
	'invalid'=>'Invalid :which',
	'outlets'=>'Shop',
	'not_accessable'=>'Not accessible right now',
	'otp'=>'OTP',
	'store_timing_info'=>[
        'always'=>'<ul><li>Always</li></ul>',
        'closed'=>'<li>:days - Closed</li>',
        'split'=>[
            0=>'<li>:days :from_time - :to_time</li>',
            1=>'<li>:days :from_time - :to_time and :from_time - :to_time</li>'
        ]
    ],
	'order_payment_status'=>[
        1=>'Success',
        0=>'Pending',
        2=>'Cancelled',
        3=>'Failled',
        4=>'Refunded',
    ],
	'transactions'=>[
        'status'=>[
            1=>'Success',
            0=>'Pending',
            2=>'Cancelled'
        ],
        'status_class'=>[
            0=>'warning',
            1=>'success',
            2=>'danger'
        ],
    ],
	'order'=>[
		'pay_through'=>[
            1=>[
                1=>'XPay',
                2=>'Redemption',
                3=>'Cashback'
            ],
            2=>[
                0=>'Deal Purchase - Xpay',
                1=>'XPay',
                2=>'Redemption',
                3=>'Shop & Earn'
            ],
            3=>[
                1=>'Coupon Purchase'
            ]
        ],
	],
	'confirm'=>'Are you sure, You wants to :what?',
];
