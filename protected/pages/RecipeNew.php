<?php

/*
  Module: RecipeNew.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Allows admin to create new recipes and enter all recipe data.
 */

class RecipeNew extends BasePage {

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
     */ {
        parent::onLoad($param);
        //If this is the first time the page has loaded
        if (!$this->IsPostBack) {
            if (!$this->User->IsAdmin)
                $this->Response->redirect($this->Service->DefaultPageUrl);
            $this->loadPageControls();
            //Fill all the controls
            //$this->loadPageControls();
        }
        else {
            $this->BindRecipeIngredient();
            $this->rebindRecipeCategory();
        }
    }

    public function validateRecipeName($sender, $param)
    /*
     * Purpose: Checks to see if the recipe name already exists
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Validator control
     * Returns: nothing
     * Side-effects: if it is not valid then display error message
     */
    {
        if (RecipeRecord::finder()->findBy_R_Name($this->txtRecipeName->Text) === null)
            $param->IsValid = true;
        else
            $param->IsValid = false;
    }

    public function btnCreate_Clicked($sender, $param)
    /*
     * Purpose: Inserts a new Recipe requires 1 ingredient and category
     * Redirects user
     * Parameters
     *     @param TControl $sender Button
     *     @param  TEventParameter $param  Button Text
     * Returns: nothing
     * Side-effects: Page redirected to RecipeView
     */
    {
        if ($this->IsValid) {
            //If if there are more than 0 ingredeints
            if (sizeof($this->GetIngredientData()) <= 0) {
                $this->lblWarning->Visible = true;
                 //If if there are more than 0 Recipe Categories
            } else if (sizeof($this->GetCategoryData()) <= 0) {
                //Display Warning
                $this->lblWarningCategory->Visible = true;
            } else {
                //Save And Create
                $finder = RecipeRecord::finder();
                $finder->DbConnection->Active = true; //open if necessary
                //Create sql transaction
                $transactionRecipe = $finder->DbConnection->beginTransaction();
                try {
                    //new recipe
                    $recipe = new RecipeRecord;
                    $recipe->R_Name = $this->txtRecipeName->Text;
                    if ($this->txtPrepTime->Text != '')
                        $recipe->Prep_Time = $this->txtPrepTime->Text;
                    if ($this->txtCookTime->Text != '')
                        $recipe->Cook_Time = $this->txtCookTime->Text;

                    $recipe->Author = $this->ddlAuthor->SelectedValue;

                    $recipe->Directions = $this->htmlDirections->Text;
                    $recipe->Current_Rating = 0;
                    $recipe->Rate_Count = 0;
                    $recipe->Is_Approved = true;

                    if ($this->txtImageUrl != '')
                        $recipe->ImageUrl = $this->txtImageUrl->Text;
                    else
                        $recipe->ImageUrl = null;
                    //Insert record into database
                    $recipe->save();

                    //Convert Ingredient Data array into Recipe_Ingredient records
                    //insert them into the database
                    $IngredientArray = $this->GetIngredientData();
                    foreach ($IngredientArray as $item) {
                        $rItem = new Recipe_IngredientRecord;
                        $rItem->Recipe_ID = $recipe->RI_ID;
                        $rItem->Ingredient_ID = $item['Ingredient_ID'];
                        $rItem->Amount = $item['Amount'];
                        $rItem->Measure_ID = $item["Measure_ID"];
                        //Insert into database
                        $rItem->save();
                    }

                    //Convert recipe category data array and place in databsae
                    $recipeCategoryArray = $this->GetCategoryData();
                    foreach ($recipeCategoryArray as $item) {
                        $rcItem = new Recipe_CategoryRecord;
                        $rcItem->Recipe_ID = $recipe->RI_ID;
                        $rcItem->R_Category_ID = $item['R_Category_ID'];
                        //Insert into database
                        $rcItem->save();
                    }
                    //Commit changes to the database
                    $transactionRecipe->commit();
                    //If this works than redirect user to the newly created recipe
                    $this->Response->redirect($this->Service->constructUrl("RecipeView", array('RI_ID' => $recipe->RI_ID)));
                } catch (Exception $e) {
                    // an exception is raised if a query fails
                    $transactionRecipe->rollBack();
                    Utilities::InsertLogEntry("New Recipe Error: ", $e->getMessage());
                    $this->Response->redirect($this->Service->constructUrl("RecipeHome", array('messge' => "error")));
                }
            }
        }
    }

