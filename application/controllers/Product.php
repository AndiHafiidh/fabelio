<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Carbon\Carbon;


class Product extends API_controller {
	public $table = "v_product";	
	private $API_URL = "https://fabelio.com/insider/data/product/id/";

	public function __construct()
	{
		parent::__construct();		
		$this->allowed = ['create', 'read', 'update'];

		$step = 1; // api key check
		$key = $this->key_check($this->header['X-Api-Key']);
		if (empty($key) || $key->status->error) {				
			$this->response = $this->model->error(401, "Unauthorized application", "Application not valid", __CLASS__ . ' '. __FUNCTION__, $step);			
			$this->get_response(true, false);
			exit;
		}
	}

	public function create()
	{
		$step = 2; // check allowed access        
        if(in_array(__FUNCTION__, $this->allowed)){		
			$step = 3; // get input data	
			$post = $this->input->raw_input_stream;			

			$require_params = [                    
				"url"			
			];      

			$json = new StdClass();
			if($post){
				$json = json_decode($post);          
			}

			$step = 4; // required parameter check
			foreach ($require_params as $param) {
				if (!property_exists($json, $param) || empty($json->$param)) {
					$this->response = $this->model->error(400, "Require params not found", "Parameter " . $param . " harus diisi", __CLASS__ . ' '. __FUNCTION__, $step);
					$this->get_response(true);
					exit;
				}	
			}  

			$step = 5; // create client for scrapping and http_request
			$goutte = new Client();
			$guzzle = new GuzzleClient();
			$guzzleClient = new GuzzleClient(array(
				'timeout' => 60,
			));

			$goutte->setClient($guzzleClient);
			
			$step = 6; // get product data (part 1)
			$crawler = $goutte->request('GET', $json->url);			
			$fullbody = $guzzle->request('GET', $json->url);

			$id = $crawler->filter('input#productId')->eq(0)->attr('value');

			$step = 7; // registered product check
			$check_product = clone($this->model->select($this->table, '*', "product_id = $id AND status = 1 AND deleted_at IS NULL"));
			if($check_product AND !$check_product->status->error){

				$step = 8; // create response
				$product = $check_product->data[0];
				$params = (object) [
					"title" => "Save data failed",
					"status" => 200,
					"message" => "Product has been registered before!",
					"data" => array(
						APP_URL.'/product/'.$product->id.'/'.$this->create_url($product->name).'.html'
					)							
				];
				$this->response = $this->res->initialize($params);
				$this->get_response(true);
				exit;
			}

			$step = 8; // get product data (part 2)
			$description = $crawler->filter('div.product-info__description')->eq(0)->text();			

			$additional_data = $crawler->filter('div#additional-data')->eq(0)->html();	
			
			$html =  preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', (string) $fullbody->getBody()->getContents());			
			preg_match_all('/"data": \[(.*?)\]/m', $html, $galery, PREG_SET_ORDER, 0);
			$galery = json_decode('{'.$galery[0][0].'}');				
			
			$data = $guzzle->request('GET', $this->API_URL.$id);
			$product = json_decode($data->getBody()->getContents());

			$step = 9; // create new product
			$new_product = (object)[
				"product_id" => $id,					
				"name" => $product->product->name,
				"description" => $description,
				"detail" => $additional_data,	
				"url" => $product->product->url,
				"image" => $product->product->product_image_url,				
			];
			$saved_product = clone($this->model->insert('product', $new_product));				

			if($saved_product AND !$saved_product->status->error){
				$id = $saved_product->data[0];
				$step = 10; // create new price
				$new_price = (object)[
					'product_id' => $id,
					'price' => $product->product->unit_price,
					'sale_price' => $product->product->unit_sale_price
				];
				$saved_price = clone($this->model->insert("price", $new_price));
				if($saved_price AND !$saved_price->status->error){

					$step = 11; // create new galery
					$new_galery = array();
					foreach ($galery->data as $data) {
						$new = (object)[
							'product_id' => $id,
							'thumbnail' => $data->thumb,
							'image' => $data->img,				
							'full' => $data->full,	
							'caption' => $data->caption,
							'order' => $data->position
						];		
						$new_galery[] = $new;	
					}
					$saved_galery = clone($this->model->insert('galery', $new_galery));
					if($saved_galery AND !$saved_galery->status->error){
						
						$step = 12; // create success response 
						$params = (object) [
							"title" => "Save data success",
							"status" => 200,
							"message" => "Data successfully added",
							"data" => array(
								APP_URL.'/product/'.$id.'/'.$this->create_url($product->product->name).'.html'
							)							
						];
						$this->response = $this->res->initialize($params);
					}else{
						$this->response = $this->model->error(400, "Terjadi kesalahan" , "Maaf terjadi kesalahan, silahkan coba beberapa saat lagi", __CLASS__ . '-' . __FUNCTION__, $step);
					}
				}else{
					$this->response = $this->model->error(400, "Terjadi kesalahan" , "Maaf terjadi kesalahan, silahkan coba beberapa saat lagi", __CLASS__ . '-' . __FUNCTION__, $step);
				}						
			}else{
				$this->response = $this->model->error(400, "Terjadi kesalahan" , "Maaf terjadi kesalahan, silahkan coba beberapa saat lagi", __CLASS__ . '-' . __FUNCTION__, $step);		
			}
												
		}else{
            $this->response = $this->model->error(401, "Authentication failed" , "Maaf terjadi kesalahan, silahkan coba beberapa saat lagi", __CLASS__ . '-' . __FUNCTION__, $step);
		}
		
		$this->get_response(true, false);       
	}

