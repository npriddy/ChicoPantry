<?php
/*
  Module: ChicoPantryException.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Returns log file for Prado:Log
 */

/**
 * BlogException class
 */
class ChicoPantryException extends THttpException {

    protected function getErrorMessageFile()
    /**
     * Purpose:Return path to error file
     * Returns: string path to the error message file
     * Side-effects: none
     */
    {
        //Current Directory
        return dirname(__FILE__) . '/logs/MyLog.log';
    }
}

?>