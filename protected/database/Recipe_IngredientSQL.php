<?php
/*
  Module: Recipe_IngredientSQL.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Provides SQL and Utility Functions
 *         For Recipe_Ingredient Table
*/
class Recipe_IngredientSQL Extends ChicoPantryDataModule
{
    public function GetIngredientByRecipeList($recipeID)
     /**
     * Purpose:  Returns a list of Recipe_Ingredients based on recipeID
     * Parameters:
     *          @param $recipeID = get all Recipe_Ingredients based on recipeID
     * Returns: array of Comments data
     * Side-Effects: None
     */
    {
        $sql ="select
                    ri.RI_ID,
                    ri.Recipe_ID,
                    i.Ingredient_Name,
                    i.Ingredient_ID,
                    ri.Amount,
                    m.Name,
                    m.Measure_ID
                from Recipe_Ingredient ri
                left join Ingredient i on i.Ingredient_ID = ri.Ingredient_ID
                left join Measure m on m.Measure_ID = ri.Measure_ID
                where ri.Recipe_ID = :recipeID";
        
        return parent::runQueryParam($sql,":recipeID",$recipeID);
    }
}
?>
