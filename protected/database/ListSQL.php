<?php
/*
  Module: ListSQL.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Provides SQL and Utility Functions
 *         For List Table
*/
class ListSQL Extends ChicoPantryDataModule
{
    public function GetAllMealList($User_ID)
     /**
     * Purpose: Returns all the meal lists by User_ID with user info
     * Parameters:
     *          @param $User_ID
     * Returns: array of List data
     * Side-Effects: None
     */
    {
        $sql ="SELECT l.List_ID, l.User_ID, l.L_Name, u.Username
        FROM `List` l
        LEFT JOIN User u on u.USER_ID = l.User_ID
        WHERE l.`User_ID` = :User_ID";
        return parent::runQueryParam($sql, ":User_ID", $User_ID);
    }

    public function GetUserMealList($User_ID)
     /**
     * Purpose: Returns all the meal lists by User_ID
     * Parameters:
     *          @param $User_ID
     * Returns: array of List data
     * Side-Effects: None
     */
    {
        $sql ="SELECT List_ID, L_Name
        FROM `List`
        WHERE User_ID= :User_ID";
        return parent::runQueryParam($sql, ":User_ID", $User_ID);
   }

    public function GetAllMealListNamesList($User_ID)
    /**
     * Purpose: Returns all the meal list names by User_ID
     * Parameters:
     *          @param $User_ID
     * Returns: array of List data
     * Side-Effects: None
     */
    {
        $result = self::GetUserMealList($User_ID);
        $data = array();
        foreach($result as $row)
        {
            $data[$row['List_ID']] = $row['L_Name'];
        }
        return $data;
        Utilities::InsertLogEntry('GetAllMealListNamesList $data:',$data);
    }

    public function GetFirstList($User_ID)
     /**
     * Purpose: Returns First List Item
     * Parameters:
     *          @param $User_ID
     * Returns: array of List data
     * Side-Effects: None
     */
    {
        $sql = "SELECT List_ID, L_Name
        FROM `List`
        WHERE User_ID= :User_ID";
        return parent::runQueryParam($sql, ":User_ID", $User_ID);
    }
}
?>
