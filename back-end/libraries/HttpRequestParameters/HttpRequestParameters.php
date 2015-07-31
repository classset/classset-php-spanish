<?php
/**
 *  Copyright 2013 Pablo Daniel Spennato <pdspennato@gmail.com> 
 *  and 2013 Gabriel Nicolás González Ferreira <gabrielinuz@gmail.com>
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

class HttpRequestParameters implements IParameters
{
    private static $_instance;
    private $parameters = array();
    private $request = array();
    private $filter;

    private function __construct($request, $filter)
    {
        $this->filter = $filter;
        $this->request = $request;
        $this->escape_request_parameters();
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

    private function escape_request_parameters()
    {
       foreach ($this->request as $key => $value)
       {
            $filteredKey = $this->filter->filters($key);
            if (is_array($value))
            {
                $filteredValue = array();
                for ($i=0; $i < count($value); $i++) 
                {
                   $val = $this->filter->filters($value[$i]);
                   $filteredValue[$i] = $val;
                }
                $this->parameters[$filteredKey] = $filteredValue;
            }    
            else
            {
               $filteredValue = $this->filter->filters($value);
               $this->parameters[$filteredKey] = $filteredValue;    
            }
        }
    }

    public static function createWith($request, $filter)
    {
        if (!(self::$_instance instanceof self))
        {
            self::$_instance=new self($request, $filter);
        }
        return self::$_instance;
    }

    public function get($key)
    {
        if (isset($this->parameters[$key]))
        {
            return $this->parameters[$key];
        }
        else
        {
            return NULL;
        }
    }

    public function getAll()
    {
        if (isset($this->parameters))
        {
            return $this->parameters;
        }
        else
        {
            return NULL;
        }   
    }
    }
?>
