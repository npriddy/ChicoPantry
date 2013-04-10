<?php

/*
  Module: MyGroceryListHome.php
  Author Name(s): Ashley Prasad
  Date Created: 12/7/2010
  Purpose: Allows users to create Grocery Lists, delete whole Grocery Lists,
 *        manipulate Recipes between lists: copy/cut, or simply delete out
 *        recipes they no longer want in list. Generate allows users to see
 *        what needs to be on their shopping lists for selected Grocery Lists.
 *
 */

class MyGroceryListHome extends TPage
{
   // Necessary for figuring out what to display on RecipeMealListGrid
   public $selectedID = 0;

    private function GetSelectedID()
    /* Purpose: Gets the selected int from viewstate
     * Used to fill labels with information
     * Parameters
     *      @param int
     * Returns: int
     * Side-effects: none
    */
    {
       $selectedID = $this->getViewState("SelectedID");
       return $selectedID;
    }

    private function SetSelectedID($data)
    /* Purpose:Sets which id is selected in the viewstate
     * Used to fill labels with information
     * Parameters
     *      @param int
     * Returns: nothing
     * Side-effects: viewstate set
    */
    {
       $selectedID = $this->setViewState("SelectedID", $data);
    }

    public function onLoad($param)/*
     * Purpose: Built in function runs when the page is loading.
     * Used to fill labels with information
     * Parameters
     *      @param TPage
     * Returns: nothing
     * Side-effects: Labels are set to to data
     */{
        $User_ID = $this->User->getUserId();
        parent::onLoad($param);
        //If this is the first time the page loaded
        if (!$this->IsPostBack) {
            //Load Table of Users Lists if a user is signed in
            if ($this->User->Name != "Guest") {
                $result = ListSQL::GetAllMealListNamesList($User_ID);
                $data = ListSQL::GetUserMealList($User_ID);
                // Find the first List's ID to set selected ID
                // Used to display RecipeMealListGrid
                foreach( $data as $row)
                {
                    $selectedID = $row['List_ID'];
                    break;
                }
                $this->SetSelectedID($selectedID);
                // Fill Drop down list with current user's Lists
                $this->ddlUserRecipeList->DataSource = $result;
                $this->ddlUserRecipeList->dataBind();
                // Load table of selected List's Recipes
                $this->ListRebind($selectedID);
                // Used to display RecipeUserListGrid
                $this->UserListRebind($User_ID);
                // Shows User data above and turns off demo as user is logged in
                $this->pnlMyAccount->Visible = true;
                $this->pnlDemo->Visible = false;
            }
        }
    }

    public function getData($List_ID)  /*
    Module: getData
    Parameters:
     *  $List_ID -- Database record ID of list
    Author Name(s): Nate Priddy
    Date Created: 12/7/2010
    Purpose: Retrieve RecipeMealList from database
    *    @return array() of sql rows
    */{
        return Recipe_ListSQL::GetRecipeTable($List_ID);
    }
    public function ListRebind($List_ID)/*
    Module: ListRebind
    Parameters:
     *  $List_ID -- Database record ID of list
    Author Name(s): Ashley Prasad
    Date Created: 12/7/2010
    Purpose: Fills RecipeMealListGrid with recipes associated
     *      with List_ID parameter. Binds data so that when page
     *      finishes loading it will display with current
     *      association.
     */{
        $data = Recipe_ListSQL::GetRecipeTable($List_ID);
        $this->RecipeMealListGrid->DataSource = $data;
        $this->RecipeMealListGrid->dataBind();
    }

    public function UserListRebind($User_ID)/*
    Module: UserListRebind
    Parameters:
     *  $User_ID -- Database record ID of user
    Author Name(s): Ashley Prasad
    Date Created: 12/7/2010
    Purpose: Fills RecipeUserListGrid with lists associated
     *      with User_ID parameter. Binds data so that when page
     *      finishes loading it will display with current
     *      association.
     */{
        $data = ListSQL::GetUserMealList($User_ID);
        $this->RecipeUserListGrid->DataSource = $data;
        $this->RecipeUserListGrid->dataBind();
    }

    public function ddlRebind($User_ID)/*
    Module: ddlRebind
    Parameters:
     *  $User_ID -- Database record ID of user
    Author Name(s): Ashley Prasad
    Date Created: 12/7/2010
    Purpose: Fills ddlUserRecipeList with lists associated
     *      with User_ID parameter. Binds data so that when page
     *      finishes loading it will display with current
     *      association.
     */{
        $this->ddlUserRecipeList->DataSource = ListSQL::GetAllMealListNamesList($User_ID);
        $this->ddlUserRecipeList->dataBind();
    }

