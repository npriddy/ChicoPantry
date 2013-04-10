<?php
/*
  Module: PantryRecipeList.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Page that lists what recipes where generated from their pantry.
 */
class PantryRecipeList extends TPage {

    public function onLoad($param)
     /*
     * Purpose: Built in function runs when the page is loading.
     * Used to fill labels with information
     * Parameters
     *      @param TPage
     * Returns: nothing
     * Side-effects: Recipe Grid is populated
     */
    {
        parent::onLoad($param);
        //If this is the first time the page loaded
        if (!$this->IsPostBack) {
            if ($this->User->Name != "Guest") {
                $this->RecipeRebind();
           
            }
        }
    }
  
    public function RecipeRebind()
     /*
     * Purpose: Retrieves data from Pantry List and Set the grid to use that data
     * Parameters (None)
     * Returns: nothing
     * Side-effects: Grid Refreshed
     */
    {
            $data = RecipeSQL::GetRecipesFromPantry($this->User->getUserId());
            $this->RecipeGrid->DataSource=$data;
            $this->RecipeGrid->dataBind();
    }
}
?>
