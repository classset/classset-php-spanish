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

class HttpSession implements ISession
{
	private static $_instance;
	private $time;
	private $regeneratedState;

	private function __construct()
	{
		$this->setTime('86400');//24 hs = 86400 segs
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
		if (!(self::$_instance instanceof self))
		{
	 		self::$_instance=new self();
		}
		return self::$_instance;
	}

	public function setTime($time)
	{
		$this->time = $time;
		$this->regeneratedState = true;
	}

	public function getTime()
	{
		return $this->time;
	}

	public function start()
	{
		/*Los parámetros comentados son para configurar el tiempo de sesión tengo que hacerlo
		desde una configuración desde un ini*/
		session_set_cookie_params($this->time);
		session_start();
		/*Cuando esté activado session_set_cookie_params lo siguiente es necesario para regenerar
		la sesión*/
		session_regenerate_id($this->regeneratedState);
	}

	public function set($key, $value)
	{
		$_SESSION["$key"] = $value;
	}

	public function get($key)
	{
		if (isset($_SESSION["$key"]))
		{	
		    return $_SESSION["$key"];
		}
		else
		{
			return NULL;
		}
	}

	public function delete($key)
	{
		if (isset($_SESSION["$key"]))
		{
			$_SESSION["$key"] = NULL;
			return TRUE;
		}
		else
		{
			return NULL;
		}
	}

	public function encode()
	{
		return session_encode();
	}

	public function decode($data)
	{
		return session_decode($data);
	}

	public function destroy()
	{
		$_SESSION = array();
		session_destroy();
	}
}

?>