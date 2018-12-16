<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

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
	 * 	@see https://codeigniter.com/user_guide/general/urls.html
	 */
	
	public function index()
	{	
		$model =  $this->load->model('Summarize');
		$model = new Summarize();
		$testing=[];
	$dir_corpus = "/opt/lampp/htdocs/Project/assets/corpus";
    $files    = scandir($dir_corpus);
    $files    = array_slice($files, 2);
    
    // hasil
    if(isset($_POST['filename'])) {
	$filename = $_POST['filename'];
	  $data['filename']  = $filename;
	
      $testing    = $model->ringkasan($filename);
      $title     = substr($filename, 0, -4);
    }

	
		$this->load->view('test',['dir'=>$files,'model'=>$model,'datanya'=>$testing]);
				
	}
	
	
	
}
