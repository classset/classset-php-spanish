<?php
/**
 *  Copyright 2013 Pablo Daniel Spennato <pdspennato@gmail.com> 
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

class FileUploader
{
	private static $_instance;
	private $_namePostVariable = "files";
	private $_uploadDir = NULL;

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
		if (!(self::$_instance instanceof self))
		{
	 		self::$_instance=new self();
		}
		return self::$_instance;
	}

	public function setNamePostVariable($namePostVariable)
	{
		$this -> _namePostVariable = $namePostVariable;
	}

	public function setUploadDirectory($uploadDir)
	{
		$this -> _uploadDir = $uploadDir;
	}

    public function getNamePostVariable()
    {
    	return $this -> _namePostVariable;
    }

    public function getUploadDirectory()
    {
    	return $this -> _uploadDir;
    }

	public function fileUpload()
	{
	    if ($_FILES[$this -> _namePostVariable]["size"][0] == 0)
	    {
	      return NULL;
	    } 
	    $files = $this -> reArrayFiles($_FILES[$this -> _namePostVariable]);
	    $uploads_dir = dirname(dirname(dirname(__DIR__))).'/'.$this -> _uploadDir.'/';
	    $downloadLink = dirname($_SERVER['PHP_SELF']).'/'.$this -> _uploadDir.'/';

	    if( !is_dir($uploads_dir) ) {
	      mkdir($uploads_dir,0775,true);
	    }
	    
	    foreach ($files as $f) {
	      $file = $f['tmp_name'];
	      $name = $f['name'];
	      $name = $this -> upload($name,$uploads_dir,$file);
	      $downloadLink .= "$name";
	    }
	    return $downloadLink;
	}

	public function fileDownload($fileName) 
	{
		$file = dirname(dirname(dirname(__DIR__))).'/'.$this -> _uploadDir.'/'.$fileName;
        if(file_exists($file)) 
        {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        }

    }	

	private function reArrayFiles(&$file_post) 
	{
	  $file_ary = array();
	  $file_count = count($file_post['name']);
	  $file_keys = array_keys($file_post);
	  for ($i=0; $i<$file_count; $i++) {
	      foreach ($file_keys as $key) {
	          $file_ary[$i][$key] = $file_post[$key][$i];
	      }
	  }
	  return $file_ary;
	}

	private function upload($fileName, $dirDest, $tmpFile) 
	{    
	  $fullpath = $dirDest.$fileName;
	  $newFileName = $fileName;
	  $i = 1;
	  while (file_exists( $fullpath )) 
	  {
	      $file_name = substr($fileName, 0, strlen($fileName)-4);
	      $extfile  = substr($fileName, strlen($fileName)-4, strlen($fileName));
	      $newFileName = $file_name."[$i]".$extfile;
	      $fullpath = $dirDest.$newFileName;
	      $i++;
	  }
	  if (move_uploaded_file($tmpFile, $fullpath))    
	    return $newFileName;
	  else                                                 
	    return FALSE;
	}
}

?>
