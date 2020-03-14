<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Response
{

	public $title;
	public $message;
	public $status;	
	public $data;
	public $info;
	public $meta;

	private $CI;

	public function __construct(){
		
		$this->CI =& get_instance();		
		$this->CI->benchmark->mark('code_start');			
	}	

	public function initialize($params = null){

		$this->title = !empty($params->title)?$params->title:"New Response";

		$this->message = !empty($params->message)?$params->message:"Create new response success";				

		$this->status = new ResponseStatus(
			!empty($params->status->error)?$params->status->error:false,
			!empty($params->status->code)?$params->status->code:200
		);		

		$this->data = !empty($params->data)?$params->data:array();

		$info_pagination = new InfoPagination(
			!empty($params->info->pagination->status)?$params->info->pagination->status:false,
			!empty($params->info->pagination->page)?$params->info->pagination->page:1,
			!empty($params->info->pagination->total)?$params->info->pagination->total:1
		);							

		$info_data = new InfoData(
			!empty($params->info->data->shown)?$params->info->data->shown:0,
			!empty($params->info->data->total)?$params->info->data->total:0
		);

		$info = new ResponseInfo($info_pagination, $info_data);

		
		$this->CI->benchmark->mark('code_end');
		
		$info->executionTime = $this->CI->benchmark->elapsed_time('code_start', 'code_end');		

		$this->info = $info;

		$this->meta = !empty($params->meta)?$params->meta:new StdClass();

		return $this;
	}
	
}

class ResponseStatus 
{
	public $error;
	public $code;

	public function __construct($error, $code) {
		$this->error = $error;
		$this->code = $code;
	}
}

class InfoPagination
{
	public $status;
	public $page;
	public $total;

	public function __construct($status, $page, $total)
	{
		$this->status = $status;
		$this->page = $page;
		$this->total = $total;
	}	
}

class InfoData
{
	public $shown;
	public $total;

	public function __construct($shown, $total)
	{
		$this->shown = $shown;
		$this->total = $total;
	}	
}

class ResponseInfo
{
	public $pagination;
	public $data;

	public function __construct($pagination, $data)
	{
		$this->pagination = $pagination;
		$this->data = $data;
	}
}
/* End of file Response */
/* Location: ./application/libraries/Response */
