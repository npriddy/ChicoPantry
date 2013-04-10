<?php

/*
  Module: RecipeEdit.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Lets admins edit all the data about the recipe. (Ingredients, category,
 *  description and title)
 */
class RecipeEdit extends BasePage {

     ///////////////////////////////////////////////////////////////////////
    //                     Variables
    ///////////////////////////////////////////////////////////////////////

    public function onLoad($param)
     /*
     * Purpose: Built in function runs when the page is loading.
     * Used to fill labels with information
     * Parameters
     *      @param TPage
     * Returns: nothing
     * Side-effects: If user is not an admin then redirect to home
     */
    {
        parent::onLoad($param);
        //If this is the first time the page has loaded
        if (!$this->IsPostBack) {
            if(!$this->User->IsAdmin)
                $this->Response->redirect($this->Service->DefaultPageUrl);
			
            //Fill all the controls
         $this->loadPageControls();
        }
     
    }

    protected function loadPageControls()
     /*
     * Purpose: Physically loads all the controls from the database
     * If ?RI_ID is set then try loading the object
     * Otherwise its a new object
     * Parameters (none)
     * Returns: nothing
     * Side-effects: Page controls loaded with values
     */
    {
        //Try to retrieve the physical record
        $RecipeRecord = $this->getRecipeRecord();
        //if the record was found fill the controls
        if ($RecipeRecord instanceof RecipeRecord) {

            $Author = UserRecord::finder()->FindByPk($RecipeRecord->Author);
            if($Author instanceof UserRecord)
                $this->lblAuthor->Text = $Author->Username;

            $this->RecipeCategoryGrid->DataSource = Recipe_CategorySQL::GetRecipeCategoryNameList($RecipeRecord->RI_ID);
            $this->RecipeCategoryGrid->dataBind();
            //Recipe Category Drop Down List
            $this->ddlRecipeCategoryList->DataSource = R_CategorySQL::GetAllRecipeCategoryNamesList();
            $this->ddlRecipeCategoryList->dataBind();

            $this->txtImageUrl->Text = $RecipeRecord->ImageUrl;

            //tests the Title of the page to the recipe name
            $this->Title = $RecipeRecord->R_Name;
            //Fill in control text
            $this->txtRecipeName->Text = $RecipeRecord->R_Name;
            $this->txtPrepTime->Text = $RecipeRecord->Prep_Time; // $Prep_Time;
            //If cooktime is null then replace with 0 minutes
            $this->txtCookTime->Text = ($RecipeRecord->Cook_Time == null ? '0' : $RecipeRecord->Cook_Time); // $Cook_Time;
            $this->htmlDirections->Text = $RecipeRecord->Directions;

            $this->ddlRecipeIngredientList->DataSource = IngredientSQL::GetAllIngredientsList();
            $this->ddlRecipeIngredientList->dataBind();

            $this->ddlMeasure->DataSource = MeasureSQL::GetAllMeasureList();
            $this->ddlMeasure->dataBind();

            $this->dataBindRecipeIngredient();
        }
    }

     public function getRecordID()
     /*
     * Purpose: Returns record ID from query string
     * Parameters (none)
     * Returns: nothing
     * Side-effects: (none)
     */
    {
        if ($this->Request['RI_ID'] != null) {
           return $this->Request['RI_ID'];
        }
        else return 0;
    }

    protected function getRecipeRecord()
      /*
     * Purpose: Returns the physical record if there is an ID for it in the query string
     * and if it exists otherwise null
     * Parameters (none)
     * Returns: nothing
     * Side-effects: Page controls loaded with values
    */
    {
        $RecipeRecord = null;
        if ($this->Request['RI_ID'] != null) {
            $RecipeRecord = RecipeRecord::finder()->findByPk($this->getRecordID());
        }
        return $RecipeRecord;
    }

    ///////////////////////////////////////////////////////////////////////
    //                      RECIPE INGREDIENT
    ///////////////////////////////////////////////////////////////////////

    public function dataBindRecipeIngredient()
    /*
     * Purpose:Retrive and set data from Ingredient table
     * Parameters (none)
     * Returns: nothing
     * Side-effects: Grid filled with data rows
    */     {
        $this->IngredientRecipeGrid->DataSource = Recipe_IngredientSQL::GetIngredientByRecipeList($this->getRecordID());
        $this->IngredientRecipeGrid->dataBind();
    }

     
    public function editRecIngredientItem($sender, $param)
     /*
     * Purpose: Sets the selected row into edit more
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  DataItem
     * Returns: nothing
     * Side-effects: none
     */
      {
        $this->IngredientRecipeGrid->EditItemIndex = $param->Item->ItemIndex;
        $this->dataBindRecipeIngredient();
    }

