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

class Validator implements IValidator
{	
	private $nullValidator;
	private $messenger;
	private $redirector;

	public function __construct(IValidator $nullValidator, IMessenger $messenger, IRedirector $redirector)
	{
		$this->nullValidator = $nullValidator;
		$this->messenger = $messenger;
		$this->redirector = $redirector;
	}

	public function ifTrue($condition)
	{
		if($condition) return $this;
		return $this->nullValidator;
	}

	public function ifFalse($condition)
	{
		if(!$condition) return $this;
		return $this->nullValidator;
	}

	public function respond($response)
	{
		$this->messenger->say($response);
	}

	public function execute(IAction $action)
	{
		$action->execute();
	}

	public function redirectTo($location)
	{
		$this->redirector->redirectTo($location);
	}

	public function __destruct()
	{
		unset($this->nullValidator, $this->messenger, $this->redirector);
	}
}


?>