    public function btnAddList_Clicked($sender, $param)/*
    Module: btnAddList_Clicked
    Parameters:
     *  $sender -- Page that sent request
     * $param -- Tcontrol that sent request
    Author Name(s): Ashley Prasad
    Date Created: 12/7/2010
    Purpose: Adds a new List containing datea of user specified name, 
     *      User_ID, and "new" List_ID.
     */{
        // Retrieve User_ID from page
        $User_ID = $this->User->getUserId();

        // Create a new "ListRecord", establishes a "new" List_ID, sets
        // User_ID and L_Name (List Name) component of ListRecord
        $item = new ListRecord;
        $item->User_ID = $User_ID;
        $item->L_Name = $this->txtNewListName->Text;
        // Commit new ListRecord to database
        $item->save();
        // Update Drop Down List and RecipeUserListGrid so new List will
        // display
        self::ddlRebind($User_ID);
        self::UserListRebind($User_ID);
    }

    public function btnRemoveList_Clicked($sender, $param)/*
    Module: btnRemoveList_Clicked
    Parameters:
     *  $sender -- Page that sent request
     * $param -- Tcontrol that sent request
    Author Name(s): Ashley Prasad
    Date Created: 12/7/2010
    Purpose: Deletes all recipes associated with selected List then List
     */{
         if ($this->IsValid)
        {
        $User_ID = $this->User->getUserId();

        // obtains the datagrid item that contains the clicked delete button
        $item = $param->Item;
        // obtains the primary key corresponding to the datagrid item
        $List_ID = $this->RecipeUserListGrid->DataKeys[$item->ItemIndex];
        Recipe_ListRecord::finder()->deleteBy_List_ID($List_ID);
        ListRecord::finder()->deleteByPk($List_ID);
        // Used to get Top ID on RecipeUserMealList Grid
        $data = ListSQL::GetUserMealList($User_ID);
        // Find the first List's ID to set selected ID
        // Used to display RecipeMealListGrid
        foreach( $data as $row)
        {
            $selectedID = $row['List_ID'];
            break;
        }
        $this->SetSelectedID($selectedID);
        // Refreshes all page data
        self::ddlRebind($User_ID);
        self::UserListRebind($User_ID);
        self::ListRebind($selectedID);
        }
        
    }

    public function updateRecipeList($sender, $param)/*
    Module: updateRecipeList
    Parameters:
     *  $sender -- Page that sent request
     * $param -- Tcontrol that sent request
    Author Name(s): Ashley Prasad
    Date Created: 12/7/2010
    Purpose: Preforms the copy or cut command on RecipeMealList Grid
        * If COPY - takes recipe ID and simply adds it to List_ID selected
        *  by the drop down box
        * If CUT - Preform Copy then delete recipe from currently displayed
        *  List_ID
     */{
        if ($this->IsValid) {
            // obtains the datagrid item that contains the clicked select button
            $item = $param->Item;
            // obtains the primary key corresponding to the datagrid item
            $RL_ID = $this->RecipeMealListGrid->DataKeys[$item->ItemIndex];
            $rList = Recipe_ListRecord::finder()->findByPk($RL_ID);
            $Recipe_ID = $rList->Recipe_ID;
            // Get Recipe List from Selected Dropdown value
            $List_ID = $this->ddlUserRecipeList->SelectedValue;
            if (Recipe_ListRecord::finder()->findBy_List_ID_AND_Recipe_ID($List_ID, $Recipe_ID) === null) {

                // Add Recipe_ID to Selected Recipe_List
                $recipeList = new Recipe_ListRecord;
                $recipeList->List_ID = $List_ID;
                $recipeList->Recipe_ID = $Recipe_ID;
                $recipeList->save();
                // If Radio == Cut Delete from list
                if ($this->cutButton->Checked == true) {
                    self::deleteRecipefromListClicked($sender, $param);
                }
            } else {
              $this->lblAlreadyExists->Visible = true;
            }
        }
         self::UserListRebind($this->User->getUserId());
        $selected = self::GetSelectedID();
        self::ListRebind($selected);
    }

    public function validateList($sender,$param)/*
    Module: validateList
    Parameters:
     *  $sender -- Page that sent request
     * $param -- Tcontrol that sent request
    Author Name(s): Ashley Prasad
    Date Created: 12/7/2010
    Purpose: Checks to see if recipe already exists in List
    Returns:
        * TRUE == If Recipe does not exist
        * FALSE == If recipes does exist
     */{
         if(Recipe_ListRecord::finder()->findBy_List_ID_AND_Recipe_ID($this->ddlGroceryList->SelectedValue,$this->getRecordID()) === null)
                 $param->IsValid=true;
         else $param->IsValid = false;
    }

