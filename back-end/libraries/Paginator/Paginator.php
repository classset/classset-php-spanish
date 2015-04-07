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
	private $paginationButtons;
	private $action;

	public function __construct(ISession $session)
	{
		$this->session = $session;
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
	
	private function setPageNumber($pageNumber)
	{
	    if (!isset($pageNumber)) 
	    {
	    	$pageNumber=1;
	    	$this->session->set("page-number", $pageNumber);
	    }
	    else
		{
			$this->session->set("page-number", $pageNumber);
		}	   
	    
	    return $this;
	}

	private function getRowsPerPage()
	{
	    return $this->session->get("rows-per-page");
	}
	
	private function setRowsPerPage($rowsPerPage)
	{
	    if (!isset($rowsPerPage) or $rowsPerPage==0) 
	    {
	    	$rowsPerPage=5;
	    }
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

	private function getPaginationButtons()
	{
		$this->generatePaginationButtons();
	    return $this->session->get("paginationButtons");
	}
	
	private function setPaginationButtons($paginationButtons)
	{
	    $this->session->set("paginationButtons", $paginationButtons);
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

	public function getAction()
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
								.PAGE.$counter.OF.$lastPage.
							'</option>';
			}
			else
			{
				$options .= '<option name="value" value="'.$counter.'">'
								.PAGE.$counter.OF.$lastPage.
							'</option>';				
			}
		} 

		$paginationSelect .= '<select id="selected_page" 
								name="page_select" 
								class="form-control" 
								onchange="setPage(this.value)">
								
								'.$options.'

								</select>

								<script type="text/javascript">
								function setPage(value)
								{
									var myselect = document.getElementById("selected_page");
									var value = myselect.options[myselect.selectedIndex].value;
									document.location.href="index.php?rows-per-page='
									.$rowsPerPage.
									'&page-number="
									+value+
									"&'.$action.'";
								}
								</script> ';
		$this->setPaginationSelect($paginationSelect);
        return $this;		
	}

	private function generatePaginationButtons()
	{
		$paginationButtons = null;
		$firstPage = 1;
		$actualPage = $this->getPageNumber();
		$lastPage = $this->getEnd();
		$rowsTotalNumber = $this->getRowsTotalNumber();
		$rowsPerPage = $this->getRowsPerPage();
		$action = $this->action;
		
		if ($this->getPageNumber() > 1) 
		{
			$paginationButtons .= '<li>
									<a href="index.php?rows-per-page='
									.$rowsPerPage.
									'&page-number='
									.($firstPage).
									'&'.$action.'">
									<span aria-hidden="true">&laquo;'.FIRST.'</span>
									<span class="sr-only">'.FIRST.'</span>
									</a>
									</li>';			
		}
		else
		{
			$paginationButtons .= '<li class="disabled">
									<a href="">
									<span aria-hidden="true">&laquo;'.FIRST.'</span>
									<span class="sr-only">'.FIRST.'</span>
									</a>
									</li>';
		}	

		if ($this->getPageNumber() > 1) 
		{
			$paginationButtons .= '<li>
									<a href="index.php?rows-per-page='
									.$rowsPerPage.
									'&page-number='
									.($actualPage - 1).
									'&'.$action.'">
									<span aria-hidden="true">&laquo;</span>
									<span class="sr-only">'.PREVIOUS.'</span>
									</a>
									</li>';			
		}
		else
		{
			$paginationButtons .= '<li class="disabled">
									<a href="">
									<span aria-hidden="true">&laquo;</span>
									<span class="sr-only">'.PREVIOUS.'</span>
									</a>
									</li>';
		}	

		//// TEXT Page x of x
		// $paginationButtons .= '<li><a href="">'
		// 						.PAGE.$actualPage.OF.$lastPage.
		// 						'</a></li>';		

		//NUMBER BUTTONS:
		for ($counter = 1; $counter <= $this->getEnd(); $counter++) 
        { 
        	if ($counter == $this->getPageNumber()) 
        	{
        		$paginationButtons .= '<li class="active">
        								<a href="index.php?rows-per-page='
										.$rowsPerPage.
										'&page-number='
        								.$counter.
        								'&'.$action.'">'
        								.$counter.
        								'</a>
        								</li>';  
        	}
        	else
        	{
	           $paginationButtons .= '<li>
	           						<a href="index.php?rows-per-page='
										.$rowsPerPage.
										'&page-number='
	           						.$counter.
	           						'&'.$action.'">'
	           						.$counter.
	           						'</a>
	           						</li>';        		
        	}
		
        }

		if ($this->getPageNumber() < $this->getEnd()) 
		{
			$paginationButtons .= '<li>
									<a href="index.php?rows-per-page='
									.$rowsPerPage.
									'&page-number='
									.($actualPage + 1).
									'&'.$action.'">
									<span aria-hidden="true">&raquo;</span>
									<span class="sr-only">'.NEXT.'</span>
									</a>
									</li>';			
		}
		else
		{
			$paginationButtons .= '<li class="disabled">
									<a href="">
									<span aria-hidden="true">&raquo;</span>
									<span class="sr-only">'.NEXT.'</span>
									</a>
									</li>';
		}	

		if ($this->getPageNumber() < $this->getEnd()) 
		{
			$paginationButtons .= '<li>
									<a href="index.php?rows-per-page='
									.$rowsPerPage.
									'&page-number='
									.($lastPage).
									'&'.$action.'">
									<span aria-hidden="true">'.LAST.'&raquo;</span>
									<span class="sr-only">'.LAST.'</span>
									</a>
									</li>';			
		}
		else
		{
			$paginationButtons .= '<li class="disabled">
									<a href="">
									<span aria-hidden="true">'.LAST.'&raquo;</span>
									<span class="sr-only">'.LAST.'</span>
									</a>
									</li>';
		}	

		////GO TO PAGE
		// $paginationButtons .= '<br><br><form action="index.php" method="post" class="">
  // 								<input style="width: 15%;" name="page-number" type="number" 
  // 								min="1" max="'.$lastPage.'" value="'.$actualPage.'">
  // 								<input name="rows-total-number" type="hidden" value="'.$rowsTotalNumber.'">
  // 								<input type="submit" name="'.$action.'" value="'
  // 								.APPLY.
  // 								'" class="" id="submit">
		// 						</form>';

		////ROWS PER PAGE
		// $paginationButtons .= '<br><br><form action="index.php" method="post" class="">
  // 								'.ROWS_PER_PAGE.'
  // 								<input style="width: 15%;" name="rows-per-page" type="number" 
  // 								min="5" max="20" value="'.$rowsPerPage.'">
  // 								<input name="page-number" type="hidden" value="'.$actualPage.'">
  // 								<input name="rows-total-number" type="hidden" value="'.$rowsTotalNumber.'">
  // 								<input type="submit" name="'.$action.'" value="'
  // 								.APPLY.
  // 								'" class="" id="submit">
		// 						</form>';

        $this->setPaginationButtons($paginationButtons);
        return $this;	
	}
}