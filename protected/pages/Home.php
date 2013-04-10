<?php
/*
  Module: Home.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Home landing page.
 */
class Home extends BasePage {

    public function onLoad($param)
     /*
     * Purpose: Built in function runs when the page is loading.
     * Used to load grids for display
     * Parameters
     *      @param TPage
     * Returns: nothing
     * Side-effects: possible for message label to be displayed
     */{
        parent::onLoad($param);
        if (!$this->IsPostBack) {
            //If there exists text for 'message' then display it on the home page
            if ($this->Request['message'] != null) {
                $this->lblMessage->Text = $this->Request['message'];
                $this->lblMessage->Visible = true;
            }
        }
    }
}
?>
