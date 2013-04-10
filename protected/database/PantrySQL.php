<?php
/*
  Module: PantrySQL.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Provides SQL and Utility Functions
 *         For Pantry Table
*/
class PantrySQL Extends ChicoPantryDataModule
{
    public function GetPantryIngredientList($User_ID)
     /**
     * Purpose: Returns all ingredients based on the pantry
     * Parameters:
     *          @param $User_ID
     * Returns: array of Pantry objects data
     * Side-Effects: None
     */
    {
        $sql ="SELECT
            p.Pantry_ID, p.User_ID, p.Ingredient_ID, p.Amount, p.Measure_ID,
            i.Ingredient_Name, m.Name
            FROM Pantry p
            LEFT JOIN Ingredient i on i.Ingredient_ID = p.Ingredient_ID
            LEFT JOIN Measure m on m.Measure_ID = p.Measure_ID
            WHERE `User_ID` = :User_ID";
        return parent::runQueryParam($sql, ":User_ID", $User_ID);
    }
}
?>
