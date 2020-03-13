<?php  if ( ! defined('BASEPATH')) exit("No direct script access allowed");

class Make extends API_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->library('migration');
        $this->load->database();        
    }

    public function uuid($amount = 1)
    {
        $arr = array();
        for ($i=0; $i < $amount; $i++) { 
            $arr[] = $this->generate_uuid();
        }

        echo json_encode($arr);
    }

    public function migrate($version = '20180909190921'){
        $migration = $this->migration->version($version);
        if (!$migration) {
            echo $this->migration->error_string();
        }else{
            echo "Migration done " . PHP_EOL;
        }
    }

    public function migration($name = null){        

        if (!$name) {
            echo "Please define migration name " . PHP_EOL;
            return;
        }

        if (!preg_match('/^[a-z_]+$/i', $name)) {
            if (strlen($name < 4)) {
                echo "Migration must be at least 4 characters long " . PHP_EOL;
                return;
            }

            echo "Wrong migration name, allowed characters: a-z and _\nExample: first_migration " . PHP_EOL;
            return;
        }

        $fileName = date('YmdHis') . "_" . $name . ".php";

        try {

            $folderPath = APPPATH . 'migrations';

            if (!is_dir($folderPath)) {
                try {
                    mkdir($folderPath);
                } catch (Exception $e) {
                    echo "Error:\n" . $e->getMessage() . PHP_EOL;
                }
            }

            $filePath = APPPATH . 'migrations/' . $fileName;

            if (file_exists($filePath)) {
                echo "File already exists:\n" . $filePath . PHP_EOL;
                return;                            
            }        

            $data['className'] = ucfirst($name);
            $data['fileName'] = $fileName;
            $data['filePath'] = "./application/migrations/".$fileName;

            $template = $this->load->view('cli/migrations/migration_class_template', $data, TRUE);
            //Create File

            try {
                $file = fopen($filePath, "w");
                $content = "<?php\n". $template;
                fwrite($file, $content);
                fclose($file);
            } catch (Exception $e) {
                echo "Error:\n" . $e->getMessage() . PHP_EOL;
            }

            echo "Migration created successfully!\nLocation: " . $filePath . PHP_EOL;
        } catch (Exception $e) {
            echo "Can't create migration file!\nError:" . $e->getMessage() . PHP_EOL;
        }

    }

    public function generate($type = 'api-controller', $name = null)
    {
        if (!$name) {
            echo "Please define file name " . PHP_EOL;
            return;
        }

        if (!preg_match('/^[a-z_]+$/i', $name)) {
            if (strlen($name < 4)) {
                echo "Filename must be at least 4 characters long " . PHP_EOL;
                return;
            }

            echo "Wrong file name, allowed characters: a-z and _\nExample: Auth " . PHP_EOL;
            return;
        }

        $fileName = $name . ".php";

        try {

            $folderPath = APPPATH . 'controllers';

            if (!is_dir($folderPath)) {
                try {
                    mkdir($folderPath);
                } catch (Exception $e) {
                    echo "Error:\n" . $e->getMessage() . PHP_EOL;
                }
            }

            $filePath = APPPATH . 'controllers/' . $fileName;

            if (file_exists($filePath)) {
                echo "File already exists:\n" . $filePath . PHP_EOL;
                return;                            
            }        

            $data['className'] = ucfirst($name);
            $data['fileName'] = $fileName;
            $data['filePath'] = "./application/controllers/".$fileName;

            switch ($type) {         
                case 'api-controller':                
                    $template = $this->load->view('cli/controllers/controller_class_api_template', $data, TRUE);        
                    break;            
                default:
                    $template = $this->load->view('cli/controllers/controller_class_auth_template', $data, TRUE);        
                    break;
            }
            
            //Create File

            try {
                $file = fopen($filePath, "w");
                $content = "<?php\n". $template;
                fwrite($file, $content);
                fclose($file);
            } catch (Exception $e) {
                echo "Error:\n" . $e->getMessage() . PHP_EOL;
            }

            echo "File created successfully!\nLocation: " . $filePath . PHP_EOL;
        } catch (Exception $e) {
            echo "Can't create file!\nError:" . $e->getMessage() . PHP_EOL;
        }
    }
}