<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_Initialize extends CI_Migration {

	public function up()
	{
		$tables = array(			
			'system_key',
			'product',
			'price',
			'galery'
		);

		foreach ($tables as $table) {
			$this->$table();
		}
	}

	public function down()
	{
		$tables = array(			
			'system_key',
			'galery',
			'price',
			'product'
		);

		foreach ($tables as $table) {
			$this->dbforge->drop_table($table);
		}
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
		$this->dbforge->add_key('created_at');		
		$this->dbforge->add_key('updated_at');			
		$this->dbforge->add_key('deleted_at');		
		$this->dbforge->create_table(__FUNCTION__, TRUE);	

		$objects = array(
			(object)[
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
				'type' => 'SERIAL',		
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
			'detail' => array(
				'type' => 'TEXT',
				'null' => FALSE
			),
			'url' => array(
				'type' => 'TEXT',
				'null' => FALSE
			),
			'image' => array(
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
		$this->dbforge->add_key('created_at');		
		$this->dbforge->add_key('updated_at');			
		$this->dbforge->add_key('deleted_at');		
		$this->dbforge->create_table(__FUNCTION__, TRUE);		
	}

	private function price() //
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'SERIAL',		
				'null' => FALSE
			),
			'product_id' => array(
				'type' => 'SERIAL',
				'null' => FALSE
			),
			'price' => array(
				'type' => 'DOUBLE',                
				'null' => FALSE
			),
			'sale_price' => array(
				'type' => 'DOUBLE',
				'null' => FALSE
			),				
			'created_at TIMESTAMP NOT NULL DEFAULT NOW()',
			'updated_at TIMESTAMP NOT NULL DEFAULT NOW()',
			'deleted_at' => array(
				'type' => 'TIMESTAMP',				
				'null' => TRUE
			),
			'CONSTRAINT `fk_price_product` FOREIGN KEY (product_id) REFERENCES product(id)'			

		));

		$this->dbforge->add_key('id', TRUE);			
		$this->dbforge->add_key('price');		
		$this->dbforge->add_key('sale_price');		
		$this->dbforge->add_key('status');		
		$this->dbforge->add_key('created_at');		
		$this->dbforge->add_key('updated_at');			
		$this->dbforge->add_key('deleted_at');		
		$this->dbforge->create_table(__FUNCTION__, TRUE);		
	}

	private function galery() //
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'SERIAL',		
				'null' => FALSE
			),
			'product_id' => array(
				'type' => 'SERIAL',
				'null' => FALSE
			),
			'thumbnail' => array(
				'type' => 'TEXT',                
				'null' => FALSE
			),
			'img' => array(
				'type' => 'TEXT',
				'null' => FALSE
			),				
			'full' => array(
				'type' => 'TEXT',
				'null' => FALSE
			),	
			'caption' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => FALSE
			),
			'order' => array(
				'type' => 'INT',
				'null' => FALSE
			),							
			'created_at TIMESTAMP NOT NULL DEFAULT NOW()',
			'updated_at TIMESTAMP NOT NULL DEFAULT NOW()',
			'deleted_at' => array(
				'type' => 'TIMESTAMP',				
				'null' => TRUE
			),
			'CONSTRAINT `fk_galery_product` FOREIGN KEY (product_id) REFERENCES product(id)'			

		));

		$this->dbforge->add_key('id', TRUE);			
		$this->dbforge->add_key('caption');		
		$this->dbforge->add_key('order');		
		$this->dbforge->add_key('status');		
		$this->dbforge->add_key('created_at');		
		$this->dbforge->add_key('updated_at');			
		$this->dbforge->add_key('deleted_at');		
		$this->dbforge->create_table(__FUNCTION__, TRUE);		
	}
}

/* End of file 20200313172027_initialize.php */
/* Location: ./application/migrations/20200313172027_initialize.php */