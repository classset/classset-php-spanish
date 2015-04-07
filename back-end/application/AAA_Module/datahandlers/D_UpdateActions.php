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

class D_UpdateActions implements IDataset
{	
	public function setInData($data)
	{
		$allActions = $data;
		$allActionsKeys = array_keys($allActions);
		
		$db = DatabaseFactory::create()->connect();
		$query1 = "SELECT actions.name FROM actions";
		$oldActions = $db->SQLFetchAllArray($query1);
		
		$oldActionsValues = array();
		foreach ($oldActions as $key => $actions) 
		{
			foreach ($actions as $key => $actionValue) 
			{
				array_push($oldActionsValues, $actionValue);
			}
		}

		$newActions = array_diff($allActionsKeys, $oldActionsValues);
		if($newActions != array())
		{
			$queries = array();
			foreach ($newActions as $actionKey => $actionValue) 
			{
				$query2 = "INSERT INTO actions (name) VALUES ('$actionValue')";
				array_push($queries, $query2);
			}		
			$db = DatabaseFactory::create()->connect();
			$db->SQLTransaction($queries);
		}

	}
}

?>