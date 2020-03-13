<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_Initialize extends CI_Migration {

	public function up()
	{
		$tables = array(
			'system_config',
			'system_key',
			'product'
		);

		foreach ($tables as $table) {
			$this->$table();
		}
	}

	public function down()
	{
		$tables = array(
			'system_config',
			'system_key',
			'product'
		);

		foreach ($tables as $table) {
			$this->dbforge->drop_table($table);
		}
	}

	private function system_config() //
	{		
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'VARCHAR',								
				'constraint' => '36',
				'unique' => TRUE,
				'null' => FALSE
			),
			'name' => array(
				'type' => 'VARCHAR',
                'constraint' => '255',
				'null' => FALSE				
			),
			'value' => array(
				'type' => 'TEXT',				
				'null' => FALSE
			),			
			'status' => array(
				'type' => 'TINYINT',				
				'null' => FALSE,				
				'default' => 1
			),		
			'created_at TIMESTAMP NOT NULL DEFAULT NOW()',
			'updated_at TIMESTAMP NOT NULL DEFAULT NOW()',
			'deleted_at' => array(
				'type' => 'TIMESTAMP',				
				'null' => TRUE
			)				
		));

		$this->dbforge->add_key('id', TRUE);			
		$this->dbforge->add_key('name');		
		$this->dbforge->add_key('status');		
		$this->dbforge->add_key('is_deleted');		
		$this->dbforge->add_key('created_at');		
		$this->dbforge->add_key('updated_at');		

		$this->dbforge->create_table(__FUNCTION__, TRUE);	
		
		$objects = array(
			(object)[
				"id" => "f559e852-0953-4243-be9d-64b4da1d368f",
				"name" => "system",
				"value" => '{"time_interval":"1 hour"}'
			]				
		);

		$this->db->insert_batch(__FUNCTION__, $objects);
	}
	
	private function system_key() //
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'VARCHAR',								
				'constraint' => '36',
				'unique' => TRUE,
				'null' => FALSE
			),
			'name' => array(
				'type' => 'VARCHAR',
                'constraint' => '150',
				'null' => FALSE
			),
			'api_key' => array(
				'type' => 'VARCHAR',	
                'constraint' => '30',
				'unique' => TRUE,
				'null' => FALSE
			),
			'status' => array(
				'type' => 'TINYINT',				
				'null' => FALSE,				
				'default' => 1
			),				
			'created_at TIMESTAMP NOT NULL DEFAULT NOW()',
			'updated_at TIMESTAMP NOT NULL DEFAULT NOW()',
			'deleted_at' => array(
				'type' => 'TIMESTAMP',				
				'null' => TRUE
			)				
		));

		$this->dbforge->add_key('id', TRUE);			
		$this->dbforge->add_key('status');		
		$this->dbforge->add_key('is_deleted');		
		$this->dbforge->add_key('created_at');		
		$this->dbforge->add_key('updated_at');			
		$this->dbforge->create_table(__FUNCTION__, TRUE);	

		$objects = array(
			(object)[
				"id" => "cc4f3a79-a76d-4aa6-8b96-cb37975d8c93",
				"name" => "application",					
				"api_key" => "6Vlo7xk4Ih0tOD6zD4IgUjO07MxXJ8"
			]
		);

		$this->db->insert_batch(__FUNCTION__, $objects);
	}

	private function product() //
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'VARCHAR',								
				'constraint' => '36',
				'unique' => TRUE,
				'null' => FALSE
			),
			'url' => array(
				'type' => 'TEXT',
				'null' => FALSE
			),
			'name' => array(
				'type' => 'VARCHAR',
                'constraint' => '255',
				'null' => FALSE
			),
			'description' => array(
				'type' => 'TEXT',
				'null' => FALSE
			),
			'image' => array(
				'type' => 'TEXT',
				'null' => FALSE
			),
			'price' => array(
				'type' => 'TEXT',
				'null' => FALSE
			),
			'status' => array(
				'type' => 'TINYINT',				
				'null' => FALSE,				
				'default' => 1
			),				
			'created_at TIMESTAMP NOT NULL DEFAULT NOW()',
			'updated_at TIMESTAMP NOT NULL DEFAULT NOW()',
			'deleted_at' => array(
				'type' => 'TIMESTAMP',				
				'null' => TRUE
			)				
		));

		$this->dbforge->add_key('id', TRUE);			
		$this->dbforge->add_key('name');		
		$this->dbforge->add_key('status');		
		$this->dbforge->add_key('is_deleted');		
		$this->dbforge->add_key('created_at');		
		$this->dbforge->add_key('updated_at');			
		$this->dbforge->create_table(__FUNCTION__, TRUE);		
	}
}

/* End of file 20200313172027_initialize.php */
/* Location: ./application/migrations/20200313172027_initialize.php */