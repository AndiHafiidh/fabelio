<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_Initialize extends CI_Migration {

	public function up()
	{
		$tables = array(			
			'system_key',
			'product',
			'price',
			'galery',
			'v_product'
		);

		foreach ($tables as $table) {
			$this->$table();
		}
	}

	public function down()
	{
		$tables = array(			
			'v_product',
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
				'type' => 'SERIAL',
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
			'product_id' => array(
				'type' => 'INTEGER',		
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
		$this->dbforge->add_key('product_id');			
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
			'product_id INTEGER REFERENCES product(id)',
			'price' => array(
				'type' => 'NUMERIC',                
				'null' => FALSE
			),
			'sale_price' => array(
				'type' => 'NUMERIC',
				'null' => FALSE
			),				
			'created_at TIMESTAMP NOT NULL DEFAULT NOW()',
			'updated_at TIMESTAMP NOT NULL DEFAULT NOW()',
			'deleted_at' => array(
				'type' => 'TIMESTAMP',				
				'null' => TRUE
			)
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
			'product_id INTEGER NOT NULL REFERENCES product(id)',
			'thumbnail' => array(
				'type' => 'TEXT',                
				'null' => FALSE
			),
			'image' => array(
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
			)
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

	private function v_product()
	{
		$name = __FUNCTION__;
        $this->db->query("CREATE VIEW $name AS
            SELECT 
				p.id,
				p.product_id,
				p.name,
				p.description,
				p.detail,
				p.url,
				p.image,
				(
					SELECT 
						p2.price
					FROM price p2
					WHERE p2.product_id = p.id
					ORDER BY created_at DESC
					LIMIT 1
				) AS price,
				(
					SELECT 
						p2.sale_price
					FROM price p2
					WHERE p2.product_id = p.id
					ORDER BY created_at DESC
					LIMIT 1
				) AS sale_price,	
				p.status,
				p.created_at,
				p.updated_at,
				p.deleted_at
			FROM product p");		
	}
}

/* End of file 20200313172027_initialize.php */
/* Location: ./application/migrations/20200313172027_initialize.php */