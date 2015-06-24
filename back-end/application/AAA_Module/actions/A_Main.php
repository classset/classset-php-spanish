<?php
/**
*	Copyright 2013 Gabriel Nicolás González Ferreira <gabrielinuz@gmail.com> 
*
*	Permission is hereby granted, free of charge, to any person obtaining
*	a copy of this software and associated documentation files (the
*	"Software"), to deal in the Software without restriction, including
*	without limitation the rights to use, copy, modify, merge, publish,
*	distribute, sublicense, and/or sell copies of the Software, and to
*	permit persons to whom the Software is furnished to do so, subject to
*	the following conditions:
*
*	The above copyright notice and this permission notice shall be
*	included in all copies or substantial portions of the Software.
*
*	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
*	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
*	MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
*	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
*	LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
*	OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
*	WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
**/

class A_Main implements IAction
{
    public function execute()
    {
        //SESSION
        $session = SessionFactory::create();

        if ($session->get("authenticated") == null) $session->set("authenticated", false);

        if ($session->get("authorized") == null) $session->set("authorized", false);

        //ACTIONS
        $actions = ActionFactory::create();

        //REQUESTHANDLER AND SELECTACTIONKEY
        $requestHandler = RequestHandlerFactory::create();
        $selectedActionKey = $requestHandler->getSelectedActionKey();

        //VALIDATOR
        $validator = ValidatorFactory::create();

        //REDIRECTOR
        $redirector = RedirectorFactory::create();

        ////LOGICA DE AUTENTICACIÓN Y AUTORIZACIÓN:
        //Si no está autenticado se ejecuta la acción de autenticación
        //, esto podría ser también si selecciona Authenticate
        $validator->ifFalse( $session->get("authenticated") )
                    ->execute($actions['A_Authenticate']);

        //Si selecciona Logout Action se le permite ejecutar siempre.
        $validator->ifTrue( $selectedActionKey == 'A_Logout' )
                    ->execute($actions['A_Logout']);

        //Si después de ser autenticado entra aquí no está autenticado 
        //se ejecuta Logout:
        $validator->ifFalse( $session->get("authenticated") )
                    ->execute($actions['A_Logout']);

        //Si está autenticado y no autorizado se ejecuta la acción de autorización
        //(siempre se debe autorizar, para que esto sea más eficiente armar un cache en
        //session con las acciones autorizadas)
        $actions['A_Authorize']->execute();

        //Si está autenticado y no autorizado:
        $validator->ifFalse( $session->get("authorized") )
                    ->respond(NO_AUTHORIZED_ACTION);

        /*Si está autenticado y autorizado y quiere ejecutar nada o login lo 
        redirijo a default action:*/
        $validator->ifTrue( $selectedActionKey == "" )
                    ->redirectTo('index.php?A_ReadUsersPaginated');		
        $validator->ifTrue( $selectedActionKey == "A_Authenticate" )
                    ->redirectTo('index.php?A_ReadUsersPaginated');

        //Si está autenticado y autorizado y ejecuta una acción no existente
        $validator->ifFalse( array_key_exists($selectedActionKey, $actions) )
                    ->respond($selectedActionKey." ".NOT_IMPLEMENTED);

        //Si está autenticado y autorizado y ejecuta una acción existente
        $actions[$selectedActionKey]->execute();
    }
}
?>