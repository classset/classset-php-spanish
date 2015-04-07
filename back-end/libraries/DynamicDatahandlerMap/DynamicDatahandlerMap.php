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

class DynamicDatahandlerMap implements IMap
{
    private static $_instance;
    private $datahandlersPaths = array();
    private $datahandlerMap;

    private function __construct($datahandlersPaths)
    {
        $this->datahandlersPaths = $datahandlersPaths;
        $this->datahandlerMap = array();
        $this->loadDatahandler();
    }

    public static function getInstance($datahandlersPaths)
    {
        if (!(self::$_instance instanceof self))
        {
            self::$_instance = new self($datahandlersPaths);
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

    private function addDatahandler($datahandlerKey, $datahandler)
    {
        $this->datahandlerMap[$datahandlerKey] = $datahandler;
        return $this;
    }

    //http://php.net/manual/es/function.readdir.php
    private function loadDatahandler()
    {
        foreach ($this->datahandlersPaths as $datahandlersPath) 
        {
            if ($directoryHandle = opendir($datahandlersPath)) 
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
                            $datahandler = new $filename;
                            if(($datahandler instanceof IDataget) or ($datahandler instanceof IDataset))
                            {
                                $this->addDatahandler($filename, $datahandler);
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
            return $this->datahandlerMap;
        }
        else
        {
            return $this->datahandlerMap[$key];
        }
    }

    public function __destruct()
    {
    }
}