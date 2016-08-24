<?php

class ShopController extends Yaf_Controller_Abstract {


   	public function indexAction(){
   		$request=$this->getRequest();
   		$assign=array(
			'title'=>'首页',
			'content'=>'Hello World!'
		);
		$this->getView()->assign($assign);
   	}
}
?>
