<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {

    public function __construct()
    {
        parent::__construct();        
    }
    
    public function index()
    {
        $this->layout('index');
    }

    public function product($id = null, $title = null)
    {
        if ($id && $title) {
            $this->layout('list', array('id' => $id, 'title' => $title));
        } else {            
            $this->layout('list');
        }        
    }

    private function layout($page, $parameter = array()){
		switch ($page) {
            case 'list':
                $conf = array(
                    'title' => 'List Product Page Fabelio Test Andi',
                    'layout' => 'list',
                    'script' => 'list-script'
                );

                $data = array_merge($conf, $parameter);
                $this->load->view('layout/wrapper',$data);
                break;
            case 'detail':
                $conf = array(
                    'title' => 'Detail Product Page Fabelio Test Andi',
                    'layout'	=> 'detail',
                    'script'    => 'detail-script'
                );

                $data = array_merge($conf, $parameter);
                $this->load->view('layout/wrapper',$data);
                break;            
            default:
                $conf = array(
                    'title' => NULL,              
                    'layout'	=> 'index',
                    'script'    => 'index-script'
                );

                $data = array_merge($conf, $parameter);
                $this->load->view('layout/wrapper',$data);
                break;
		}
	}
}

/* End of file Page.php */
?>