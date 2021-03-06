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

class HttpRequestHandler implements IRequestHandler
{
    private static $_instance;
    private $mainAction;
    private $parser;

    private $selectedActionKey;

    /* methods: */
    private function __construct(IAction $mainAction, IParser $parser)
    {
        //ctor
        $this->mainAction = $mainAction;
        $this->parser = $parser;
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

    public static function createWith(IAction $mainAction, IParser $parser)
    {
        if (!(self::$_instance instanceof self))
        {
            self::$_instance=new self($mainAction, $parser);
        }
        return self::$_instance;
    }

    public function handle($request)
    {
        $this->selectedActionKey = $this->parser->parse($request);
        $this->mainAction->execute();
    }

    public function getSelectedActionKey()
    {
        return $this->selectedActionKey;
    }

    public function __destruct()
    {
        unset($this->mainAction, $this->parser);
    }
}
?>