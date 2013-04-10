<?php
/*
  Module: Utilities.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Used for debuging process
 */
class Utilities
{
    function InsertLogEntry($text,$paramToVarDump)
    /**
    * Purpose: Insert vardump into mylog.log file for debugging
    * Parameters:
    *   @param $text string category text (ie "Generated Log Entry: "
    *   @param $paramToVarDump object to vardump
    * Returns: None
    * Side-effects: Error logged in MyLog.log
    */
    {
        PradoBase::trace($text . PradoBase::varDump($paramToVarDump),"MyCategory");
    }
}

?>
