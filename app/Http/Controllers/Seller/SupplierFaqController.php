<?php

namespace App\Http\Controllers\Seller;
use App\Http\Controllers\SupplierBaseController;
use App\Models\Seller\SupplierFaq;
use Config;
use Lang;
use Redirect;

class SupplierFaqController extends SupplierBaseController
{
    public function __construct ()
    {
        parent::__construct();
        //$this->checkUsrLogin();
        $this->faqObj = new SupplierFaq();
    }

    public function view_faq ()
    {
        $data['faqs'] = $this->faqObj->view_faq();
        return View::make('supplier.faq.view_faq', $data);
    }

}
