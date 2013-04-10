<?php
/*
  Module: RecipeSQL.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Provides SQL and Utility Functions
 *         For Recipe Table
*/
class RecipeSQL Extends ChicoPantryDataModule
{
    public function GetRecipeList()
     /**
     * Purpose: Returns all recipes
     * Parameters: (none)
     * Returns: array of Recipe objects
     * Side-Effects: None
     */
    {
        $sql ="select
                r.RI_ID,
                r.R_name,
                if(IsNull(r.Cook_Time),0,r.Cook_Time) as 'Cook_Time',
                u.username,
                r.ImageUrl
                from Recipe r
                left join User u on u.User_ID = r.Author
                order by r.R_name";
        return parent::runQuery($sql);
    }

    public function GetRecipeSearch($sqlSearchText,$categoryID)
     /**
     * Purpose: Returns all Recipe Category Names
     * Parameters:
     *          @param $sqlSearchText = SQL search text
     *          @param $categoryID = Category ID to search for if its null search by all
     * Returns: array of Recipes data
     * Side-Effects: None
     */
    {
       $sqlSearchText = "%" . $sqlSearchText . "%";
       $sql = "SELECT  r.RI_ID,
                       r.R_name,
                       if(IsNull(r.Cook_Time),0,r.Cook_Time) as 'Cook_Time',
                       u.username,
                       r.ImageUrl
                       FROM Recipe r
                       left join User u on u.User_ID = r.Author
                       where r.R_Name like :searchText and
                            (:categoryID = 0  or  exists(Select * from Recipe_Category rc where rc.Recipe_ID = r.RI_ID and rc.R_Category_ID = :categoryID))
                       order by r.R_name";
     //Pass in array of parameters for searching
     $data = array(array('value'=>$sqlSearchText,'text'=>":searchText"),array('value'=>$categoryID,'text'=>":categoryID"));
     //Returns array of Recipe objects
     return parent::runQueryListOfParam($sql,$data);
    }

    public function GetRecipeSearchText($sqlSearchText)
     /**
     * Purpose: Returns all Recipe by search text
     * Parameters:
     *          @param $sqlSearchText = SQL search text
     * Returns: array of Recipes data
     * Side-Effects: None
     */
    {
        $sqlSearchText = "%" . $sqlSearchText . "%";
        $sql = "SELECT * FROM Recipe r
        where r.R_Name like :searchText";
        return parent::runQueryParam($sql,":searchText",$sqlSearchText);
    }

    public function GetRecipesFromPantry($User_ID)
     /**
     * Purpose: Returns all Recipes that have at least 1 ingredient that match pantry
     * Parameters:
     *          @param $User_ID = User ID to get pantry of
     * Returns: array of Recipes data
     * Side-Effects: None
     */
    {
          $sql="SELECT *
            FROM Recipe r
            WHERE
            EXISTS (

            SELECT *
            FROM Recipe_Ingredient ri
            WHERE ri.Recipe_ID = r.RI_ID
            AND EXISTS (

            SELECT *
            FROM Pantry p
            WHERE p.Ingredient_ID = ri.Ingredient_ID
            AND p.User_ID = :User_ID))";
      return parent::runQueryParam($sql, ":User_ID", $User_ID);
    }
}
?>
