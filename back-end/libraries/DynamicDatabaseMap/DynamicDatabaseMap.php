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

class DynamicDatabaseMap implements IMap
{
    private static $_instance;
    private $databasesPaths = array();
    private $databaseMap;

    private function __construct($databasesPaths)
    {
        $this->databasesPaths = $databasesPaths;
        $this->databaseMap = array();
        $this->loadDatabase();
    }

    public static function getInstance($databasesPaths)
    {
        if (!(self::$_instance instanceof self))
        {
            self::$_instance = new self($databasesPaths);
        }
        return self::$_instance;
    }

    //to_prevent cloned:
    private function __clone()
    {
        trigger_error
        (
            'Invalid Operation: You cannot clone an instance of '
            . get_class($this) ." class.", E_USER_ERROR 
        );
    }

    //to prevent deserialization:
    private function __wakeup()
    {
        trigger_error
        (
            'Invalid Operation: You cannot deserialize an instance of '
            . get_class($this) ." class."
        );
    }

    private function addDatabase($databaseKey, $databaseConnection)
    {
        $this->databaseMap[$databaseKey] = $databaseConnection;
        return $this;
    }

    //http://php.net/manual/es/function.readdir.php
    private function loadDatabase()
    {
        foreach ($this->databasesPaths as $databasesPath) 
        {
            if ($directoryHandle = opendir($databasesPath)) 
            {
                while (false !== ($file = readdir($directoryHandle))) 
                {  
                    if( is_dir($file) )
                    {
                        continue;
                    }
                    else
                    {
                        $filename = str_replace(".php", "", $file);
                        if(class_exists($filename))
                        {
                            if (method_exists($filename, "getInstance")) 
                            {
                                $database = $filename::getInstance();
                                if($database instanceof IDatabase)
                                {
                                    $this->addDatabase($filename, $database);
                                }
                            }
                        }
                    }
                } 
                closedir($directoryHandle);
            }
        }
    }

    public function generate($key = null)
    {
        if ($key==null) 
        {
            return $this->databaseMap;
        }
        else
        {
            return $this->databaseMap[$key];
        }
    }

    public function __destruct()
    {
    }
}