<?php
/*
  Module: MyGroceryListGeneration.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Displays the list of ingredients based on what is in the users pantry
 */
class MyGroceryListGeneration extends TPage {

    public function onLoad($param)
     /*
     * Purpose: Built in function runs when the page is loading.
     * Used to fill labels with information
     * Parameters
     *      @param TPage
     * Returns: nothing
     * Side-effects: Grid is populated with Ingredient Rows
     */
    {
        parent::onLoad($param);
        //If this is the first time the page loaded
        if (!$this->IsPostBack) {
            //if the user is a not a guest and there is a supplied List ID then fill the grid
            if ($this->User->Name != "Guest" && $this->Request['List_ID'] != null)
            {
                //Bind the grid with ingredeitn data rows
                $this->IngredientRebind();
                $ListRecord = ListRecord::finder()->findByPk($this->Request['List_ID']);
                //Display the list name
                $this->lblListName->Text = $ListRecord->L_Name;
            }
            else
            {
                //If either of these are invalid then redirect the user to the home apge.
                $this->Response->redirect($this->Service->DefaultPageUrl);
            }
        }
    }

    public function IngredientRebind()
     /*
     * Purpose: Retrieve and set the data grid to the ingredient rows
     * Parameters (none)
     * Returns: nothing
     * Side-effects: Grid is populated with Ingredient Rows
     */
    {
            $data = Recipe_ListSQL::GenerateGroceryList($this->Request['List_ID']);
            $this->GroceryList->DataSource=$data;
            $this->GroceryList->dataBind();
    }
}
?>
