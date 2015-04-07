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

class DynamicActionMap implements IMap
{
    private static $_instance;
    private $actionsPaths = array();
    private $actionMap;

    private function __construct($actionsPaths)
    {
        $this->actionsPaths = $actionsPaths;
        $this->actionMap = array();
        $this->loadAction();
    }

    public static function getInstance($actionsPaths)
    {
        if (!(self::$_instance instanceof self))
        {
            self::$_instance = new self($actionsPaths);
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

    private function addAction($actionKey, IAction $action)
    {
        $this->actionMap[$actionKey] = $action;
        return $this;
    }

    //http://php.net/manual/es/function.readdir.php
    private function loadAction()
    {
        foreach ($this->actionsPaths as $actionsPath) 
        {
            if ($directoryHandle = opendir($actionsPath)) 
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
                            $action = new $filename;
                            if($action instanceof IAction)
                            {
                                $this->addAction($filename, $action);
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
            return $this->actionMap;
        }
        else
        {
            return $this->actionMap[$key];
        }
    }

    public function __destruct()
    {
    }
}