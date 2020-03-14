defined('BASEPATH') OR exit('No direct script access allowed');
class <?php echo "Migration_".$className ?> extends CI_Migration {

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

/* End of file <?php echo $fileName ?> */
/* Location: <?php echo $filePath ?> */