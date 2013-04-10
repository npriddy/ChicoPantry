<?php
/*
  Module: IngredientSQL.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Provides SQL and Utility Functions
 *         For Ingredient Table
*/
class IngredientSQL extends ChicoPantryDataModule
{
    public function GetIngredientList()
    /**
     * Purpose: Returns list of ingredients with names
     * Parameters: (none)
     * Returns: Array of row objects
     * Side-Effects: (none)
     */
    {
        $sql = "Select i.*,c.IC_Name FROM Ingredient i left join I_Category c on c.I_Category_ID = i.I_Category_ID";
        return parent::runQuery($sql);
    }

    public function GetAllIngredients()
    /**
    * Purpose: Returns list of ingredients ordered
    * Parameters: (none)
    * Returns: Array of row objects
    * Side-Effects: (none)
    */
    {
        $sql = "Select Ingredient_ID,Ingredient_Name,I_Category_ID FROM Ingredient order by Ingredient_Name asc";
        return parent::runQuery($sql);
    }

     public function GetAllIngredientsList()
    /**
    * Purpose: Converts Rows to Array
    * Parameters: (none)
    * Returns: array of ingredient , ingredient name
    * Side-Effects: (none)
    */
    {
        $result = self::GetAllIngredients();
        $data = array();
        foreach($result as $row)
        {
            $data[$row['Ingredient_ID']] = $row['Ingredient_Name'];
        }
        return $data;
    }
}
?>