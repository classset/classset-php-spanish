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

class DOMHandler implements IDOMHandler
{
    private $document;
    private $idPosition;
    private $encoder;
    private $messenger;

    public function __construct(IEncoder $encoder, IMessenger $messenger)
    {
        //ctor
        $this->encoder = $encoder;
        $this->messenger = $messenger;
        $this->idPosition = 0;
    }

    public function setHeader($header)
    {
        header($header);
        return $this;
    }

    public function setDocumentFromString($str)
    {
        $this->document = $str;
        return $this;
    }   

    public function setDocumentFromFile($filePath)
    {
        $this->document = file_get_contents($filePath);
        return $this;
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function whereIdIs($id)
    {
        $this->idPosition = strpos($this->document, $id);

        if( ($this->idPosition == null) )
            $this->messenger->say("DOMHandler Error: The identifier \"".$id."\" does not exist :(");
                
        return $this;
    }

    public function insertNode($node)
    {
        $subString = substr($this->document, $this->idPosition);
        $tagEndPosition = strpos($subString, "<");
        if( ($tagEndPosition == null) )
            $this->messenger->say("DOMHandler Error: The position of character \"<\" does not exist :(");
        $targetPosition = $this->idPosition+$tagEndPosition;
        $this->document = substr_replace($this->document, $node."\n", $targetPosition, 0);
        return $this;
    }

    public function insertAttribute($attribute)
    {
        $subString = substr($this->document, $this->idPosition);
        $tagEndPosition = strpos($subString, ">");
        if( ($tagEndPosition == null) )
            $this->messenger->say("DOMHandler Error:  The position of character \">\" does not exist :(");
        $targetPosition = $this->idPosition+$tagEndPosition;
        $this->document = substr_replace($this->document, " ".$attribute, $targetPosition, 0);
        return $this;
    }

    public function removeAttribute($attribute)
    {
        $subString = substr($this->document, $this->idPosition);
        $attributePosition = strpos($subString, $attribute);      
        
        if( ($attributePosition != null) )
            $targetPosition = $this->idPosition+$attributePosition;
            $this->document = substr_replace($this->document, "", $targetPosition, strlen($attribute));
        
        if( ($attributePosition == null) )
            $this->messenger->say("DOMHandler Error: The position of attribute \"".$attribute."\" does not exist :(");

        return $this;
    }

    public function display()
    {
        echo $this->encoder->encode($this->document);
    }

    public function __destruct()
    {
        unset($this->messenger, $this->encoder);
    }
} 


?>