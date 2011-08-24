<?php
$db = new MySQL("localhost","user1","050809","sinedb");
class MySQL
{	function MySQL($dbhost,$dbuser,$dbpass,$dbname)
	{	$this->dbhost=$dbhost;
		$this->dbuser=$dbuser;
		$this->dbpass=$dbpass;
		$this->dbname=$dbname;
		@mysql_connect($this->dbhost,$this->dbuser,$this->dbpass) OR exit('Can not connect to MySQL server');
		@mysql_select_db($this->dbname) OR exit('Can not select to the database');
	}

	function query($sql,$report=true) // whether report errors or not!
	{	$Query = @mysql_query($sql);
		if ($Query)
		{	return $Query;	}
		elseif ($report)
		{	$this->alert($sql);	}
	}

	function fetch_row($query) 
	{	return @mysql_fetch_row($query);	}

	function assoc($query)
	{	return @mysql_fetch_assoc($query);	}

	function fields($query,$row_result_type=0)
	{	if ($row_result_type)
		{	return $this->fetch_row($query);	}
		else
		{	return $this->assoc($query);	}
	}

	function result($query,$row=0)
	{	return @mysql_result($query, $row);	}

	function num_fields($query) 
	{	return @mysql_num_fields($query);	}

	function num_rows($query)
	{	return @mysql_num_rows($query);	}

	function insert_id()
	{    return mysql_insert_id();	}

	function affected()
	{	return @mysql_affected_rows();	}

    function free($query)
	{	mysql_free_result($query);	}

	function errno()
	{	return mysql_errno();	}

	function error()
	{	return mysql_error();	}

	function alert($SQL) 
	{	echo '<br>Query : <i>'.htmlspecialchars($SQL).'</i><br>';
		echo $this->error();
	}
}
?>