    protected function loadPageControls()
      /*
     * Physically loads all the controls from the database
     * If ?RI_ID is set then try loading the object
     * Otherwise its a new object
     * Parameters (none)
     * Returns: nothing
     * Side-effects: Page controls loaded with values
     */
    {
        //Author Drop Down
        $this->ddlAuthor->DataSource = UserSQL::GetAllAuthorList();
        $this->ddlAuthor->dataBind();

        $this->ddlAuthor->SelectedValue = $this->User->getUserId();

        //Recipe Category Drop Down List
        $this->ddlRecipeCategoryList->DataSource = R_CategorySQL::GetAllRecipeCategoryNamesList();
        $this->ddlRecipeCategoryList->dataBind();

        //Fill in Ingredients list
        $this->ddlRecipeIngredientList->DataSource = IngredientSQL::GetAllIngredientsList();
        $this->ddlRecipeIngredientList->dataBind();

        $this->ddlMeasure->DataSource = MeasureSQL::GetAllMeasureList();
        $this->ddlMeasure->dataBind();

        $this->BindRecipeIngredient();
    }

    ///////////////////////////////////////////////////////////////////////
    //                     Recipe Ingredient
    ///////////////////////////////////////////////////////////////////////

    //Used for storing Ingredient data in viewstate
    private $viewStateIngredient = null;

    private function GetIngredientData()
    /*
     * Purpose: returns the data in viewstate for Ingredient Data
     * and if it exists otherwise null
     * Parameters (none)
     * Returns: data array of ingredient data
     * Side-effects: none
    */
    {
        $viewStateIngredient = $this->getViewState("IngredientData");
        return $viewStateIngredient;
    }

    private function SetIngredientData($data)
    /*
     * Purpose: sets the the supplied data into viewstate
     * and if it exists otherwise null
     * Parameters
     *     @param  array of ingredients $data
     * Returns: data array of ingredient data
     * Side-effects: Viewstate set
    */
    {
        $viewStateIngredient = $this->setViewState("IngredientData", $data);
    }

    public function itemRecIngredientCreated($sender, $param)
     /*
     * Purpose: When user clicks edit this function is fired
      * It fills in the edit template controls for use and fills them
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Grid
     * Returns: nothing
     * Side-effects: Edit controls set
     */
                {
        $item = $param->Item;
        //If this is the edit row
        if ($item->ItemType == 'EditItem') {
            //Fill drop down with all ingredients
            $item->ingredientNameColumnDDL->ddlIngredientName->DataSource = IngredientSQL::GetAllIngredientsList();
            $item->ingredientNameColumnDDL->ddlIngredientName->dataBind();
            $item->ingredientNameColumnDDL->ddlIngredientName->SelectedValue = $item->DataItem['Ingredient_ID'];
            //Fill all dropdowns with Measures from database
            $item->measureColumnDDL->ddlMeasure->DataSource = MeasureSQL::GetAllMeasureList();
            $item->measureColumnDDL->ddlMeasure->dataBind();
            $item->measureColumnDDL->ddlMeasure->SelectedValue = $item->DataItem['Measure_ID'];
        }
    }

    public function btnAddIngredient_Clicked($sender, $param)
     /*
     * Purpose: Inserts a new ingredient into the view state data array
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Grid
     * Returns: nothing
     * Side-effects: Viewstate dataitem updated
     */
    {
        //Get the current array of data
        $data = $this->GetIngredientData();
        //makes sure that there is an ingredient selected
        if ($data[$this->ddlRecipeIngredientList->SelectedItem->Text] == null) {
            //Insert new Ingredient array item
            $data[$this->ddlRecipeIngredientList->SelectedItem->Text] = array('RI_ID' => '0',
                'Ingredient_Name' => $this->ddlRecipeIngredientList->SelectedItem->Text,
                'Ingredient_ID' => $this->ddlRecipeIngredientList->SelectedValue,
                'Amount' => $this->txtAmount->Text,
                'Name' => $this->ddlMeasure->SelectedItem->Text,
                'Measure_ID' => $this->ddlMeasure->SelectedValue);
            //Store this back in the database
            $this->SetIngredientData($data);
            //Rebind
            $this->BindRecipeIngredient();
        }
    }

