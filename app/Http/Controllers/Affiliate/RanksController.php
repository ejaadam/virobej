<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\User;

class RanksController extends AffBaseController {
    
    private $userObj = '';
    
    public function __construct ()
    {
        parent::__construct();
        $this->affObj = new AffModel();
    }	
	
	public function myrank(){
		$data = array();
		return view('affiliate.bonus.myrank',$data);
	}
	
	public function myrank_history(){
		$data = array();
		return view('affiliate.bonus.myrank_history',$data);
	}
	
	public function eligibilities(){
		$data = array();
		return view('affiliate.bonus.eligibilities',$data);
	}
}