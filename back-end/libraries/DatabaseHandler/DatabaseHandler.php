<?php
/**
 *  Copyright 2013 Gabriel Nicolás González Ferreira <gabrielinuz@gmail.com> 
 *
 *  Permission is hereby granted, free of charge, to any person obtaining
 *  a copy of this software and associated documentation files (the
 *  "Software"), to deal in the Software without restriction, including
 *  without limitation the rights to use, copy, modify, merge, publish,
 *  distribute, sublicense, and/or sell copies of the Software, and to
 *  permit persons to whom the Software is furnished to do so, subject to
 *  the following conditions:
 *
 *  The above copyright notice and this permission notice shall be
 *  included in all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 *  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 *  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 *  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 *  LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 *  OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 *  WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 **/

class DatabaseHandler implements IDatabaseHandler
{
	/* attributes: */
	private static $instance;
	private $host;
	private $port;
	private $name;
	private $user;
	private $password;
	private $filePath;
	private $databaseObject;
	private $databaseLink;

	/* methods: */

	private function __construct()
	{ 
	}

	//to_prevent cloned:
	private function __clone()
	{
		trigger_error
		(
			'Invalid Operation: You can not clone an instance of '
			. get_class($this) ." class.", E_USER_ERROR 
			);
	}

	//to prevent deserialization:
	private function __wakeup()
	{
		trigger_error
		(
			'Invalid Operation: You can not deserialize an instance of '
			. get_class($this) ." class."
			);
	}

	public static function getInstance()
	{
		if (!(self::$instance instanceof self))
		{
			self::$instance=new self();
		}
		return self::$instance;
	}

	/*FOR PROPERTIES*/
	public function __get($name)
	{
		if (method_exists($this, ($method = 'get'.ucfirst($name))))
		{
			return $this->$method();
		}
		else return;
	}

	public function __set($name, $value)
	{
		if (method_exists($this, ($method = 'set'.ucfirst($name))))
		{
			$this->$method($value);
		}
	}
	/*FOR PROPERTIES*/
	

	/*GETTERS AND SETTERS*/
	private function getHost()
	{
		return $this->host;
	}

	private function setHost($host)
	{
		$this->host = $host;
		return $this;
	}

	private function getPort()
	{
		return $this->port;
	}

	private function setPort($port)
	{
		$this->port = $port;
		return $this;
	}

	private function getName()
	{
		return $this->name;
	}

	private function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	private function getUser()
	{
		return $this->user;
	}

	private function setUser($user)
	{
		$this->user = $user;
		return $this;
	}

	private function getPassword()
	{
		return $this->password;
	}

	private function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}

	private function getFilePath()
	{
		return $this->filePath;
	}

	private function setFilePath($filePath)
	{
		$this->filePath = $filePath;
		return $this;
	}
	/*GETTERS AND SETTERS*/

	//LOAD DATABASE SYSTEM

  	//INTERFACE PUBLIC METHOD
	public function openDBMS($systemName)
	{
		$ucSystemName = ucfirst($systemName);

		require_once "{$ucSystemName}Database.php";

		$databaseClass = "{$ucSystemName}Database";
		$this -> databaseObject = new $databaseClass;
		$this -> databaseLink = $this -> databaseObject -> openDatabase();
		return $this;
	}
    //INTERFACE PUBLIC METHOD

	private function execQuery($query)
	{ 
		try 
		{
			$this -> databaseLink -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$databaseReply = $this -> databaseLink -> prepare($query);
			$databaseReply -> execute();
		}

		catch(PDOException $exception) 
		{
	    	//manage exception and log
			echo $exception -> getMessage();  
		}

		return $databaseReply;
	}

	private function execQueriesInTransaction($queries)
	{ 
		try 
		{
			$this -> databaseLink -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this -> databaseLink -> beginTransaction();
			foreach ($queries as $query) 
			{
				$databaseReply = $this -> databaseLink -> prepare($query);
				$databaseReply -> execute();
			}
			$this -> databaseLink -> commit();
		}

		catch(PDOException $exception) 
		{
	    	//manage exception and log
			echo $exception -> getMessage();  
		}

		return $databaseReply;
	}		

	private function getResultingRowOnArray($databaseReply)
	{
		$row = $databaseReply -> fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	private function getResultingRowsOnArrayOfArrays($databaseReply)
	{
		$row = $databaseReply -> fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}

	private function closeDatabase($databaseLink, $databaseReply)
	{
		$databaseLink = null;
		$databaseReply = null;
	}

//INTERFACE PUBLIC METHODS	
	public function SQLQuery($query)
	{
		$databaseLink = $this -> databaseLink;
		$reply = $this-> execQuery($query);
		$this -> closeDatabase($databaseLink, $reply);
	}

	public function SQLFetchArray($query)
	{
		$databaseLink = $this -> databaseLink;
		$reply = $this -> execQuery($query);
		$resultRow = $this -> getResultingRowOnArray($reply);
		$this -> closeDatabase($databaseLink, $reply);
		return $resultRow;
	}

	public function SQLFetchAllArray($query)
	{
		$databaseLink = $this -> databaseLink;
		$reply = $this -> execQuery($query);
		$resultRow = $this -> getResultingRowsOnArrayOfArrays($reply);
		$this -> closeDatabase($databaseLink, $reply);
		return $resultRow;
	}

	/*FOR TRANSACTIONS*/
	public function SQLTransaction($queriesOnArray)
	{
		$databaseLink = $this -> databaseLink;
		$reply = $this -> execQueriesInTransaction($queriesOnArray);
		$this -> closeDatabase($databaseLink, $reply);
	}

	public function SQLTransactionArray($queriesOnArray)
	{
		$databaseLink = $this -> databaseLink;
		$reply = $this -> execQueriesInTransaction($queriesOnArray);
		$resultRow = $this -> getResultingRowOnArray($reply);
		$this -> closeDatabase($databaseLink, $reply);
		return $resultRow;
	}

	public function SQLTransactionAllArray($queriesOnArray)
	{
		$databaseLink = $this -> databaseLink;
		$reply = $this -> execQueriesInTransaction($queriesOnArray);
		$resultRow = $this -> getResultingRowsOnArrayOfArrays($reply);
		$this -> closeDatabase($databaseLink, $reply);
		return $resultRow;    
	}
	/*FOR TRANSACTIONS*/
//INTERFACE PUBLIC METHODS

	public function __destruct()
	{
		unset($this -> databaseLink);
	}

}	

?>