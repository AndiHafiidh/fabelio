<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Product extends API_controller {
	public $table = "product";	

	public function __construct()
	{
		parent::__construct();		
		$this->allowed = ['create', 'read', 'update'];
	}

	public function create()
	{
		# code...
	}


	
}

/* End of file Product.php */
/* Location: ./application/controllers/Product.php */