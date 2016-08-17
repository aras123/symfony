<?php

namespace Acme\Bundle\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    public $view_data;

    public function __construct()
    {
    	//default settings

    	/** breadcrumbs **/
    	$this->view_data['breadcrumbs'][0] = array('title'=>'<i class="fa fa-home" style="font-size:16px;"></i>','url'=>'/');
    }
}
