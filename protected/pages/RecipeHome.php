<?php
/*
  Module: RecipeHome.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Used for displaying all recipes in the database and allows users to
 * search for recipes by category
 */
class RecipeHome extends TPage {


  
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
        //If this is the first time the page loaded
        if (!$this->IsPostBack) {
             if ($this->Request['message'] != null) {
                $this->lblMessage->Text = $this->Request['message'];
                $this->lblMessage->Visible = true;
            }
            //Set the grids Data source to the returned data set.
            $this->RecipeGrid->DataSource = $this->getData();
            //Binds the grid to the data set
            $this->RecipeGrid->dataBind();

           $result = R_CategorySQL::GetAllRecipeCategoryNamesList(true);
            $this->ddlRecipeCategory->DataSource = $result;
            $this->ddlRecipeCategory->dataBind();
        }
    }

   

    /**
     * Retrieve RecipeList from database
     * @return array() of sql rows;
     */
    public function getData() {
        return RecipeSQL::GetRecipeList();
    }

  

    public function Search_Clicked($sender,$param)
    {
        $value = $this->ddlRecipeCategory->SelectedItem->Value;
        $text =$this->txtName->Text;
        if( $text == "*" ) $text = "%%";
        $this->RecipeGrid->DataSource = RecipeSQL::GetRecipeSearch($text,$value);
        $this->RecipeGrid->dataBind();
    }

   public function itemDataBound($sender, $param)
   {
        $item = $param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {

                if($param->Item->imageUrlColumn->Text != '')
                    $param->Item->imageColumn->imgRecipe->ImageUrl = $param->Item->imageUrlColumn->Text;
                else $param->Item->imageColumn->imgRecipe->ImageUrl = "Images/NoImage.jpg";
            
        }
   }

}

?>
