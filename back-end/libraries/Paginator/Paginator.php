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

class Paginator implements IPaginator
{
	//attributes:
	private $session;
	private $pageNumber;
	private $rowsPerPage;
	private $beginning;
	private $end;
	private $paginationSelect;
	private $action;

	public function __construct(ISession $session)
	{
		$this->session = $session;

		$this->setConstants();

		if(empty($_REQUEST['rows-per-page']))
		{
			$this->setRowsPerPage();
		}
		else
		{
			$this->setRowsPerPage($_REQUEST['rows-per-page']);
		}

		if(empty($_REQUEST['page-number']))
		{
			$this->setPageNumber();
		}
		else
		{
			$this->setPageNumber($_REQUEST['page-number']);
		}
	}

	/*FOR PROPERTIES*/
	public function __get($name)
	{
	    if (method_exists($this, ($method = 'get'.ucfirst($name))))
	    {
	      return $this->$method();
	    }
	    else return;
	}
	
	public function __set($name, $value)
	{
	    if (method_exists($this, ($method = 'set'.ucfirst($name))))
	    {
	      $this->$method($value);
	    }
	}
	/*FOR PROPERTIES*/
	
	/*GETTERS AND SETTERS*/
	private function getPageNumber()
	{
	    return $this->session->get("page-number");
	}
	
	private function setPageNumber($pageNumber = 1)
	{
		$this->session->set("page-number", $pageNumber);
	    return $this;
	}

	private function getRowsPerPage()
	{
	    return $this->session->get("rows-per-page");
	}
	
	private function setRowsPerPage($rowsPerPage = 5)
	{
	    $this->session->set("rows-per-page", $rowsPerPage);
	    return $this;
	}

	private function getRowsTotalNumber()
	{
	    return $this->session->get("rows-total-number");
	}
	
	private function setRowsTotalNumber($rowsTotalNumber)
	{
	    $this->session->set("rows-total-number", $rowsTotalNumber);
	    return $this;
	}

	private function getBeginning()
	{
		$this->calculateBegining();
	    return $this->session->get("beginning");
	}
	
	private function setBeginning($beginning)
	{
	    $this->session->set("beginning", $beginning);
	    return $this;
	}

	private function getEnd()
	{
		$this->calculateEnd();
	    return $this->session->get("end");
	}
	
	private function setEnd($end)
	{
	    $this->session->set("end", $end);
	    return $this;
	}

	private function getPaginationSelect()
	{
		$this->generatePaginationSelect();
	    return $this->session->get("paginationSelect");
	}
	
	private function setPaginationSelect($paginationSelect)
	{
	    $this->session->set("paginationSelect", $paginationSelect);
	    return $this;
	}

	private function getAction()
	{
	    return $this->action;
	}
	
	public function setAction($action)
	{
	    $this->action = $action;
	    return $this;
	}
	/*GETTERS AND SETTERS*/

	private function calculateBegining()
	{
		if(is_numeric( $this->getPageNumber() ))
		{
			$this->setBeginning( ( ( $this->getPageNumber() - 1 ) * $this->getRowsPerPage() ) );
		}
		else
		{
			$this->setBeginning(0);
		}
		return $this;
	}

	private function calculateEnd()
	{
		$this->setEnd( ceil( $this->getRowsTotalNumber() / $this->getRowsPerPage() ) );
		return $this;
	}

	private function setConstants()
	{
		if (!defined('CL_PAGE')) {	define('CL_PAGE', 'Page: ');}
		if (!defined('CL_OF')) { define('CL_OF', ' of ');	}
		if (!defined('CL_NO_RESULTS')) { define('CL_NO_RESULTS', ' No results! ');	}
	}

	private function generatePaginationSelect()
	{
		$paginationSelect = null;
		$firstPage = 1;
		$actualPage = $this->getPageNumber();
		$lastPage = $this->getEnd();
		$rowsTotalNumber = $this->getRowsTotalNumber();
		$rowsPerPage = $this->getRowsPerPage();
		$action = $this->action;

		$options = null;
		for ($counter = 1; $counter <= $this->getEnd(); $counter++) 
		{
			if ($counter == $actualPage) 
			{
				$options .= '<option selected value="'.$counter.'">'
								.CL_PAGE.$counter.CL_OF.$lastPage.
							'</option>'."\n";
			}
			else
			{
				$options .= '<option name="value" value="'.$counter.'">'
								.CL_PAGE.$counter.CL_OF.$lastPage.
							'</option>'."\n";				
			}
		} 

		if($this->rowsTotalNumber == 0)
		{
			$paginationSelect .= '<strong><h1>'.NO_RESULTS.'</h1></strong><br><br>';
		}

		$paginationSelect .= '<select id="selected_page" name="page_select" ';
		$paginationSelect .= 'class="form-control" onchange="setPage(this.value)">'."\n";
		$paginationSelect .= $options;
		$paginationSelect .= '</select>'."\n";
		$paginationSelect .= '<script type="text/javascript">'."\n";
		$paginationSelect .= 'function setPage(value)'."\n";
		$paginationSelect .= '{'."\n";
		$paginationSelect .= 'var myselect = document.getElementById("selected_page");'."\n";
		$paginationSelect .= 'var value = myselect.options[myselect.selectedIndex].value;'."\n";
		$paginationSelect .= 'document.location.href="index.php?rows-per-page=';
		$paginationSelect .= $rowsPerPage;
		$paginationSelect .= '&page-number="+value+"&';
		$paginationSelect .= $action;
		$paginationSelect .= '";'."\n";
		$paginationSelect .= '}'."\n";
		$paginationSelect .= '</script> ';

		$this->setPaginationSelect($paginationSelect);

        return $this;		
	}
}