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
	// public $redis;
	public $auth;
	public $dblog;

	public $secondary_db = "db";

	public function __construct()
	{		
		parent::__construct();		
		$this->load->model('model');		
		$this->load->library('user_agent');

		$this->header = $this->input->request_headers();		
		
		date_default_timezone_set('UTC');
		Carbon::setLocale('id');

		$this->dblog = (object)[
			'id' => $this->generate_uuid(),
			'admin_id' => NULL,
			'member_id' => NULL,
			'sub' => ($this->uri->segment(1) == $this->router->fetch_class())?NULL:strtolower($this->uri->segment(1)),
			'class' => strtolower(__CLASS__),
			'function' => strtolower(__FUNCTION__),
			'parameter' => NULL,
			'payload' => NULL,
			'result' => NULL,
			'ip' => $_SERVER['REMOTE_ADDR'],
			'device' => $this->agent->agent_string()
		];	
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

	protected function token_check($jwt = NULL, $type = 'member'){
		$step = 0;
		if ($jwt) {
			$step = 1;
			$now = Carbon::now();
			$config = clone($this->model->select('system_config', '*', "name = jwt AND status = 1 AND deleted_at IS NULL"));
			if($config AND !$config->status->error){
				$step = 2;
				$conf = json_decode($config->data[0]->value);
				$token = explode(" ",$jwt);
				if ($token[0] === $conf->type) {
					$step = 3;
					try {						
						$result = NULL;
						switch ($type) {
							case 'admin':								
								$decoded = JWT::decode($token[1], $conf->bo, array($conf->algorithm));											
								$result = clone($this->model->select('admin_login', '*', "token = $decoded->token AND status = 1 AND expired_time > $now"));								

								break;
							default:
								$decoded = JWT::decode($token[1], $conf->key, array($conf->algorithm));	
								$result = clone($this->model->select('member_login', '*', "token = $decoded->token AND status = 1 AND expired_time > $now"));								
								break;
						}
						$step = 4;
						if ($result && !$result->status->error) {	
							$dt = $result->data[0];
							$data_user = NULL;
							
							switch ($type) {
								case 'admin':
									$data_admin = clone($this->model->select("v_admin", '*', "id = $dt->admin_id AND status != 2 AND deleted_at IS NULL"));
									if($data_admin AND !$data_admin->status->error){
										$data_user = $data_admin->data[0];
									}
									break;								
								default:
									$data_member = clone($this->model->select("v_member", '*', "id = $dt->member_id AND status != 2 AND deleted_at IS NULL"));
									if($data_member AND !$data_member->status->error){
										$data_user = $data_member->data[0];
									}
									break;
							}

							$data_user->token = $dt->token;
							$params = (object) [
								"title" => "Token check success",
								"status" => 200,
								"message" => "Here is the data",
								"data" => array(
									$data_user
								)
							];

							return $this->res->initialize($params);											
						}else{
							return $this->model->error(401, "Authentication failed" , "You haven't permission", __CLASS__ . '-' . __FUNCTION__, $step);							
						}
					} catch (Exception $e) {
						return $this->model->error(400, "Bad Request", $e->getMessage(), __CLASS__ . '-' . __FUNCTION__, $step);
					}			
				}else{
					return $this->model->error(401, "Authentication failed" ,"Unauthorized authentication type", __CLASS__ . '-' . __FUNCTION__, $step);
				}	
			}else{
				return $this->model->error(400, "Authentication failed" , "Configuration not found", __CLASS__ . '-' . __FUNCTION__, $step);
			}	
		}else{
			return $this->model->error(401, "Authentication failed" , "JWT not found", __CLASS__ . '-' . __FUNCTION__, $step);			
		}
	}

	protected function role_check($auth = NULL, $sub = NULL, $class = NULL, $function = NULL, $params = NULL, $type = 'member')
	{		
		$step = 0;
		if ($auth && $sub && $class && $function) {
			$step = 1;			
			$sub = strtolower($sub);
			$class = strtolower($class);
			$function = strtolower($function);
			switch ($type) {
				case 'admin':
					if($params) return $this->model->select('v_admin_role', "*", "admin_category_id = $auth->admin_category_id AND sub = $sub AND controller = $class AND function = $function AND params = $params AND is_allowed = 1");		
					return $this->model->select('v_admin_role', "*", "admin_category_id = $auth->admin_category_id AND sub = $sub AND controller = $class AND function = $function AND is_allowed = 1");		
					break;				
				default:
					if($params) return $this->model->select('v_member_role', "*", "member_category_id = $auth->member_category_id AND sub = $sub AND controller = $class AND function = $function AND params = $params AND is_allowed = 1");
					return $this->model->select('v_member_role', "*", "member_category_id = $auth->member_category_id AND sub = $sub AND controller = $class AND function = $function AND is_allowed = 1");
					break;
			}
		} else {
			return $this->model->error(400, "Terjadi kesalahan" , "Maaf terjadi kesalahan, silahkan coba beberapa saat lagi", __CLASS__ . '-' . __FUNCTION__, $step);
		}
	}

	protected function app_check($app_key, $app_secret){
		return $this->model->select("system_app", "*", "app_key = $app_key AND app_secret = $app_secret AND status = 1 AND deleted_at IS NULL");		
	}

	protected function key_check($api_key){
		return $this->model->select("system_key", "*", "api_key = $api_key AND status = 1 AND deleted_at IS NULL");		
	}
	
	public function create(){	
		$step = 0;		
		$this->dblog->function = strtolower(__FUNCTION__);
		if(in_array(__FUNCTION__, $this->allowed)){
			$step = 1;
			$check_role = clone($this->role_check($this->auth, $this->dblog->sub, $this->dblog->class, $this->dblog->function));
			if($check_role AND !$check_role->status->error){
				$step = 2;
				$json = $this->input->raw_input_stream;
				$objects = json_decode($json);
				
				$this->response = $this->model->insert($this->table, $objects, $this->secondary_db);
			}else{
				$this->response = $this->model->error(401, "Authentication failed" , "You're not allowed to do this!", __CLASS__ . '-' . __FUNCTION__, $step);
			}
		}else{
			$this->response = $this->model->error(401, "Authentication failed" , "You're not allowed to do this!", __CLASS__ . '-' . __FUNCTION__, $step);	
		}
	
		$this->get_response(true);
	}

	public function read($id = null){
		$step = 0;		
		$this->dblog->function = strtolower(__FUNCTION__);
		
		if (in_array(__FUNCTION__, $this->allowed)) {
			$step = 1;
			if($id) $this->dblog->parameter = $id;

			$check_role = clone($this->role_check($this->auth, $this->dblog->sub, $this->dblog->class, $this->dblog->function));
			if($check_role AND !$check_role->status->error){
				$step = 2;
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
				$this->response = $this->model->error(401, "Authentication failed" , "You're not allowed to do this!", __CLASS__ . '-' . __FUNCTION__, $step);
			}		
        }else{
			$this->response = $this->model->error(401, "Authentication failed" , "You're not allowed to do this!");			
		}

        $this->get_response(true);		
	}		

	public function update($id = NULL){
		$step = 0;
		$this->dblog->function = strtolower(__FUNCTION__);
		
        if (in_array(__FUNCTION__, $this->allowed)) {
			$step = 1;
			if($id) {
				$step = 2;
				$this->dblog->parameter = $id;

				$check_role = clone($this->role_check($this->auth, $this->dblog->sub, $this->dblog->class, $this->dblog->function));
				if($check_role AND !$check_role->status->error){
					$step = 3;
					$json = $this->input->raw_input_stream;
					$object = json_decode($json);
		
					$this->response = $this->model->update($this->table, $objects, "id = $id", $this->secondary_db);
				}else{
					$this->response = $this->model->error(401, "Authentication failed" , "You're not allowed to do this!", __CLASS__ . '-' . __FUNCTION__, $step);
				} 
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
		$this->dblog->function = strtolower(__FUNCTION__);

        if (in_array(__FUNCTION__, $this->allowed)) {
			$step = 1;
			if($id){
				$step = 2;
				$this->dblog->parameter = strtolower(__FUNCTION__);

				$check_role = clone($this->role_check($this->auth, $this->dblog->sub, $this->dblog->class, $this->dblog->function));
				if($check_role AND !$check_role->status->error){					
					$this->response = $this->model->delete($this->table, "id = $id", $this->secondary_db);
				}else{
					$this->response = $this->model->error(401, "Authentication failed" , "You're not allowed to do this!", __CLASS__ . '-' . __FUNCTION__, $step);
				}
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

	protected function get_response($json_result = TRUE, $save_log = TRUE, $type = 'admin'){		
		switch ($type) {
			case 'member':
				$table = 'member_log';
				unset($this->dblog->admin_id);
				break;
			default:
				unset($this->dblog->member_id);
				$table = 'admin_log';
				break;
		}

		if ($json_result) {
			$this->setup_response();

			$res = clone($this->response);
			$this->dblog->result = json_encode($res);

			$method = $_SERVER['REQUEST_METHOD'];
			if($method != "OPTIONS") {				
				if($save_log) $saved_log = clone($this->model->insert($table, $this->dblog));
			}

			echo json_encode($res);			
		}else{
			$res = clone($this->response);
			$this->dblog->result = json_encode($res);

			$method = $_SERVER['REQUEST_METHOD'];
			if($method != "OPTIONS") {				
				if($save_log) $saved_log = clone($this->model->insert($table, $this->dblog));
			}

			return $res;
		}		
	}	

	protected function code_generator($length = 6){
		$factory = new RandomLib\Factory;
		$generator = $factory->getGenerator(new SecurityLib\Strength(SecurityLib\Strength::MEDIUM));

		// return $generator->generateString($length, '0123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ');
		return $generator->generateString($length, '0123456789abcdefghijkmnpqrstuvwxyz');
	}

	protected function base_64_upload($sid, $image){

		$temp_file_path = $this->config->item('upload_dir');

		$name = str_replace(' ', '', $sid) . '-' . date("dmYhis") . '.' . 'png';		

		if ( ! file_put_contents($temp_file_path . $name , base64_decode($image)))
		{
			$this->response = $this->model->error(400, "Uploading file failed", "Your file didn't upload successfully");			
		}
		else
		{							
			$params = (object)[
				"title" => "Uploading file successfully",
				"status" => 200,				
				"message" => "File has been uploaded",
				"data" => $name
			];

			$this->response = $this->res->initialize($params);
		}

		return $this->response;		
	}
	
	protected function limit_words($text, $limit = 0)
	{	
		if (str_word_count($text, 0) > $limit) {
			$words = str_word_count($text, 2);
			$pos = array_keys($words);
			$text = substr($text, 0, $pos[$limit]) . '...';
		}
		return $text;		
	}	

	protected function file_delete($file){
		$temp_file_path = $this->config->item('upload_dir');
		unlink($temp_file_path . $file); 
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
   		return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}

}

/* End of file API_Controller.php */
/* Location: ./application/core/API_Controller.php */