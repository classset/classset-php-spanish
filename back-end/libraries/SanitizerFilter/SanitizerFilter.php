<?php
/*--------------------------------------------------------------------------------------------------
    SanitizerFilter.php
    ________________________________________________________________________________________________

    History of Modifications:

    Date(dd/mm/yyyy)        Person                                  Description
    ----------------        ------                                  -----------
    --/--/20--              Denham Coote                            Initial design and coding
    27/05/2013              Gabriel Nicolás González Ferreira       Generalization, design and coding

    VERSION: 1.0

    DESCRIPTION:
        This class is a simple filter for escape and prevent SQL injection in PHP language.

    LICENSE:
        You are free to change or modify or redistribute the code, just keep the header.
        And you can use this class in any application you want without any warranty.
*/

class SanitizerFilter implements IFilter
{
    private function cleanInput($input) 
    {
     
      $search = array(
        '@<script [^>]*?>.*?@si',            // Strip out javascript
        '@< [/!]*?[^<>]*?>@si',            // Strip out HTML tags
        '@<style [^>]*?>.*?</style>@siU',    // Strip style tags properly
        '@< ![sS]*?--[ tnr]*>@'         // Strip multi-line comments
      );
     
        $output = preg_replace($search, '', $input);
        return $output;
    }

    private function escape_quotes($input)
    {
        $pattern = array(); 
        $pattern[0] = "/'/";

        $replacement = array();
        $replacement[0] = "''";

        return preg_replace($pattern, $replacement, $input);
    } 

    public function filters($input) 
    {
        if (is_array($input)) 
        {
            foreach($input as $var=>$val) 
            {
                $output[$var] = $this->filters($val);
            }
        }
        else 
        {
            if (get_magic_quotes_gpc()) 
            {
                $input = stripslashes($input);
            }
            $output = $this->escape_quotes($input);
        }
        return $output;
    }
}