<?php
class IndexController extends Yaf_Controller_Abstract {
	
	public function indexAction() {
		
		$assign=array(
			'title'=>'首页',
			'content'=>'Hello World!'
		);
		$this->getView()->assign($assign);
	}
}
?>