    public function cancelRecIngredientItem($sender, $param)
     /*
     * Purpose: Resets the edit index when cancel has been selected
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Validator control
     * Returns: nothing
     * Side-effects: none
     */{
        $this->IngredientRecipeGrid->EditItemIndex = -1;
        $this->dataBindRecipeIngredient();
        //$this->bindData();
    }

    public function itemRecIngredientCreated($sender,$param)
     /*
     * Purpose: When Recipe Ingredient row is edited then bind the template controls
      * Such as the ingredient dropdownlist and the measure list drop down
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Validator control
     * Returns: nothing
     * Side-effects: none
     */
    {
        $item = $param->Item;
        if($item->ItemType == 'EditItem')
        {
            $item->ingredientNameColumnDDL->ddlIngredientName->DataSource = IngredientSQL::GetAllIngredientsList();
            $item->ingredientNameColumnDDL->ddlIngredientName->dataBind();
            $item->ingredientNameColumnDDL->ddlIngredientName->SelectedValue = $item->DataItem['Ingredient_ID'];

            $item->measureColumnDDL->ddlMeasure->DataSource = MeasureSQL::GetAllMeasureList();
            $item->measureColumnDDL->ddlMeasure->dataBind();
            $item->measureColumnDDL->ddlMeasure->SelectedValue = $item->DataItem['Measure_ID'];
        }
    }

    public function btnAddIngredient_Clicked($sender, $param)
    /*
     * Purpose: Inserts the ingredient into the databse rebinds grid
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Validator control
     * Returns: nothing
     * Side-effects: Grid rebound
     */
    {
        $item = new Recipe_IngredientRecord;
        $item->Recipe_ID = $this->Request['RI_ID'];
        $item->Ingredient_ID = $this->ddlRecipeIngredientList->SelectedValue;
        $item->Measure_ID = $this->ddlMeasure->SelectedValue;
        $item->Amount = $this->txtAmount->Text;
        //Insert Recipe_Ingredient record
        $item->save();
        self::dataBindRecipeIngredient();
    }

    public function deleteRecIngredientButtonClicked($sender,$param)
    /*
     * Purpose: Delete the selected record from Ingredeint Recipe table and rebind
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Validator control
     * Returns: nothing
     * Side-effects: Selected Item removed
     */
    {
         // obtains the datagrid item that contains the clicked delete button
        $item = $param->Item;
        // obtains the primary key corresponding to the datagrid item
        $RI_ID = $this->IngredientRecipeGrid->DataKeys[$item->ItemIndex];
        //Find by primary key
        $Recipe = Recipe_IngredientRecord::finder()->findByPk($RI_ID);
        //If Recipe Ingredient record is not null
        if($Recipe instanceof Recipe_IngredientRecord)
            //Delete by primary key
             Recipe_IngredientRecord::finder()->deleteByPk($RI_ID);
        self::dataBindRecipeIngredient();
    }

    public function saveRecipeIngredientItem($sender,$param)
     /*
     * Purpose: Updates the selected record to the new values
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Validator control
     * Returns: nothing
     * Side-effects: grid refreshed
     */
    {
        //Find the ingredient record data item
        $item=$param->Item;
        $RI_ID=$this->IngredientRecipeGrid->DataKeys[$item->ItemIndex];
        $RecipeIngredient = Recipe_IngredientRecord::finder()->findByPk($RI_ID);
        //If it exists then update
        if($RecipeIngredient instanceof Recipe_IngredientRecord)
        {
            $RecipeIngredient->Amount = $item->AmountColumnDDL->txtAmount->Text;
            $RecipeIngredient->Measure_ID = $item->measureColumnDDL->ddlMeasure->SelectedValue;
            $RecipeIngredient->Ingredient_ID = $item->ingredientNameColumnDDL->ddlIngredientName->SelectedValue;
            //save to database
            $RecipeIngredient->save();
            $this->IngredientRecipeGrid->EditItemIndex=-1;
            //Re bind
            self::dataBindRecipeIngredient();
        }
    }

