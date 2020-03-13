<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_Initialize extends CI_Migration {

	public function up()
	{
		$tables = array(
			
		);

		foreach ($tables as $table) {
			$this->$table();
		}
	}

	public function down()
	{
		$tables = array(
			
		);

		foreach ($tables as $table) {
			$this->dbforge->drop_table($table);
		}
	}

}

/* End of file 20200313172027_initialize.php */
/* Location: ./application/migrations/20200313172027_initialize.php */