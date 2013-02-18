<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Actions extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$data['actions'] = array("PETR4","PDGR3","VALE3");
		$data['wallet'] = array(
			"PETR4" => array(
				"quantity" => 24,
				"value" => 20.80
			),
			"PDGR3" => array(
				"quantity" => 200,
				"value" => 4.30
			)
		);

		$this->load->view('actions', $data);
	}

	public function json($actions){
		$url = 'http://www.bmfbovespa.com.br/Pregao-Online/ExecutaAcaoAjax.asp?CodigoPapel='.$actions;

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch);
		curl_close($ch);

		$doc = new SimpleXmlElement($data, LIBXML_NOCDATA);

		$json = json_encode($doc);

		header('Content-Type: application/json');
		echo strtolower($json);
	}
}

/* End of file actions.php */
/* Location: ./application/controllers/actions.php */