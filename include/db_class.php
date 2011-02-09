<?php
/**
   Author : Guohai
   Revised History: 2006/10/31
*/
class MySqlClass{
	var $DBHost = ''; /* 数据库主机名称 */
	var $DBUser = ''; /* 数据库用户名称 */
	var $DBPswd = ''; /* 数据库密码	 */
	var $DBName = ''; /* 数据库名称	 */

	var $LinkId = NULL;
	var $Result = NULL;
	var $row = 0;		//上一次操作影响的行数
	function MySqlClass($DBHost='',$DBUser='',$DBPswd='',$DBName='')
	{
		//构造函数，初始化部分类成员，并调用数据库连接
		$this->DBHost=$DBHost;
		$this->DBUser=$DBUser;
		$this->DBPswd=$DBPswd;
		$this->DBName=$DBName;
		$this->Connect();
		$this->selDB();
	}

	function Connect()
	{
		//尝试连接MySql数据库
		$connect = @mysql_connect($this->DBHost,$this->DBUser,$this->DBPswd);
		if(!is_resource($connect)){
			$this->halt("连接数据库失败，请检查include/config.php文件。");
			return false;
		}
		@mysql_query("set names 'utf8'");;
		$this->LinkId = $connect;
		return true;
	}
	function selDB()
	{
		mysql_select_db($this->DBName);
	}
	function close()
	{
		//关闭数据库的连接
		mysql_close($this->LinkId);
	}
	function halt($message)
	{
		echo $message;
		return $message;
	}

	function ExeSql($sql)
	{
		//执行SQL语句$sql为接受的查询语句
		$this->Result=mysql_query($sql);
		$this->row=mysql_affected_rows();
		return $this->Result;
	}

	function getRowResult($result)
	{
		return mysql_fetch_assoc($result);
	}

	function getOneRowString($result)
	{
		$temp=mysql_fetch_row($result);
		return $temp[0];
	}
}
?>
