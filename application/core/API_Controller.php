<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use \Firebase\JWT\JWT;
use Carbon\Carbon;

class API_Controller extends CI_Controller {

	public $response;
	public $table;
	public $header;	
	public $connection;
	public $allowed = [];	
	
	public $secondary_db = "db";

	public function __construct()
	{		
		parent::__construct();		
		$this->load->model('model');		
		$this->load->library('user_agent');

		$this->header = $this->input->request_headers();		
		
		date_default_timezone_set('UTC');
		Carbon::setLocale('id');		
	}	

	protected function set_connection()
	{
		$config['hostname'] = $this->connection->mysql_host;
		$config['username'] = $this->connection->mysql_user;
		$config['password'] = $this->connection->mysql_pass;
		$config['database'] = $this->connection->mysql_db;
		$config['dbdriver'] = 'mysqli';
		$config['dbprefix'] = '';
		$config['pconnect'] = FALSE;
		$config['db_debug'] = TRUE;
		$config['cache_on'] = FALSE;
		$config['cachedir'] = '';
		$config['char_set'] = 'utf8';
		$config['dbcollat'] = 'utf8_general_ci';
		$this->model->secondary_db = $this->load->database($config, TRUE);

		$this->secondary_db = "secondary_db";
	}

	protected function key_check($api_key){
		return $this->model->select("system_key", "*", "api_key = $api_key AND status = 1 AND deleted_at IS NULL");		
	}
	
	public function create(){	
		$step = 0;		
		if(in_array(__FUNCTION__, $this->allowed)){
			$step = 1;
			$json = $this->input->raw_input_stream;
			$objects = json_decode($json);
			
			$this->response = $this->model->insert($this->table, $objects, $this->secondary_db);		
		}else{
			$this->response = $this->model->error(401, "Authentication failed" , "You're not allowed to do this!", __CLASS__ . '-' . __FUNCTION__, $step);	
		}
	
		$this->get_response(true);
	}

	public function read($id = null){
		$step = 0;		
		
		if (in_array(__FUNCTION__, $this->allowed)) {
			$step = 1;
			$json    = $this->input->raw_input_stream;

			$object = json_decode($json);
			if (!empty($object)) {
				$object->table = $this->table;
				$object->field = !empty($this->field)?$this->field:"*";
				$object->filter = !empty($object->filter)?$object->filter:"";
				$object->limit = !empty($object->limit)?$object->limit:0;
				$object->page = !empty($object->page)?$object->page:1;
				$object->sort = !empty($object->sort)?$object->sort:"created_at ASC";
				$object->group_by = !empty($object->group_by)?$object->group_by:null;
			} else {
				$object = new StdClass();
				$object->table = $this->table;
				$object->field = !empty($this->field)?$this->field:"*";
				$object->filter = null;
				$object->limit = null;
				$object->page = null;
				$object->sort = "created_at ASC";
				$object->group_by = null;
			}

			$step = 3; // Checking parameter
			
			// echo json_encode($object);
			// exit;
			
			if ($id) {
				$object->filter = "id = $id";
			}

			$response = clone($this->model->select(
				$object->table,
				$object->field,
				$object->filter,
				$object->limit,
				$object->page,
				$object->sort,
				$object->group_by,
				$this->secondary_db
			));

			if ($response && !($response->status->error)) {
				$this->response = $response;
			} else {
				$this->response = $this->model->error($response->status->code, "Getting your data failed", $response->message, __FUNCTION__, $step);
			}				
        }else{
			$this->response = $this->model->error(401, "Authentication failed" , "You're not allowed to do this!");			
		}

        $this->get_response(true);		
	}		

	public function update($id = NULL){
		$step = 0;
			
        if (in_array(__FUNCTION__, $this->allowed)) {
			$step = 1;
			if($id) {
				$step = 2;
				
				$step = 3;
				$json = $this->input->raw_input_stream;
				$object = json_decode($json);
	
				$this->response = $this->model->update($this->table, $objects, "id = $id", $this->secondary_db);
			
			}else{
				$this->response = $this->model->error(400, "Terjadi kesalahan" , "Parameter id harus diisi", __CLASS__ . '-' . __FUNCTION__, $step);
			}			          
        }else{
			$this->response = $this->model->error(401, "Authentication failed" , "You're not allowed to do this!", __CLASS__ . '-' . __FUNCTION__, $step);
		}
		$this->get_response(true);
	}

	public function delete($id = NULL){		
		$step = 0;

        if (in_array(__FUNCTION__, $this->allowed)) {
			$step = 1;
			if($id){
				$step = 2;			
				$this->response = $this->model->delete($this->table, "id = $id", $this->secondary_db);			
			}else{
				$this->response = $this->model->error(400, "Terjadi kesalahan" , "Parameter id harus diisi", __CLASS__ . '-' . __FUNCTION__, $step);
			}
        }else{
			$this->response = $this->model->error(401, "Authentication failed" , "You're not allowed to do this!", __CLASS__ . '-' . __FUNCTION__, $step);
		}
		$this->get_response(true);
	}
	
	public function db_check()
	{		
		$this->response = $this->model->db_check();		
		$this->get_response(true);
	}	

	private function setup_response(){
		header_remove();
		http_response_code($this->response->status->code);
		header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");


		$status = array(
			200 => '200 OK',
			400 => '400 Bad Request',
			401 => '401 Unauthorized',
			404 => '404 Not Found',
			422 => 'Unprocessable Entity',
			500 => '500 Internal Server Error'
		);

    	// ok, validation error, or failure
		header('Status: '.$status[$this->response->status->code]);	
		
		header('Content-Type: application/json');	
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-Api-Key, X-App-Key, X-App-Secret, Origin, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		$method = $_SERVER['REQUEST_METHOD'];
		if($method == "OPTIONS") {
			die();
		}
	}

	protected function get_response($json_result = TRUE){		
		if ($json_result) {
			$this->setup_response();					
			echo json_encode($this->response);			
		}else{			
			return $this->response;
		}		
	}		
	
	protected function generate_uuid()
	{
		try {			
			$uuid4 = Uuid::uuid4();
			return $uuid4->toString();
		} catch (UnsatisfiedDependencyException $e) {			
			return false;
		}
	}

	protected function create_url($string)
	{
	   	$string = str_replace(' ', '-', $string); // Replaces spaces with hyphens.
   		return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $string)); // Removes special chars.
	}

}

/* End of file API_Controller.php */
/* Location: ./application/core/API_Controller.php */