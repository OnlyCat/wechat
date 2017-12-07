<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

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
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
    	$this->load->helper('url');

	    $webapp_config =  "/var/www/wechat/config/webapp.conf";
	    $service_config = "/var/www/wechat/config/service.conf";
 	    $webapp_config = json_decode(file_get_contents($webapp_config), true);   
 	    $service_config = json_decode(file_get_contents($service_config), true);
	    $template['appid'] = $service_config['appid'];
	    $template['timestamp'] = $webapp_config['timestamp'];
	    $template['noncestr'] = $webapp_config['noncestr'];
	    $template['signature'] = $webapp_config['signature'];

	  /* 新闻模块
	    $pageElement['area'] = $this->avatardata->getPositionCity($_SERVER['REMOTE_ADDR']);
        $pageElement['news'] = $this->avatardata->getRregionNews($pageElement['area']);
	  */

	  /* 账单助手 */
	    $userid = $this->input->get('auth');
        $spendItem = $this->spend_model->getNewSpendRecord($userid);
        $pageElement['spend'] = $spendItem;
        $template['loading'] = $this->load->view('Page/loading', null, true);
	    $template['header'] = $this->load->view('Page/header', null, true);
        $template['default'] = $this->load->view('Page/home', $pageElement, true);
        $template['menu'] = $this->load->view('Page/menu', null, true);
        $template['footer'] = $this->load->view('Page/footer', null, true);

	    $this->load->view('Home/index', $template);
	}
}
