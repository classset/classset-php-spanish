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

class A_CreateRole implements IAction
{
    public function execute()
    {
        //PARAMETERS
        $params = RequestParametersFactory::create();
        $name = $params->get('role-name');
        $description = $params->get('role-description');

        //VALIDATOR (EXISTING ROLE)
        $datahandler = DatahandlerFactory::create();
        $datahandler['D_ReadRoleByName']->setInData($name);
        $existingRole = $datahandler['D_ReadRoleByName']->getOutData();
        $validator = ValidatorFactory::create();
        $validator->ifFalse( ($existingRole['name'] == null) )
                    ->respond(EXISTING_ROLE);

        //DATAHANDLER
        $datahandler['D_CreateRole']->setInData( array("name" => "$name", 
                                                        "description" => "$description"));

        //REDIRECTOR
        $redirector = RedirectorFactory::create();
        $redirector->redirectTo('index.php?A_ReadRolesPaginated');
    }
}
?>