    public function deleteRecipefromListClicked($sender, $param)/*
    Module: deleteRecipefromListClicked
    Parameters:
     *  $sender -- Page that sent request
     * $param -- Tcontrol that sent request
    Author Name(s): Ashley Prasad
    Date Created: 12/7/2010
    Purpose: Removes recipe from List
     */{
         if ($this->IsValid)
        {
        // obtains the datagrid item that contains the clicked delete button
        $item = $param->Item;
        // obtains the primary key corresponding to the datagrid item
        $RL_ID = $this->RecipeMealListGrid->DataKeys[$item->ItemIndex];
        Recipe_ListRecord::finder()->deleteByPk($RL_ID);
        // Used to re-display RecipeMealListGrid
        $selected = self::GetSelectedID();
        self::ListRebind( $selected );
        }
    }

    public function editRecipeListClicked($sender, $param)/*
    Module: editRecipeListClicked
    Parameters:
     *  $sender -- Page that sent request
     * $param -- Tcontrol that sent request
    Author Name(s): Ashley Prasad
    Date Created: 12/7/2010
    Purpose: Allows user to preform an "edit" on List Name, changes row
     * where Edit was clicked to have "Update/Cancel" and Textbox in place
     * of the "Edit" and List name.
     */{
        // Changes from ItemTemplate to EditTemplate
        $this->RecipeUserListGrid->EditItemIndex = $param->Item->ItemIndex;
        self::UserListRebind($this->User->getUserId());
    }

    public function cancelRecListItem($sender, $param)/*
    Module: cancelRecListItem
    Parameters:
     *  $sender -- Page that sent request
     * $param -- Tcontrol that sent request
    Author Name(s): Ashley Prasad
    Date Created: 12/7/2010
    Purpose: Cancels edit command
     */{
        // Changes from EditTemplate to ItemTemplate w/o updating
        $this->RecipeUserListGrid->EditItemIndex = -1;
        self::ddlRebind($this->User->getUserId());
        self::UserListRebind($this->User->getUserId());
    }

    public function saveRecipeListItem($sender, $param)/*
    Module: saveRecipeListItem
    Parameters:
     *  $sender -- Page that sent request
     * $param -- Tcontrol that sent request
    Author Name(s): Ashley Prasad
    Date Created: 12/7/2010
    Purpose: Updates List Name from after users completes "edit"
     */{
        // Sets $item to row of RecipeUsterListGrid wehre edit was clicked
        $item = $param->Item;
        // Gets List_ID from current row
        $List_ID = $this->RecipeUserListGrid->DataKeys[$item->ItemIndex];
        // Gets ListRecord from database
        $List = ListRecord::finder()->findByPk($List_ID);
        if ($List instanceof ListRecord) {
            //Changes List Name to new List Name
            $List->L_Name = $item->L_NameColumn->txtL_Name->Text;
            // Commit change
            $List->save();
            // Resets EditTemplate to Item Template
            $this->RecipeUserListGrid->EditItemIndex = -1;
            // Refreshes grid and dropdown to display new List Name
            self::ddlRebind( $this->User->getUserId() );
            self::UserListRebind( $this->User->getUserId() );
        }
    }

   public function itemDataBound($sender, $param)/*
    Module: itemDataBound
    Parameters:
     *  $sender -- Page that sent request
     * $param -- Tcontrol that sent request
    Author Name(s): Nate Priddy
    Date Created: 12/7/2010
    Purpose: Sets clicked radio button to that of selectedID
    */{
        $item = $param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
            if($item->recipeListIdColumn->Text ==$this->GetSelectedID())
            {
                $param->Item->selectColumn->rb_Select->Checked = true;
            }
        }
   }

   public function radiobuttonClicked($sender,$param)/*
    Module: radiobuttonClicked
    Parameters:
     *  $sender -- Page that sent request
     * $param -- Tcontrol that sent request
    Author Name(s): Nate Priddy
    Date Created: 12/7/2010
    Purpose: Not in use!
        * Meant to be used when radio buttons rather than select button is
        * used to make adjusstments to lists
    */{
         // obtains the datagrid item that contains the clicked delete button
        $item = $param->Item;
        // obtains the primary key corresponding to the datagrid item
        $RL_ID = $this->RecipeUserListGrid->DataKeys[$item->ItemIndex];
        $this->SetSelectedID($RL_ID);
        $this->UserListRebind($this->User->getUserId());
        $this->ListRebind($RL_ID);
   }
   
   public function Generate_Clicked($sender,$param)/*
    Module: Generate_Clicked
    Parameters:
     *  $sender -- Page that sent request
     * $param -- Tcontrol that sent request
    Author Name(s): Nate Priddy
    Date Created: 12/7/2010
    Purpose: Redirects to page containing Shopping List
    */{
    $this->Response->redirect($this->Service->constructUrl('MyGroceryListGeneration',array('List_ID'=>$this->GetSelectedID())));
   }
}

?>
