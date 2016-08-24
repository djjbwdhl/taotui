<?php
/**
 * @Author: Dujiangjiang
 * @Date:   2015-10-09 11:24:54
 * @Last Modified by:   Dujiangjiang
 * @Last Modified time: 2015-10-09 11:27:39
 */
class ErrorController extends Yaf_Controller_Abstract {
	/**
	 * 异常处理函数
	 *
	 * @param  object  $exception
	 * @return string  html
	 */
	public function errorAction($exception) {
		//$this->getView()->assign("exception", $exception);
		//$this->getView()->assign('exception', $exception->getMessage());
		//header("HTTP/1.1 404 Not Found");  
		header("Status: 404 Not Found");
		echo $exception->getMessage();
		exit;
	}

}

?>