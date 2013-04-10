<?php
/*
  Module: Login.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Used for account creation and Users Logging In
 */
class Login extends TPage {

    public function validateUser($sender, $param)
     /*
     * Purpose: Checks to see if the user / password combo is valid
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Validator control
     * Returns: nothing
     * Side-effects: none
     */
    {
        $user = $this->txtUserName->Text;
        $pass = $this->txtPassword->Text;

        //Uses authentication module with PantryUser to verify
        $auth = $this->Application->Modules['auth'];
        $param->IsValid = $auth->login($user, $pass);
    }

    public function btnLogin_Clicked($sender, $param)
     /*
     * Purpose: Checks to see if the user / password combo is valoid
     * Parameters
     *     @param TControl $sender Button
     *     @param TEventParameter $param  button text
     * Returns: nothing
     * Side-effects: none
     */{
        if ($this->IsValid) {
            //Calls the validate user function
            $url = $this->Application->Modules['auth']->ReturnUrl;
            //Used for in future case if we wish admins to be directed to a different page.
            if (empty($url))
                $url = $this->Service->DefaultPageUrl;
            //Direct the page to this url
            $this->Response->redirect($url);
        }
    }

    /**
     * Verify that the username is not taken.
     * @param TControl custom validator that created the event.
     * @param TServerValidateEventParameter validation parameters.
     */
    public function checkUsername($sender, $param)
     /*
     * Purpose: Checks to see if the username already exists
     * Parameters
     *     @param TControl  $sender Validator
     *     @param TEventParameter $param  if is valid
     * Returns: nothing
     * Side-effects: Page displays error message if not valid
     */
    {
        $param->IsValid = UserRecord::finder()->findBy_username($this->txtNewUserName->Text) === null;
    }

    public function createNewUser($sender, $param)
    /*
     * Purpose:
     *  Create a new user if all data entered are valid.
     * The default user roles are obtained from "config.xml". The new user
     * details is saved to the database and the new credentials are used as the
     * application user. The user is redirected to the requested page.
     * @param TControl button control that created the event.
     * Parameters
     *     @param TControl $sender Button
     *     @param TEventParameter $param  Button Text
     * Returns: nothing
     * Side-effects: Page displays error message if not valid
     */
    {
        if ($this->IsValid)
        {
            //Creates a New User Object
            $newUser = new UserRecord($this->User->Manager);
            $newUser->Email = $this->txtNewEmail->Text;
            $newUser->Username = $this->txtNewUserName->Text;
            $newUser->Role = 0;
            $newUser->Password = md5($this->txtNewPassword->Text);
            $newUser->save();
            //save the user into the database

            //Redirects the user to the home page
            $auth = $this->Application->Modules['auth'];
            $auth->login($user, $pass);
            $this->Response->redirect($this->Service->constructUrl("Home", array('message' => 'Account Created! You may now login.')));

        }
    }
}

?>