<?php
/*
  Module: ChicoPantryErrorHandler.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Used by pradosoft framework to handel user errors
 *          This grabs the error stores it in our log file and redirects user
 *          to our error report page.
 */
Prado::using('System.Exceptions.TErrorHandler');
Prado::using('Application.common.ChicoPantryException');

/**
 * BlogErrorHandler class
 *
**/
class ChicoPantryErrorHandler extends TErrorHandler
{
	
	protected function handleExternalError($statusCode,$exception)
         /**
	 * Purpose: Displays error to the client user.
	 * THttpException and errors happened when the application is in <b>Debug</b>
	 * mode will be displayed to the client user.
         * Parameters:
	 *   @param integer response status code
	 *   @param Exception exception instance
          *Returns: None
          * Side-effects: Error logged in MyLog.log Page Redirected
	 */
	{
                //If the exception was thrown by the programmer
		if($exception instanceof ChicoPantryException)
		{
                    Utilities::InsertLogEntry("Error","Handled");
                    //Get the message text
                    $message=$exception->getMessage();
                    //Insert The actual full error intot he log
                    Prado::log($message,TLogger::ERROR,'ChicoPantryApplication');
                    //Encrypt the message for the query string
                    $message=urldecode($this->getApplication()->getSecurityManager()->hashData($message));
                    //Redirect to the error page with the encrypted message text
                    $this->Response->redirect($this->Service->constructUrl('ErrorReport',array('msg'=>$message),false));
		}
                //If the exception was thrown by the user
		else
                {
                    //Inser the exception into our log file
                    Utilities::InsertLogEntry("Status Code: $statusCode Error: ",$exception->getMessage());
                    //Grab the error from the parent
                    parent::handleExternalError($statusCode,$exception);
                    //Redirect to generic ErrorReport Page
                    $this->Response->redirect($this->Service->constructUrl('ErrorReport'));
                }
	}
}

?>