<?php
/*
  Module: PantryUser.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: User class stored in session for the user credentials
 */

// Include TDbUserManager.php file which defines TDbUser
Prado::using('System.Security.TDbUserManager');

/**
 * BlogUser Class.
 * BlogUser represents the user data that needs to be kept in session.
 * Default implementation keeps username and role information.
 */

class PantryUser extends TDbUser
{

    public function createUser($username)
    /**
     * Purpose: Creates a BlogUser object based on the specified username.
     *          This method is required by TDbUser. It checks the database
     *          to see if the specified username is there. If so, a BlogUser
     *          object is created and initialized.
     * Parameters:
     *          @param $username string the specified username
     * Returns: User Object or NUll
     * Side-Effects: PantryUser object now in session
     */
    {
        // use UserRecord Active Record to look for the specified username
        $userRecord=UserRecord::finder()->findBy_username($username);
        if($userRecord instanceof UserRecord) // if found
        {
            //Retrieves the pantry user manager and assigns it to the user
            $user=new PantryUser($this->Manager);
            $user->Name=$username;  // set username
            //Sets the role for user
            $user->Roles=($userRecord->Role==1?'admin':'user'); // set role
            $user->IsGuest=false;   // the user is not a guest
            return $user;
        }
        else
            return null;
    }


    public function validateUser($username,$password)
    /**
     * Purpose: Checks if the specified (username, password) is valid.
     *          This method is required by TDbUser.
     * Parameters:
     *           @param string username
     *           @param string password
     * Returns: boolean whether the username and password are valid.
     * Side-Effects: (none)
     */
    {
        // use UserRecord Active Record to look for the (username, password) pair.
        return UserRecord::finder()->findBy_username_AND_password($username,md5($password))!==null;
    }


    public function getIsAdmin()
    /**
     * Purpose: Checks whether this user is an administrator
     * Parameters: (none)
     * Returns: bool if the user is admin
     * Side-Effects: (none)
     */
    {
        return $this->isInRole('admin');
    }

    public function getUserId()
     /**
     * Purpose: Returns the logged in users ID
     * Parameters: (none)
     * Returns: int of the ID if none are found then 0
     * Side-Effects: (none)
     */
    {
    //Retrieve user record by user name
    $userRecord = UserRecord::finder()->findBy_username($this->Name);
    if ($userRecord != null)
        return $userRecord->USER_ID;
    else
        return 0;
    }
}

?>
