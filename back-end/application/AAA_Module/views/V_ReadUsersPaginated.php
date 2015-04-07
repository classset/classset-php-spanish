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

class V_ReadUsersPaginated implements IView, IDataset
{
    private $data;

    public function setInData($data)
    {
        $this->data = $data;
    }

    public function display()
    {
        $session = SessionFactory::create();
        $dom = DOMHandlerFactory::create();
        $dom->setDocumentFromFile(USERS_HTML)

                ->whereIdIs('login-user')
                    ->insertNode($session->get('session-user-name'))
        
                ->whereIdIs('table')
                    ->removeAttribute('style="display: none;"');

        $trs = null;
        foreach ($this->data['users'] as $key => $user) 
        {
            $trs .= "<tr><td>".$user["name"]."</td>";

            $trs .= "<td><a href='?user-id=".$user["id"]."&A_UpdateUserForm'";
            $trs .= "title='Update User' class='button'>";
            $trs .= "<i class='glyphicon glyphicon-pencil'></i></a> ";

            $trs .= "<a href='?user-id=".$user["id"]."&A_DeleteUserConfirmation'";
            $trs .= "title='Delete User' class='button'>";
            $trs .= "<i class='glyphicon glyphicon-trash'></i></a></td></tr>";       
        }
        $dom->whereIdIs("tbody")->insertNode($trs); 

        $paginator = PaginatorFactory::create();
        $paginator->action = "A_ReadUsersPaginated";
        $dom->whereIdIs('ul-pagination')
            ->insertNode($paginator->paginationSelect);
        
        $dom->display();
    }
}