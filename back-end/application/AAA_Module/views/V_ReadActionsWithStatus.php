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

class V_ReadActionsWithStatus implements IView, IDataset
{
    private $data;

    public function setInData($data)
    {
        $this->data = $data;
    }

    public function display()
    {
        $dom = DOMHandlerFactory::create();
        $dom->setDocumentFromFile(ACTIONS_ASSIGNMENT_HTML);

        $session = SessionFactory::create();
        $dom->whereIdIs('login-user')
                ->insertNode($session->get('session-user-name'));  

        $dom->whereIdIs('message-container')
            ->insertNode('Actions Assignment for: '.$this->data['role-name']);

        $dom->whereIdIs('actions-search-form')
                ->removeAttribute('style="display: none;"');
        
        $dom->whereIdIs('actions-assignment-form')
                ->removeAttribute('style="display: none;"');    

        $trs = null;
        foreach($this->data['role-actions'] as $actionRole) 
        {
            if ($actionRole['status'] == 1) 
            {
                $trs .= '<tr><td><div class="checkbox">
                    <label>
                        <b>'.$actionRole['name'].'</b>
                    </label>
                    </td>
                    <td><input name="selected-actions-names[]" 
                        type="checkbox" 
                        value="'.$actionRole['name'].'" checked
                    ></td>
                </div></tr>';
            }
            elseif($actionRole['status'] == 0)
            {
                $trs .= '<tr><td><div class="checkbox">
                        <label>
                            <b>'.$actionRole['name'].'</b>
                        </label>
                        </td>
                        <td><input name="selected-actions-names[]" 
                            type="checkbox" 
                            value="'.$actionRole['name'].'"></td>
                    </div></tr>';                
            }            
        }

        $dom->whereIdIs('tbody')->insertNode($trs); 

        $id = $session->get('selected-role-id');
        $paginator = PaginatorFactory::create();
        $paginator->action = "selected-role-id=".$id."&A_ReadActionsWithStatus";
        $dom->whereIdIs('ul-pagination')
            ->insertNode($paginator->paginationSelect);

        $dom->display();
    }
}
