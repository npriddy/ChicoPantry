<?php
/*
  Module: RecipeView.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Displays recipe information / allows users to comment on the recipes/
 * and print recipes.
 */
class RecipeView extends BasePage
{
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
        if(!$this->IsPostBack)
        {
            
            //Fill all the controls
            $this->loadPageControls();
        }
    }
 /**
     * Physically loads all the controls from the database
     * If ?RI_ID is set then try loading the object
     * Otherwise its a new object
     * @return none;
     */
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
        if($RecipeRecord instanceof RecipeRecord)
        {
            $this->hypEdit->NavigateUrl = "?page=RecipeEdit&RI_ID=" . $RecipeRecord->RI_ID;
            //tests the Title of the page to the recipe name
            $this->Title = $RecipeRecord->R_Name;
            //Fill in control text
            $this->lblRecipeName->Text = $RecipeRecord->R_Name;
            $this->lblPrepTime->Text = $RecipeRecord->Prep_Time . ' Minutes';// $Prep_Time;
            //If cooktime is null then replace with 0 minutes
            $this->lblCookTime->Text = ($RecipeRecord->Cook_Time == null ? '0' : $RecipeRecord->Cook_Time) . ' Minutes';// $Cook_Time;
            $this->litDirections->Text = $RecipeRecord->Directions;

            if($RecipeRecord->ImageUrl != null)
            $this->imgRecipe->ImageUrl = $RecipeRecord->ImageUrl;
            else $this->imgRecipe->ImageUrl = "images/NoImage.jpg";

            //If an author is set then retrieve their username
            if($RecipeRecord->Author != null)
                $this->lblAuthor->Text = UserRecord::finder()->findByPk($RecipeRecord->Author)->Username;
            else $this->lblAuthor->Text = "ChicoPantry.com";

            //Fill Category list comma seperated
            $this->lblRecipeCategories->Text = Recipe_CategorySQL::getCategoryStringForRecipe($RecipeRecord->RI_ID);
            //Grab all the comments for the recipe html generated.
            //Set the html to the literal so it renders as html

            $this->CommentsGrid->DataSource =  Recipe_CommentSQL::GetCommentsByRecipe($RecipeRecord->RI_ID);
            $this->CommentsGrid->dataBind();

            //Set the ingredient dataset
            $this->IngredientRecipeGrid->DataSource = Recipe_IngredientSQL::GetIngredientByRecipeList($this->getRecipeID());
            //Bind the Ingredient table
            $this->IngredientRecipeGrid->dataBind();

            //Fill meallist drop down
            $this->ddlGroceryList->DataSource =  ListSQL::GetAllMealListNamesList($this->User->getUserId());
            $this->ddlGroceryList->dataBind();
            //If there are no grocery list items then disable to add button for them.
            if(sizeof(ListSQL::GetAllMealListNamesList($this->User->getUserId())) <= 0)
                    $this->btnGroceryList->Enabled = false;
        }
    }

    protected function getRecipeRecord()
     /*
     * Purpose: Returns record from database
     * Parameters (none)
     * Returns: RecipeRecord
     * Side-effects: (none)
     */
    {
        $RecipeRecord = null;
        if($this->Request['RI_ID'] != null)
        {
            $RecipeRecord = RecipeRecord::finder()->findByPk($this->Request['RI_ID']);
        }

        return $RecipeRecord;
    }

    protected function getRecipeID()
       /*
     * Purpose: Returns ID from query string
     * Parameters (none)
     * Returns: int
     * Side-effects: (none)
     */
    {
          if($this->Request['RI_ID'] != null)
        {
            return $this->Request['RI_ID'];
        }
        else return 0;
    }

   
    public function GroceryList_Clicked($sender,$param)
      /*
     * Purpose: Adds new recipe list record
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  DataItem
     * Returns: nothing
     * Side-effects: none
     */
    {
        if($this->IsValid)
        {
            $newRecord = new Recipe_ListRecord;
            $newRecord->List_ID = $this->ddlGroceryList->SelectedValue;
            $newRecord->Recipe_ID = $this->getRecipeID();
            $newRecord->save();
        }
    }

    public function validateList($sender,$param)
     /*
     * Purpose: Checks to see if the recipe is not already in the currently
      * selected list
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Validparam
     * Returns: nothing
     * Side-effects: none
     */
    {
         if(Recipe_ListRecord::finder()->findBy_List_ID_AND_Recipe_ID($this->ddlGroceryList->SelectedValue,$this->getRecipeID()) === null)
                 $param->IsValid=true;
         else $param->IsValid = false;
    }

    public function Submit_Clicked($sender,$param)
    /*
     * Purpose: Inserts a new comment
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Button
     * Returns: nothing
     * Side-effects: none
     */
    {
        if($this->htmlComments->Text != '')
        {
            $newComment = new Recipe_CommentRecord;
            $newComment->User_ID = $this->User->getUserId();
            $newComment->Recipe_ID = $this->getRecipeID();
            $newComment->Comment = $this->htmlComments->Text;
            //insert new comment
            $newComment->save();
            $this->RebindComments();
        }
    }

    public function RebindComments()
      /*
     * Purpose: Retrieves data from Comments Table and Set the Comments grid to use that data
     * Parameters (None)
     * Returns: nothing
     * Side-effects: Grid Refreshed
     */
    {
         $this->CommentsGrid->DataSource =  Recipe_CommentSQL::GetCommentsByRecipe($this->getRecipeID());
         $this->CommentsGrid->dataBind();
    }

    public function deleteComment($sender,$param)
     /*
     * Purpose:Removes the comment from the database
     * Parameters
     *     @param TControl $sender Page
     *     @param  TEventParameter $param  Button
     * Returns: nothing
     * Side-effects: Grid Refreshed
     */
    {
         // obtains the datagrid item that contains the clicked delete button
        $item = $param->Item;
        // obtains the primary key corresponding to the datagrid item
        $RI_ID = $this->CommentsGrid->DataKeys[$item->ItemIndex];
        $RecipeComment = Recipe_CommentRecord::finder()->findByPk($RI_ID);
        if($RecipeComment instanceof Recipe_CommentRecord)
             Recipe_CommentRecord::finder()->deleteByPk($RI_ID);
        $this->RebindComments();
    }   
}

?>
