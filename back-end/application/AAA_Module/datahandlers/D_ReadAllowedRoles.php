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

class D_ReadAllowedRoles implements IDataset, IDataget
{
    private $data;

    public function setInData($data)
    {
        $this->data = $data;
    }

    public function getOutData()
    {
      $actionName = $this->data['action-name'];
      $userId = $this->data['user-id'];
      $adminRoleId = $this->data['admin-role-id'];

      //conexión a la Base de datos
      $db = DatabaseFactory::create()->connect();

      $isAuthorized = false;

      $isAdmin = "SELECT roles.* 
                      FROM roles
                      LEFT JOIN users_roles
                      ON users_roles.role_id = roles.id
                      WHERE users_roles.user_id = $userId
                      AND users_roles.role_id = $adminRoleId";

      $isAllowed = "SELECT roles.* 
                        FROM roles
                        LEFT JOIN users_roles
                        ON users_roles.role_id = roles.id

                        INNER JOIN roles_actions
                        ON roles_actions.role_id = roles.id

                        WHERE users_roles.user_id = $userId
                        AND roles_actions.action_name = '$actionName'";

      if ($db->SQLFetchAllArray($isAdmin) != array()) 
      {
        $isAuthorized = TRUE;
      }

      if ($db->SQLFetchAllArray($isAllowed) != array()) 
      {
        $isAuthorized = TRUE;
      }
                
      return $isAuthorized;
    }
}
?>