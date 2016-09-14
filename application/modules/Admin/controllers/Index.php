<?php
class IndexController extends Yaf_Controller_Abstract {
	
	private $session=null;
	private $user=null;
	
	public function init(){
		$this->session = Yaf_Session::getInstance();
		$this->user=$this->session->get(ADMIN_USER);
    }
	
	//后台首页
	public function indexAction() {
		if($this->user['au_id']){
			$assign=array(
				'title'=>'管理中心',
				'content'=>'Hello World!'
			);
			$assign['user']=$this->user;
			$this->getView()->assign($assign);
		}else{
			$this->redirect('/admin/login');
			exit;
		}
		
	}

	public function iframeAction(){
		if($this->user['au_id']){
		}else{
			$this->redirect('/admin/login');
			exit;
		}	
	}
	
	//登录页面
	public function loginAction(){
		if($this->user['au_id']){
			$this->redirect('/admin/index');
		}else{
			$assign = array();
			$this->getView()->assign($assign);
		}
	}
	
	//登录提交
	public function loginpostAction(){

		if($this->user['au_id']){
			$this->redirect('/admin/index');
		}else{
			$admin = array(
				'au_id'=>1,
				'au_name'=>'admin',
			);
			$this->session = Yaf_Session::getInstance();
			$this->session->set(ADMIN_USER, $admin);
			$this->redirect('/admin/index');
		}
		Yaf_Dispatcher::getInstance()->disableView();
	}

	//退出登录
	public function logoutAction(){
		$admin = array(
				'au_id'=>0,
				'au_name'=>'',
			);
		$this->session->set(ADMIN_USER, $admin);
		$this->redirect('/admin/login');
		Yaf_Dispatcher::getInstance()->disableView();
	}
	
}
?>