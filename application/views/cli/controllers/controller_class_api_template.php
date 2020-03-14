defined('BASEPATH') OR exit('No direct script access allowed');
class <?php echo $className ?> extends API_controller {
	public $table = "<?php echo strtolower($className); ?>";	

	public function __construct()
	{
		parent::__construct();		
		$this->allowed = ['create', 'read', 'update', 'delete'];
	}
	
}

/* End of file <?php echo $fileName ?> */
/* Location: <?php echo $filePath ?> */