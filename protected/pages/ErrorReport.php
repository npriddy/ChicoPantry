<?php
/*
  Module: ErrorReport.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Used for displaying generic error page to use when there are errors.
 */

class ErrorReport extends BasePage
{
	public function onLoad($param)
        /*
        * Purpose: Built in function runs when the page is loading.
        * Used to load grids for display
        * Parameters
        *      @param TPage
        * Returns: nothing
        * Side-effects: Message might be displayed
        */
	{
		parent::onLoad($param);
                //If there is a valid encoded message from the error handler than display it.
		$this->ErrorMessage->Text=$this->Application->SecurityManager->validateData(urldecode($this->Request['msg']));
	}
}

?>