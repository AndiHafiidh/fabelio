<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends API_Controller {

	public function __construct()
	{		
		parent::__construct();
	}

	public function index(){        
		$params = (object)[
			"title" => "Welcome to Fabelio apps API",
			"status" => 200,
			"message" => "Connected to API's"	
		];

		$this->response = $this->res->initialize($params);
		$this->get_response(true);
	}		
}
