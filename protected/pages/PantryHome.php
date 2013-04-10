<?php
/*
  Module: PantryHome.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Allows users to enter ingredients that are in their pantry
 *         and then generate a list of recipes that use those ingredients.
 */
class PantryHome extends TPage {

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
            if ($this->User->Name != "Guest") {
                //Binds the grid
                $this->PantryRebind();
                //Fills in the drop down list with Ingredient Rows
                $this->ddlRecipeIngredientList->DataSource = IngredientSQL::GetAllIngredientsList();
                $this->ddlRecipeIngredientList->dataBind();
                $this->ddlMeasure->DataSource = MeasureSQL::GetAllMeasureList();
                $this->ddlMeasure->dataBind();
                //If the user is not a guest then show the pantry panel
                $this->pnlMyAccount->Visible = true;
                $this->pnlDemo->Visible = false;
            }
        }
    }
    public function deleteIngredientButtonClicked($sender,$param)
     /*
     * Purpose: deletes the ingredient based on the datakey
     * Parameters
     *     @param TControl $sender Grid
     *     @param  TEventParameter $param  RowDataItem
     * Returns: nothing
     * Side-effects: Ingredient Removed and Grid Refreshed
     */
    {
        // obtains the datagrid item that contains the clicked delete button
        $item = $param->Item;
        // obtains the primary key corresponding to the datagrid item
        $Pantry_ID = $this->PantryGrid->DataKeys[$item->ItemIndex];
        PantryRecord::finder()->deleteByPk($Pantry_ID);
        self::PantryRebind();
    }

    public function btnAddIngredient_Clicked($sender, $param)
     /*
     * Purpose: Adds the ingredient to the pantry
     * Parameters
     *     @param TControl $sender Grid
     *     @param  TEventParameter $param  RowDataItem
     * Returns: nothing
     * Side-effects: Ingredient Added and Grid Refreshed
     */
    {
        // Check to see if Ingredient is already in pantry
        if (PantryRecord::finder()->findBy_User_ID_AND_Ingredient_ID($this->User->getUserID() , $this->ddlRecipeIngredientList->SelectedValue) === null) {
             $item = new PantryRecord;
             $item->User_ID = $this->User->getUserId();
             $item->Ingredient_ID = $this->ddlRecipeIngredientList->SelectedValue;
             $item->Measure_ID = $this->ddlMeasure->SelectedValue;
             $item->Amount = $this->txtAmount->Text;
             //Insert row into database
             $item->save();
             //Refresh grid
             $this->PantryRebind();
        }
        else {
            // Set warning to visible
              $this->lblAlreadyExists->Visible = true;
        }
    }

    public function PantryRebind()
     /*
     * Purpose: Retrieves data from Pantry List and Set the grid to use that data
     * Parameters (None)
     * Returns: nothing
     * Side-effects: Grid Refreshed
     */
    {
        //Gets the pantry by the currently logged in user
        $data = PantrySQL::GetPantryIngredientList($this->User->getUserId());
        $this->PantryGrid->DataSource=$data;
        $this->PantryGrid->dataBind();
    }

    public function generateList_Clicked($sender, $param)
    /*
     * Purpose: Redirects the user to the pantry generation page
     * Parameters
     *     @param TControl $sender Button
     *     @param  TEventParameter $param  Button Text
     * Returns: nothing
     * Side-effects: Page redirected
     */
    {
        $this->Response->redirect($this->Service->constructUrl('PantryRecipeList'));
    }
}
?>
