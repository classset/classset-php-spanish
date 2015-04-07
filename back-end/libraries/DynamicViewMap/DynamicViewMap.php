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

class DynamicViewMap implements IMap
{
    private static $_instance;
    private $viewsPaths = array();
    private $viewMap;

    private function __construct($viewsPaths)
    {
        $this->viewsPaths = $viewsPaths;
        $this->viewMap = array();
        $this->loadView();
    }

    public static function getInstance($viewsPaths)
    {
        if (!(self::$_instance instanceof self))
        {
            self::$_instance = new self($viewsPaths);
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

    private function addView($viewKey, IView $view)
    {
        $this->viewMap[$viewKey] = $view;
        return $this;
    }

    //http://php.net/manual/es/function.readdir.php
    private function loadView()
    {
        foreach ($this->viewsPaths as $viewsPath) 
        {
            if ($directoryHandle = opendir($viewsPath)) 
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
                            $view = new $filename;
                            if($view instanceof IView)
                            {
                                $this->addView($filename, $view);
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
            return $this->viewMap;
        }
        else
        {
            return $this->viewMap[$key];
        }
    }

    public function __destruct()
    {
    }
}