    ///////////////////////////////////////////////////////////////////////
    //                      RECIPE CATEGORY
    ////////////////////////////////////////////////////////////////////////

    
    public function rebindRecipeCategory() 
     /*
     * Purpose: Retrieves data from Recipe Category List and Set the grid to use that data
     * Parameters (None)
     * Returns: nothing
     * Side-effects: Grid Refreshed
     */
    {
        //Rebind The Recipe Category Grid
        $this->RecipeCategoryGrid->DataSource = Recipe_CategorySQL::GetRecipeCategoryNameList($this->Request['RI_ID']);
        $this->RecipeCategoryGrid->dataBind();
    }

    public function deleteButtonClicked($sender, $param)
     /*
     * Purpose: Deletes the selected row by the data key
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Validator control
     * Returns: nothing
     * Side-effects: Recipe_Category rebound
     */{
        // obtains the datagrid item that contains the clicked delete button
        $item = $param->Item;
        // obtains the primary key corresponding to the datagrid item
        $RC_ID = $this->RecipeCategoryGrid->DataKeys[$item->ItemIndex];
        //find by ID
        $RecipeCat = Recipe_CategoryRecord::finder()->findByPk($RC_ID);
        //if exists
        if($RecipeCat instanceof Recipe_CategoryRecord)
        {
            //delete by primary key
            Recipe_CategoryRecord::finder()->deleteByPk($RC_ID);
        }
        //rebind

            $this->dataBindRecipeIngredient();
            $this->rebindRecipeCategory();
    }

    public function btnAddRecipeCategory_Clicked($sender, $param)
     /*
     * Purpose: Inserts recipe category into database refresh grid
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Validator control
     * Returns: nothing
     * Side-effects: Grid refreshed
     */{
        //See if the record currently exists to link Recipe and Recipe Cateogry if not than add one
        if (Recipe_CategoryRecord::finder()->findBy_Recipe_ID_AND_R_Category_ID(self::getRecordID(), $this->ddlRecipeCategoryList->SelectedValue) == null) {
            //Add New Item
            $item = new Recipe_CategoryRecord;
            $item->R_Category_ID = $this->ddlRecipeCategoryList->SelectedValue;
            $item->Recipe_ID = $this->Request['RI_ID'];
            $item->save();
   

        }

                 //Rebinds grid
            $this->dataBindRecipeIngredient();
            $this->rebindRecipeCategory();
    }

    ///////////////////////////////////
    //              Save
    ///////////////////////////////////
    public function btnSave_Clicked($sender,$param)
     /*
     * Purpose: Update the recipe record with the new information
     * Parameters
     *     @param TControl $sender Button
     *     @param  TEventParameter $param  Button Text
     * Returns: nothing
     * Side-effects: Page redirected to RecipeView
     */
    {
        if($this->IsValid)
        {
            $RecipeRecord = RecipeRecord::finder()->findByPk($this->Request['RI_ID']);
            //If exists
             if ($RecipeRecord instanceof RecipeRecord) {
                 //Update the changes
                 $RecipeRecord->R_Name = $this->txtRecipeName->Text;

                 if($this->txtCookTime->Text != '')
                    $RecipeRecord->Cook_Time =  $this->txtCookTime->Text;
                 else $RecipeRecord->Cook_Time = null;

                 if($this->txtPrepTime->Text != '')
                     $RecipeRecord->Prep_Time = $this->txtPrepTime->Text;
                 else $RecipeRecord->Prep_Time = null;

                 if($this->txtImageUrl != '')
                    $RecipeRecord->ImageUrl = $this->txtImageUrl->Text;
                 else $RecipeRecord->ImageUrl = null;
                 
                 $RecipeRecord->Directions =  $this->htmlDirections->Text;
                 //save data
                 $RecipeRecord->save();
                 //Redirect page to recipe view with the recipes ID
                 $this->Response->redirect($this->Service->constructUrl("RecipeView", array('RI_ID' => $RecipeRecord->RI_ID)));
             }
        }
    }

   public function validateRecipeName($sender,$param)
     /*
     * Purpose: Checks to see if the recipe name already exists
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Validator control
     * Returns: nothing
     * Side-effects: if it is not valid then display error message
     */
    {
       //Search recipe table for recipe name
       $recipe = RecipeRecord::finder()->findBy_R_Name($this->txtRecipeName->Text);
       //if it doesn't exist or the found recipe matches this one then the name is valid
        if($recipe === null || $recipe->RI_ID == $this->getRecordID())
                $param->IsValid = true;
        else $param->IsValid = false;
    }



}

?>
