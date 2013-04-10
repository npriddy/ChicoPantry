<?php
/*
  Module: Recipe_ListSQL.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Provides SQL and Utility Functions
 *         For Recipe_List Table
*/
class Recipe_ListSQL Extends ChicoPantryDataModule
{
    public function GetRecipeMealList($List_ID)
     /**
     * Purpose:  Returns Recipe_List based on recipeID
     * Parameters:
     *          @param $List_ID
     * Returns: array of Recipe_List data
     * Side-Effects: None
     */
    {

        $sql ="SELECT rl.RL_ID, rl.List_ID, rl.Recipe_ID,
            r.R_Name, r.Cook_Time, r.Author
            FROM Recipe_List rl
            LEFT JOIN Recipe r on r.RI_ID = rl.Recipe_ID
            WHERE rl.`List_ID` = :List_ID";
        return parent::runQueryParam($sql, ":List_ID", $List_ID);
    }

    public function GetRecipeTable($List_ID)
     /**
     * Purpose:  Returns Recipe_List based on recipeID with user name
     * Parameters:
     *          @param $List_ID
     * Returns: array of Recipe_List data
     * Side-Effects: None
     */
    {
        $sql ="SELECT r.RI_ID,r.R_Name,
            if(IsNull(r.Cook_Time),0,r.Cook_Time) as 'Cook_Time', r.Author,
            u.Username, rl.RL_ID
            FROM Recipe_List rl
            LEFT JOIN Recipe r on r.RI_ID = rl.Recipe_ID
            LEFT JOIN User u on u.USER_ID = r.Author
            WHERE rl.`List_ID` = :List_ID";
        return parent::runQueryParam($sql, ":List_ID", $List_ID);
    }


    public function GenerateGroceryList($List_ID)
     /**
     * Purpose:  Returns Recipe_List based on List_ID returns all ingredients
     * Parameters:
     *          @param $List_ID
     * Returns: array of Recipe_List data
     * Side-Effects: None
     */
    {
        $sql = "SELECT DISTINCT I.Ingredient_Name, IC.IC_Name
            FROM Ingredient I
            Left Join Recipe_Ingredient RI on RI.Ingredient_ID=I.Ingredient_ID
            Left Join Recipe_List RL on RL.Recipe_ID=RI.Recipe_ID
            Left Join List L on L.List_ID=RL.List_ID
            Left Join I_Category IC on IC.`I_Category_ID`=I.`I_Category_ID`
            WHERE
            L.List_ID=  :List_ID
            Order By IC.IC_Name";
         return parent::runQueryParam($sql, ":List_ID", $List_ID);
    }
}
?>
