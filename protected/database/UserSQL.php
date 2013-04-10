<?php
/*
  Module: UserSQL.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Provides SQL and Utility Functions
 *         For User Table
*/
class UserSQL Extends ChicoPantryDataModule
{
    public function GetAllUsers()
    /**
     * Purpose:  Returns all Users
     * Parameters: (none)
     * Returns: array of User data
     * Side-Effects: None
     */
    {
        $sql ="select
                      USER_ID,
                      Username,
                      Email,
                      Password,
                      Passwordsalt,
                      Role
                from User
                order by UserName Asc
                ;";
        return parent::runQuery($sql);
    }

    public function GetAllAuthorList()
    /**
     * Purpose:  Returns array of Users for drop downlist
     * Parameters: (none)
     * Returns: array of User data
     * Side-Effects: None
     */
    {
        $result = self::GetAllUsers();
        $data = array();
        foreach($result as $row)
        {
            $data[$row['USER_ID']] = $row['Username'];
        }
        return $data;
    }
}
?>