    public function BindRecipeIngredient()
    /*
     * Purpose: Binds the ingredient data to the ingredient grid
     * Parameters (None)
     * Returns: nothing
     * Side-effects: Grid Refreshed
     */
    {
        $this->IngredientRecipeGrid->DataSource = $this->GetIngredientData();
        $this->IngredientRecipeGrid->dataBind();
    }

    public function deleteRecIngredientButtonClicked($sender, $param)
     /*
     * Purpose: Deletes the selected row by the data key
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Grid control
     * Returns: nothing
     * Side-effects: Recipe_Category rebound
     */
    {
        // obtains the datagrid item that contains the clicked delete button
        $item = $param->Item;
        // obtains the primary key corresponding to the datagrid item
        $RI_ID = $this->IngredientRecipeGrid->DataKeys[$item->ItemIndex];
        if ($RI_ID != null) {
            $data = $this->GetIngredientData();
            unset($data[$RI_ID]);
            $this->SetIngredientData($data);
        }
        $this->BindRecipeIngredient();
    }

    ///////////////////////////////////////////////////////////////////////
    //                     Recipe Category
    ///////////////////////////////////////////////////////////////////////

    //Used for storing Recipe Category data in viewstate
    private $viewStateCategory = null;

    private function GetCategoryData()
    /*
     * Purpose: returns the data in viewstate for Category Data
     * and if it exists otherwise null
     * Parameters (none)
     * Returns: data array of Recipe Category data
     * Side-effects: none
    */
    {
        $viewStateCategory = $this->getViewState("CategoryData");
        return $viewStateCategory;
    }

    private function SetCategoryData($data)
    /*
     * Purpose: returns the data in viewstate for Recipe Category Data
     * and if it exists otherwise null
     * Parameters (none)
     * Returns: data array of Recipe Ingredient data
     * Side-effects: none
    */
    {
        $viewStateCategory = $this->setViewState("CategoryData", $data);
    }

  
    public function rebindRecipeCategory()
     /*
     * Purpose: Binds the Recipe Ingredient data to the Recipe grid
     * Parameters (None)
     * Returns: nothing
     * Side-effects: Grid Refreshed
     */
    {

        //Rebind The Recipe Category Grid
        $this->RecipeCategoryGrid->DataSource = $this->GetCategoryData();
        $this->RecipeCategoryGrid->dataBind();
    }

    /**
     * Page cycle onLoad built in function
     * @param object param
     * @return none;
     */
    public function btnAddRecipeCategory_Clicked($sender, $param)
    /*
     * Purpose: Inserts recipe category into database refresh grid
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Grid control
     * Returns: nothing
     * Side-effects: Grid refreshed
     */    
    {
        //Gets data
        $data = $this->GetCategoryData();
        //Makes sure null is not selected
        if ($data[$this->ddlRecipeCategoryList->SelectedItem->Text] == null) {
            //Inserts new Recipe Category Into grid
            $data[$this->ddlRecipeCategoryList->SelectedItem->Text] = array('RC_ID' => '0',
                'R_Category_ID' => $this->ddlRecipeCategoryList->SelectedValue,
                'RC_Name' => $this->ddlRecipeCategoryList->SelectedItem->Text);
            //Updates viewstate
            $this->SetCategoryData($data);
            //refresh grid
            $this->rebindRecipeCategory();
        }
    }

    public function deleteButtonClicked($sender, $param)
    /*
     * Purpose: Delete the selected record from Recipe Category table and rebind
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
        $RI_ID = $this->RecipeCategoryGrid->DataKeys[$item->ItemIndex];
        if ($RI_ID != null) {
            $data = $this->GetCategoryData();
            //Remove the selected record
            unset($data[$RI_ID]);
            $this->SetCategoryData($data);
        }
        self::rebindRecipeCategory();
    }

}

?>
