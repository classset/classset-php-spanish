<?php
/*--------------------------------------------------------------------------------------------------
    SingleQuotesFilter.php
    ________________________________________________________________________________________________

    History of Modifications:

    Date(dd/mm/yyyy)        Person                                  Description
    ----------------        ------                                  -----------
    27/05/2013              Gabriel Nicolás González Ferreira       Initial design and coding

    VERSION: 1.0

    DESCRIPTION:
        This class is a simple filter for escape single quotes in PHP language.

    LICENSE:
        You are free to change or modify or redistribute the code, just keep the header.
        And you can use this class in any application you want without any warranty.
*/

class SingleQuotesFilter implements IFilter
{
    public function filters($subject)
    {
        $pattern = array(); 
        $pattern[0] = "/'/";

        $replacement = array();
        $replacement[0] = "''";

        return preg_replace($pattern, $replacement, $subject);
    }  
}


?>
