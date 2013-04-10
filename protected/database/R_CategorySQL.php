<?php
/*
  Module: R_CategorySQL.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Provides SQL and Utility Functions
 *         For R_Category Table
*/
class R_CategorySQL Extends ChicoPantryDataModule
{
    public function GetAllRecipeCategoryNames()
     /**
     * Purpose: Returns all Recipe Category names
     * Parameters: (none)
     * Returns: array of Recipe_Category objects data
     * Side-Effects: None
     */
    {
        $sql ="select R_Category_ID, RC_Name from R_Category order by RC_Name";
        return parent::runQuery($sql);
    }

     public function GetAllRecipeCategoryNamesList($bool = null)
     /**
     * Purpose: Returns all Recipe Category Names
     * Parameters:
     *          @param $bool if true than add all to the array list
     * Returns: array of Pantry Recipe_Category data
     * Side-Effects: None
     */
    {
        $result = self::GetAllRecipeCategoryNames();
        $data = array();
        if($bool != null)
            $data[0] = "All";

        foreach($result as $row)
        {
            $data[$row['R_Category_ID']] = $row['RC_Name'];
        }
        return $data;
    }
}
?>
