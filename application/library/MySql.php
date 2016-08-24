<?php
/**
 * @Author: Dujiangjiang
 * @Date:   2015-12-07 09:47:54
 * @Last Modified by:   Dujiangjiang
 * @Last Modified time: 2016-01-22 11:53:29
 */
class MySql {
	private $db_host;
	private $db_user;
	private $db_pwd;
	private $database;
	private $charset;

	public $dbh; 
	private $quotes; 

	private $page_now; 
	private $page_rows; 

	private $log_error;

	/**
	 * 为兼容以前的代码使用，所有方法都没变
	 */
	function __construct($db_host, $db_user, $db_pwd, $db_name, $db_charset = 'utf8') {
		$this->db_host = $db_host;
		$this->db_user = $db_user;
		$this->db_pwd = $db_pwd;
		$this->database = $db_name;
		$this->charset = $db_charset;
		$this->dbh = false;
		$this->page_now = 1;
		$this->page_rows = 10;
		$this->quotes = get_magic_quotes_gpc();
		$this->log_error = 'sql_error.txt';
		$this->connect();
	}

	/**
	 * destroy class
	 */
	function __destruct() {
		if (is_resource($this->dbh)) {
			$this->dbh=null;
		}
	}

	/**
	 * destroy class
	 */
	public function destory() {
		if (is_resource($this->dbh)) {
			mysql_close($this->dbh);
		}
	}

	/**
	 * set log file for error
	 */
	public function set_log_file($fullpath) {
		$this->log_error = $fullpath;
	}

	/**
	 * set page number
	 */
	public function set_page_num($page) {
		$this->page_now = $page;
	}

	/**
	 * set rows per page
	 */
	public function set_page_rows($num) {
		$this->page_rows = $num;
	}

	public function set_database($dbname){
		if ($this->database != $dbname) {
			$this->database = $name;
			if (is_resource($this->dbh)) {
				$this->dbh->query("use $dbname") || $this->error();
			}
		}
	}

	//创建数据库连接
	private function connect() {
		if (!is_resource($this->dbh)) {
			($this->dbh = new PDO("mysql:host=$this->db_host;dbname=$this->database","$this->db_user","$this->db_pwd")) || $this->error();
			$this->dbh->exec("SET NAMES '{$this->charset}'");
		}
	}


	//过滤字符
	public function escape($val) {
		return stripslashes(trim($val));
	}

	/**
	 * filter value
	 */
	public function filter_value($values) {

		if (is_array($values)) {
			$arr = array();
			foreach ($values as $k => $v) {
				if ('=' == substr($v, 0, 1) && preg_match('/^[\w\+\-\*\/\.()\s_,]+$/', substr($v, 1))) {
					$arr[] = $k . $v;
				} else {
					$arr[] = $k . "='" . $this->escape($v) . "'";
				}
			}
			return implode(',', $arr);
		} else {
			return false;
		}
	}

	//查询全部数据，返回二维数组。
	public function select_all($sql) {

		if (2 <= func_num_args()) {
			$sql = $this->fetch_args(func_get_args());
		}

		($res = $this->dbh->query($sql)) || $this->error($sql);
        return $res->fetchAll(PDO::FETCH_ASSOC);
	}

	//分页查询，返回查询页数据；与set_page_num、set_page_rows配合使用
	public function select_page($sql) {

		if (2 <= func_num_args()) {
			$sql = $this->fetch_args(func_get_args());
		}

		$sql .= ' LIMIT ' . (($this->page_now - 1) * $this->page_rows) . ', ' . $this->page_rows;

		($res = $this->dbh->query($sql)) || $this->error($sql);
        return $res->fetchAll(PDO::FETCH_ASSOC);
	}

	//查询单条数据，返回一维数组；
	public function select_row($sql) {
		if (2 <= func_num_args()) {
			$sql = $this->fetch_args(func_get_args());
		}
		($res = $this->dbh->query($sql)) || $this->error($sql);
        return $res->fetch(PDO::FETCH_ASSOC);
	}

	//查询单个字段值，返回单个值；
	public function select_one($sql) {
		if (2 <= func_num_args()) {
			$sql = $this->fetch_args(func_get_args());
		}

		($res = $this->dbh->query($sql)->fetchColumn()) || $this->error($sql);
		return $res;
	}

	//查询两列返回一个哈希数组
	public function select_hash($sql) {
		if (2 <= func_num_args()) {
			$sql = $this->fetch_args(func_get_args());
		}

		($res = $this->dbh->query($sql)) || $this->error($sql);
		$arr = array();
		while ($row = $res->fetch(PDO::FETCH_NUM)) {
			$arr[$row[0]] = $row[1];
		}
		unset($res);
		return $arr;
	}

	//执行一条SQL语句，不返回结果；
	public function exe($sql) {

		if (2 <= func_num_args()) {
			$sql = $this->fetch_args(func_get_args());
		}

		$del = preg_match('/^DELETE/i', $sql);
		if($del){
			$this->error($sql."\n\r 不允许执行DELETE语句。");
			return false;
		}

		($this->dbh->exec($sql)) || $this->error($sql);
	}

	//运行一条copy功能的sql
	public function copy($sql) {
		if (2 <= func_num_args()) {
			$sql = $this->fetch_args(func_get_args());
		}
		($this->dbh->query($sql)) || $this->error($sql);
		$id = $this->dbh->lastInsertId();
		return $id ? $id : 0;
	}

	//插入数据
	function insert($table, $values) {

		$val = $this->filter_value($values);
		$sql = "INSERT LOW_PRIORITY INTO $table SET $val";

		if($this->dbh->query($sql)){
			$id = $this->dbh->lastInsertId();
			return $id;
		}else{
			return $this->error($sql);
		}
	}

	//更新数据
	function update($table, $values, $where) {

		$val = $this->filter_value($values);
		$sql = "UPDATE LOW_PRIORITY $table SET $val WHERE $where";

		if($this->dbh->query($sql)){
			return true;
		}else{
			$this->error($sql);
			return false;
		}

	}

	private function fetch_args($args) {
		$num = sizeof($args);
		$arr = array();
		for ($i = 1; $i < $num; $i++) {
			$arr['#' . $i] = $this->escape($args[$i]);
		}
		return strtr($args[0], $arr);
	}

	private function error($params = null) {

		if($this->dbh->errorCode()!='00000'){
			$ext = '';
			if (is_null($params) or empty($params)) {
				$ext = 'n/a';
			} elseif (is_array($params)) {
				foreach ($params as $k => $v) {
					$ext .= $k . ' = ' . $v . '<br />';
				}
			} else {
				$ext = $params;
			}

			$msg = "================================================================================\r\n";
			$msg .= 'time: ' . date('Y-m-d H:i:s') . "\r\n";
			$msg .= 'from: ' . $_SERVER['REMOTE_ADDR'] . "\r\n";
			$msg .= 'page: ' . $_SERVER['REQUEST_URI'] . "\r\n";
			$msg .= 'error: ' . $this->dbh->errorCode().'：'.json_encode($this->dbh->errorInfo()). "\r\n";
			$msg .= 'param: ' . $ext . "\r\n\r\n";

			error_log($msg, 3, $this->log_error);
			return false;
		}
	}
}

?>