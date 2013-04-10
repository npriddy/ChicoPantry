<?php
/*
  Module: BasePage.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Every web page inerhits from this and allows all the pages to share common functions
*/


class BasePage extends TPage
/**
 * BasePage Class.
 * Every web page inerhits from this and allows all the pages to share common functions
 * This goes in between the code and TPage
 *
 */
{

    
    public function getDataAccess()
    /**
     * Purpose: Returns the Data Module used for data connections
     * @return TModule;
     * Side-effects: None
     */
    {
        return $this->getApplication()->getModule('data');
    }

  
    public function gotoDefaultPage()
     /**
     * Purpose: Redirects the current page to the home page based on application.xml
     * @return none;
     * Side-effects: Page Redirected
     */
    {
        $this->gotoPage($this->Service->DefaultPage);
    }

    /**
     * Purpose: Forwards the page with paramaters and pagePath
     * @param string pagePath : Page to direct to
     * @param string getParameters default : null : Query string params to add
     * @return none;
     * Side-effects: Page Redirected
     */
    public function gotoPage($pagePath, $getParameters=null) {
        $this->Response->redirect($this->Service->constructUrl($pagePath, $getParameters, false));
    }
}

?>
