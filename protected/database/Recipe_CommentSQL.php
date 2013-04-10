<?php
/*
  Module: Recipe_CommentSQL.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Provides SQL and Utility Functions
 *         For Recipe_Comment Table
*/
class Recipe_CommentSQL Extends ChicoPantryDataModule
{
    public function GetCommentsByRecipe($recipeID)
     /**
     * Purpose:  Returns a list of Comments based on recipeID
     * Parameters:
     *          @param $recipeID = get all Comments based on recipe ID
     * Returns: array of Comments data
     * Side-Effects: None
     */
    {
        $sql ="select
                      rc.RCO_ID,
                      rc.Comment,
                      u.Username
                from Recipe_Comment rc
                left join User u on u.User_ID = rc.User_ID
                where rc.Recipe_ID = :recipeID";
        return parent::runQueryParam($sql,":recipeID",$recipeID);
    }
}
?>
