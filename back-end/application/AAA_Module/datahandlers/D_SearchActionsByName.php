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

class D_SearchActionsByName implements IDataset, IDataget 
{	
	private $data;

	public function setInData($data)
	{
		$this->data = $data;
	}

	public function getOutData()
	{

		$id = $this->data["selected-role-id"];
		$name = $this->data["search-action-name"];
 
        $query = "SELECT actions.*, 1 as status
                  FROM actions 
                  LEFT JOIN roles_actions
                  ON roles_actions.action_name = actions.name
                  WHERE roles_actions.role_id = $id
                  AND actions.name LIKE '%$name%'

				  UNION

				  SELECT actions.*, 0 as status
                  FROM actions 
                  WHERE actions.name NOT IN 
                  (
                  	SELECT actions.name
			      	FROM actions
		    	  	LEFT JOIN roles_actions
		          	ON roles_actions.action_name = actions.name
		          	WHERE roles_actions.role_id = $id
		          ) AND actions.name LIKE '%$name%'
					ORDER BY actions.name DESC";

        $db = DatabaseFactory::create()->connect();
        return $db->SQLFetchAllArray($query);
	}
}

?>