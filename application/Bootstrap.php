<?php
/**
 * @Author: Dujiangjiang
 * @Date:   2015-12-07 15:40:40
 * @Last Modified by:   dujiangjiang
 * @Last Modified time: 2016-06-27 10:45:25
 */

class Bootstrap extends Yaf_Bootstrap_Abstract {
	/**
	 * 初始化全局变量及对象
	 */
	public function _initVariables() {
		$config = Yaf_Application::app()->getConfig();
		Yaf_Registry::set("config",$config);

		//是否开启deBug调试模式
		if($config->get("debug")){
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
		}

		//初始化数据库连接
		$db = $config->get("db");
		Yaf_Registry::set("db", new MySql(
			$db->host,
			$db->username,
			$db->password,
			$db->default,
			$db->charset
		));
	}
	
	/**
	 * 初始化路由规则 
	 */
	public function _initRoute(Yaf_Dispatcher $dispatcher){
		$routeconf = new Yaf_Config_Ini(APP_PATH.'/conf/route.ini','product');
		if ($routeconf->route) {
            $routes = $dispatcher::getInstance()->getRouter();
            $routes->addConfig($routeconf->route);
        }
	}
}

?>