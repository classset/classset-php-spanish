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

class A_CreateUser implements IAction
{
	public function execute()
	{
        //FILTERS
        $filter = FilterFactory::create();        

        //PARAMETERS
        $params = RequestParametersFactory::create();
        $name = $filter->filters($params->get('user-name'));
        $password = $params->get('user-password');
        $passwordConfirmation = $params->get('password-confirmation');
        $encryptPassword = crypt($password);
        
        //VALIDATION
        $datahandler = DatahandlerFactory::create();
        $datahandler['D_ReadUserByName']->setInData($name);
        $data = $datahandler['D_ReadUserByName']->getOutData();
        $validator = ValidatorFactory::create();
        $validator->ifTrue(($data['name'] != null))->respond(EXISTING_USER);
        $validator->ifFalse( ($password == $passwordConfirmation) )->respond(PASSWORDS_NOT_MATCH);

        //DATAHANDLER
        $datahandler['D_CreateUser']->setInData
                    (
                        array
                        (
                            "name" => "$name", 
                            "password" => "$encryptPassword"
                        )
                    );

        //REDIRECTOR
        $redirector = RedirectorFactory::create();
        $redirector->redirectTo('index.php?A_ReadUsersPaginated');
	}
}

?>