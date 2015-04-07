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

class A_DeleteRoleConfirmation implements IAction
{
    public function execute()
    {
        $params = RequestParametersFactory::create();
        $id = $params->get('role-id');

        $validator = ValidatorFactory::create();
        $validator->ifTrue( ($id == "1") )->respond(NOT_DELETE_ADMIN);

        $datahandler = DatahandlerFactory::create('D_ReadUsedRoles');
        $datahandler->setInData($id);
        $data = $datahandler->getOutData();

        $validator->ifTrue( ($data != array()) )->respond(USED_ROLE);

        $session = SessionFactory::create();
        $session->set('role-id', $id);

        $datahandler = DatahandlerFactory::create('D_ReadRoleById');
        $datahandler->setInData($id);
        $data = $datahandler->getOutData();

        $view = ViewFactory::create('V_DeleteRoleConfirmation');
        $view->setInData($data);
        $view->display();
        
        unset($datahandler, $view);
    }
}

?>