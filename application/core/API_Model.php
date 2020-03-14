<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Model extends CI_Model {

	public $table;	
	public $secondary_db;	

	private $SELECT = "Getting data ";
	private $FOUND = "Data found";
	private $NOT_FOUND = "Data not found";

	private $UPDATE = "Update data on ";
	private $UPDATE_SUCCESS = "Data successfully updated";
	private $UPDATE_FAILED = "Update data failed";

	private $DELETE = "Delete data on ";
	private $DELETE_SUCCESS = "Data successfully deleted";
	private $DELETE_FAILED = "Delete data failed";

	private $INSERT = "Insert data on ";
	private $INSERT_SUCCESS = "Data successfully inserted";
	private $INSERT_FAILED = "Insert data failed";	
	

	public function __construct()
	{
		parent::__construct();		
		$this->load->database();					
	}

	public function query($query, $db = "db"){		
		$query = $this->$db->query($query);

		if (gettype($query) != 'boolean') {
			return $query->result();		
		}else{
			return $query;
		}	
	}	

	public function insert(
		$table,
		$object,
		$db = "db"
	){		
		
		$insert_id = array();
		$affected_rows = 0;

		$status = new StdClass();
		$status->error = true;
		$status->code = 400;		

		$message = $this->INSERT_FAILED;


		if (is_array($object)) {
			foreach ($object as $item) {
				$this->$db->insert($table, $item);			
				$insert_id[] = $this->db->insert_id();	
				$affected_rows += $this->$db->affected_rows();
			}			
		}else{
			$this->$db->insert($table, $object);			
			$insert_id[] = $this->db->insert_id();
			$affected_rows += $this->$db->affected_rows();
		}

		if($this->$db->affected_rows() > 0){
			if ($affected_rows == count($insert_id)) {
				$status->error = false;
				$status->code = 200;		
				$message = $this->INSERT_SUCCESS;
			}
		}

		$params = (object)[
			"title" => $this->INSERT . $table,
			"status" => $status,
			"message" => $message,
			"data" => $insert_id			
		];		
		

		return $this->res->initialize($params);		
	}	

	public function select(
		$table, 			
		$field = '*',
		$filter = null,
		$limit = 0, 
		$page = 1, 
		$sort = null,			
		$group_by = null,
		$db = "db"		
	)
	{		
		$status = new StdClass();
		$status->error = true;
		$status->code = 404;		

		$message = $this->NOT_FOUND;
		
		$pagination = new StdClass();		
		$data = new StdClass();
										
		if ($field) {
			if ($field != '*') {											
				$this->$db->select($field);						
			}			
		}								

		if ($filter) {
			$arr_filter = explode(" AND ", $filter);

			foreach ($arr_filter as $item) {

				if(strpos($item, '!=')){
					$arr_item = explode(" != ", $item);	
					$this->$db->where($arr_item[0] . " != ", $arr_item[1]);
				}elseif (strpos($item, '=')) {
					$arr_item = explode(" = ", $item);	
					$this->$db->where($arr_item[0], $arr_item[1]);
				}elseif(strpos($item, 'LIKE')){
					$arr_item = explode(" LIKE ", $item);	
					$this->$db->like($arr_item[0], $arr_item[1], 'BOTH');					
				}elseif(strpos($item, 'IS')){					
					$this->$db->where($item);			
				}else{
					$arr_item = explode(" ", $item);	
					$this->$db->where($arr_item[0] . " " . $arr_item[1], $arr_item[2]);
				}	

			}
		}

		if($group_by){
			$arr_groupby = explode(', ', $group_by);

			$this->$db->group_by($arr_groupby);				
		}

		// get result
		$count = $this->$db->get($table)->num_rows();		

		if ($field) {
			if ($field != '*') {											
				$this->$db->select($field);						
			}			
		}		

		if ($filter) {
			$arr_filter = explode(" AND ", $filter);

			foreach ($arr_filter as $item) {

				if(strpos($item, '!=')){
					$arr_item = explode(" != ", $item);	
					$this->$db->where($arr_item[0] . " != ", $arr_item[1]);
				}elseif (strpos($item, '=')) {
					$arr_item = explode(" = ", $item);	
					$this->$db->where($arr_item[0], $arr_item[1]);
				}elseif(strpos($item, 'LIKE')){
					$arr_item = explode(" LIKE ", $item);	
					$this->$db->like($arr_item[0], $arr_item[1], 'BOTH');
				}elseif(strpos($item, 'IS')){					
					$this->$db->where($item);					
				}else{
					$arr_item = explode(" ", $item);	
					$this->$db->where($arr_item[0] . " " . $arr_item[1], $arr_item[2]);
				}				
			}
		}

		if ($sort) {
			$arr_sort = explode(",", $sort);		
			foreach ($arr_sort as $item) {
				$str = explode(" ", $item);
				$this->$db->order_by($str[0], $str[1]);		
			}
		}

		if($group_by){
			$arr_groupby = explode(', ', $group_by);

			$this->$db->group_by($arr_groupby);				
		}
		
		// setup offset
		$offset = 0;		
		if ($page <= 0) {
			$page = 1;			
		}
		if ($page > 1) {
			$offset = $limit * ($page - 1);
		}		

		$pagination->page = $page;
		
		// get result		



		$result = $this->$db->get($table, $limit, $offset);			

		if (gettype($result) != 'boolean') {
			// $rows = $result->num_rows();
			$datas = $result->result();

			// if ($limit > 0) {
			// 	$datas = array_slice($result->result(), $offset, $limit);
			// }

			if ($datas) {
				$message = $this->FOUND;
				$status->error = false;
				$status->code = 200;
				$pagination->total = ceil($count/($limit?$limit:$count));
				if ($pagination->total > 1) {
					$pagination->status = true;
				}
				$data->shown = count($datas);
				$data->total = $count;			
			}				
		}
				
		$params = (object)[
			"title" => $this->SELECT . $table,
			"message" => $message,
			"status" => $status,
			"data" => $datas,
			"info" => (object)[
				"pagination" => $pagination,
				"data" => $data
			]
			// "meta" => $this->$db->last_query()
		];

		return $this->res->initialize($params);		
	}

	public function update(
		$table,
		$object,
		$filter,
		$db = "db"
	){

		$status = new StdClass();
		$status->error = true;
		$status->code = 400;		

		$message = $this->UPDATE_FAILED;		

		if ($filter) {
			$arr_filter = explode("AND", $filter);

			foreach ($arr_filter as $item) {

				if (strpos($item, '=')) {
					$arr_item = explode(" = ", $item);	
					$this->$db->where($arr_item[0], $arr_item[1]);
				}elseif(strpos($item, 'LIKE')){
					$arr_item = explode(" LIKE ", $item);	
					$this->$db->like($arr_item[0], $arr_item[1], 'BOTH');					
				}				
			}
		}

		$this->$db->update($table, $object);	
		
		if($this->$db->affected_rows() >= 0){
			$status->error = false;
			$status->code = 200;		
			$message = $this->UPDATE_SUCCESS;		
		}		

		$params = (object)[
			"title" => $this->UPDATE . $table,
			"message" => $message,
			"status" => $status
		];

		return $this->res->initialize($params);	
	}

	public function delete(
		$table,
		$filter,
		$db = "db"
	){

		$status = new StdClass();
		$status->error = true;
		$status->code = 400;		

		$message = $this->DELETE_FAILED;		

		if ($filter) {
			$arr_filter = explode("AND", $filter);

			foreach ($arr_filter as $item) {

				if (strpos($item, '=')) {
					$arr_item = explode(" = ", $item);	
					$this->$db->where($arr_item[0], $arr_item[1]);
				}elseif(strpos($item, 'LIKE')){
					$arr_item = explode(" LIKE ", $item);	
					$this->$db->like($arr_item[0], $arr_item[1], 'BOTH');					
				}				
			}
		}

		$this->$db->delete($table);
		
		if($this->$db->affected_rows() > 0){
			$status->error = false;
			$status->code = 200;		
			$message = $this->DELETE_SUCCESS;		
		}
		
		$params = (object)[
			"title" => $this->DELETE . $table,
			"message" => $message,
			"status" => $status
		];

		return $this->res->initialize($params);	
	}

	public function db_check($db = "db"){	

		// return $this->$db;

		$this->table = $this->$db->list_tables();	

		$params = (object)[
			"title" => "Connected to " . strtoupper($this->$db->database),			
			"status" => (object)[
				"error" => false, 
				"code" => 200
			],
			"data" => $this->table,
			"info" => (object)[
				"data" => (object)[
					"shown" => count($this->table),
					"total" => count($this->table)
				]
			]
		];

		$params->message = "Checking database connection succesfull";

		return $this->res->initialize($params);		
	}

	public function error($code, $title, $message, $error_function = null, $step = null){
		
		$status = new StdClass();
		$status->error = true;
		$status->code = $code;

		$params = (object)[
			"title" => $title,
			"status" => $status,
			"message" => $message,
			"meta" => (object)[
				"error" => $error_function . " on step " . $step
			]
		];

		return $this->res->initialize($params);	
	}


}

/* End of file API_Model.php */
/* Location: ./application/core/API_Model.php */