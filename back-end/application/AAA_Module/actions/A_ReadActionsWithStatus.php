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

class A_ReadActionsWithStatus implements IAction
{
	public function execute()
	{
        //PARAMETERS
        $params = RequestParametersFactory::create();
        $roleId = $params->get('selected-role-id');
        $pageNumber = $params->get('page-number');
        $rowsPerPage = $params->get('rows-per-page');
        
        //PAGINATOR
        $paginator = PaginatorFactory::create();
        $paginator->pageNumber = $pageNumber;

        $datahandler = DatahandlerFactory::create('D_ActionsRowsTotalNumber');
        $actionsRowsTotalNumber = $datahandler->getOutData();
        $paginator->rowsPerPage = $actionsRowsTotalNumber;
        
        //Always force a single page
        $paginator->rowsPerPage = $actionsRowsTotalNumber;

        //SESSION
        $session = SessionFactory::create();
        $session->set('selected-role-id',$roleId);

        //DATAHANDLER
        $datahandler = DatahandlerFactory::create('D_ReadActionsWithStatus');
        $datahandler->setInData($roleId);
        $roleActions = $datahandler->getOutData();
 
        //DATAHANDLER
        $datahandler = DatahandlerFactory::create('D_ReadRoleById');
        $datahandler->setInData($roleId);
        $roleData = $datahandler->getOutData();
        
        //SESSION
        $session->set('selected-role-name',$roleData['name']);
        
        $data = array
                (
                    'role-actions' => $roleActions,
                    'role-name' => $roleData['name'] 
                );

        //VIEW
        $view = ViewFactory::create('V_ReadActionsWithStatus');
        $view->setInData($data);
        $view->display();
	}
}

?>