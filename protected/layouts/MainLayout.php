<?php
/*
  Module: MainLayout.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: This is the site template (Master Page) for all the other content pages.
 */
class MainLayout extends TTemplateControl
{
  
    public function Logout_Clicked($sender,$param)
      /**
       * Purpose: logs the user out
     * Physically logs the user out and removes from session.
     * @param object sender
     * @param string param
     * @return none;
     */
    {
        //Removes the user from session
        $this->Application->Modules['auth']->logout();
        //Redirects the page back to Home.php based on setting in application.xml
        $this->Response->redirect($this->Service->DefaultPageUrl);
    }
}
?>