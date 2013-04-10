<?php
/*
  Module: Recipe_CategorySQL.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Provides SQL and Utility Functions
 *         For Recipe_Category Table
*/
class Recipe_CategorySQL Extends ChicoPantryDataModule {

  
    public function GetRecipeCategoryNameList($recipeID)
     /**
     * Purpose:  Returns a list of RecipeCategoryNames based on recipeID
     * Parameters:
     *          @param $recipeID = get all categories based on recipe ID
     * Returns: array of recipe_category data
     * Side-Effects: None
     */
    {

        $sql = "Select rc.RC_ID, rc.R_Category_ID, cat.RC_Name
                From Recipe_Category rc
                left join R_Category cat on cat.R_Category_ID = rc.R_Category_ID
                where rc.Recipe_ID = :recipeID";
        return parent::runQueryParam($sql, ":recipeID", $recipeID);
    }

    public function getCategoryStringForRecipe($recipeID)
     /**
     * Purpose:  returns a comma seperated string of categories based on the $recipID
     * Parameters:
     *          @param $recipeID = get all categories based on recipe ID
     * Returns: array of recipe_category data
     * Side-Effects: None
     */
    {
        //Retrieves the data rows for CategoryName based on racipe ID
        $result = self::GetRecipeCategoryNameList($recipeID);
        $str = "";
        //Creates comma seperated list of strings
        foreach ($result as $row) {
            $str .= $row['RC_Name'] . ', ';
        }
        //Removes the last space and comma
        return substr($str, 0, -2);
    }
}

?>
