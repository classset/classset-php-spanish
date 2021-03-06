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

class A_Authenticate implements IAction
{
    public function execute()
    {
        //SESSION
        $session = SessionFactory::create();

        //PARAMETERS
        $params = RequestParametersFactory::create();
        $session = SessionFactory::create();

        if( !$session->get("authenticated") and $params->get('public_key') == $session->get('randLogin') )
        {
            //PARAMETERS:
            $params = RequestParametersFactory::create();
            $username = $params->get('user-name');
            $userpassword = $params->get('user-password');

            //DATAHANDLER
            $datahandler = DatahandlerFactory::create('D_ReadUserByName');
            $datahandler->setInData($username);
            $existingUser = $datahandler->getOutData();    

            //PASSWORD VERIFY
            $encryptor = EncryptorFactory::create();
            $isAuthenticate = $encryptor->verify($userpassword, $existingUser['password']);

            if( $isAuthenticate )
            {
                //SET SESSION DATA
                $session->set('session-user-name', $existingUser['name']);
                $session->set('session-user-id', $existingUser['id']);
                $session->set("authenticated", true);
            }
            else
            {
                $session->set("authenticated", false);
            }            
        }
    }
}
?>