	public function update($id = null)
	{
		$step = 2; // check allowed access        
        if(in_array(__FUNCTION__, $this->allowed)){		
			$step = 3; // interval time check	
			
			$data = $this->model->query("SELECT * FROM v_product WHERE status = 1 AND deleted_at IS NULL AND CURRENT_TIMESTAMP - updated_at > INTERVAL '1 HOURS' ORDER BY updated_at ASC LIMIT 1");
			if($data){

				$goutte = new Client();
				$guzzle = new GuzzleClient();
				$guzzleClient = new GuzzleClient(array(
					'timeout' => 60,
				));

				$goutte->setClient($guzzleClient);			

				foreach ($data as $found) {					
					$step = 6; // get product data (part 1)
					$crawler = $goutte->request('GET', $found->url);			
					$fullbody = $guzzle->request('GET', $found->url);

					$id = $crawler->filter('input#productId')->eq(0)->attr('value');				
					$description = $crawler->filter('div.product-info__description')->eq(0)->text();			

					$additional_data = $crawler->filter('div#additional-data')->eq(0)->html();	
					
					$html =  preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', (string) $fullbody->getBody()->getContents());			
					preg_match_all('/"data": \[(.*?)\]/m', $html, $galery, PREG_SET_ORDER, 0);
					$galery = json_decode('{'.$galery[0][0].'}');				
					
					$data = $guzzle->request('GET', $this->API_URL.$id);
					$product = json_decode($data->getBody()->getContents());
					$now = Carbon::now(); 

					$update_product = (object)[
						"product_id" => $id,					
						"name" => $product->product->name,
						"description" => $description,
						"detail" => $additional_data,	
						"url" => $product->product->url,
						"image" => $product->product->product_image_url,
						"updated_at" => $now->format('Y-m-d H:i:s')				
					];

					$saved_product = clone($this->model->update('product', $update_product, "id = $found->id"));
					
					$new_price = (object)[
						'product_id' => $found->id,
						'price' => $product->product->unit_price,
						'sale_price' => $product->product->unit_sale_price
					];					
					$saved_price = clone($this->model->insert("price", $new_price));					

					$deleted_galery = clone($this->model->delete("galery", "product_id = $found->id"));
					
					$new_galery = array();
					foreach ($galery->data as $data) {
						$new = (object)[
							'product_id' => $found->id,
							'thumbnail' => $data->thumb,
							'image' => $data->img,				
							'full' => $data->full,	
							'caption' => $data->caption,
							'order' => $data->position
						];		
						$new_galery[] = $new;	
					}
					$saved_galery = clone($this->model->insert('galery', $new_galery));
				}
				
				$params = (object) [
					"title" => "Update data success",
					"status" => 200,
					"message" => "Data successfully updated"					
				];

				$this->response = $this->res->initialize($params);
			}else{
				$this->response = $this->model->error(404, "Data Not Found" , "No data need to update", __CLASS__ . '-' . __FUNCTION__, $step);
			}
		}else{
            $this->response = $this->model->error(401, "Authentication failed" , "Maaf terjadi kesalahan, silahkan coba beberapa saat lagi", __CLASS__ . '-' . __FUNCTION__, $step);
		}
		
		$this->get_response(true, false);       
	}
}

/* End of file Product.php */
/* Location: ./application/controllers/Product.php */