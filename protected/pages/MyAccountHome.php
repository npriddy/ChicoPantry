<?php

/*
  Module: MyAccountHome.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Allows users to update their passwords and display their info
 */

class MyAccountHome extends TPage {

    public function onLoad($param)
    /*
     * Purpose: Built in function runs when the page is loading.
     * Used to fill labels with information
     * Parameters
     *      @param TPage
     * Returns: nothing
     * Side-effects: Labels are set to to data
     */{
        parent::onLoad($param);
        if (!$this->IsPostBack) {

            if ($this->User->Name != "Guest") {
                //Get User By User ID
                $UserRecord = UserRecord::finder()->findByPk($this->User->getUserId());
                if ($UserRecord instanceof UserRecord) {
                    //Fills the labels with user information if it was a vlid user
                    $this->lblUserName->Text = $this->User->Name;
                    $this->lblEmail->Text = $UserRecord->Email;
                    $this->pnlMyAccount->Visible = true;
                    $this->pnlDemo->Visible = false;
                }
            }
        }
    }


      public function validateOldPassword($sender, $param)
    /*
     * Purpose: Checks to see if the user / password combo is valid
     * Parameters
     *     @param TControl $sender Validitor
     *     @param  TEventParameter $param  Validator control
     * Returns: nothing
     * Side-effects: If not valid error message displayed
     */
      {
        $UserRecord = UserRecord::finder()->findByPk($this->User->getUserId());
        //If the UserRecord exists and the password hash matches then the login was valid
        if ($UserRecord instanceof UserRecord && $UserRecord->Password == md5($this->txtOldPassword->Text)) {
            $param->IsValid = true;
        }
        else $param->IsValid = false;
      }

    public function Save_Clicked($sender, $param)
     /*
     * Purpose: Set the user password if the supplied password is valid
     * Parameters
     *     @param TControl $sender Button
     *     @param  TEventParameter $param  Button Text
     * Returns: nothing
     * Side-effects: If not valid error message displayed
     */{
        if ($this->IsValid) {
            //Retrive the current user ID
            $UserRecord = UserRecord::finder()->findByPk($this->User->getUserId());
            if ($UserRecord instanceof UserRecord) {
                //Update the user password
                $UserRecord->Password = md5($this->txtNewPassword->Text);
                $UserRecord->save();
                //Redirect the user to the home page.
                $this->Response->redirect($this->Service->constructUrl("Home", array('message' => 'Account Updated')));
            }
        }
    }

}

?>
