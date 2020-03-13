defined('BASEPATH') OR exit('No direct script access allowed');
class <?php echo $className ?> extends API_controller {
	public $table = "<?php echo strtolower($className); ?>";
	private $auth;

	public function __construct()
	{
		parent::__construct();
		$this->check_session();
	}
	
}

/* End of file <?php echo $fileName ?> */
/* Location: <?php echo $filePath ?> */