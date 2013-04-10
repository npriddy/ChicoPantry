<?php
/*
  Module: Admin.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: Used for debuging process
 */
class Admin extends BasePage {
   
    public function onLoad($param)
     /*
     * Purpose: Built in function runs when the page is loading.
     * Used to load grids for display
     * Parameters
     *      @param TPage
     * Returns: nothing
     * Side-effects: Grids display with data
     */
    {
        parent::onLoad($param);
        if (!$this->IsPostBack)
        {
            //If the user is not an admin then redirect them to the home page.
            if(!$this->User->IsAdmin)
                     $this->Response->redirect($this->Service->DefaultPageUrl);

          //Rebind the tables with the data from Ingredients Table and User Table
          $this->IngredientGrid->DataSource = $this->getData();
          $this->IngredientGrid->dataBind();

          $this->UserGrid->DataSource = $this->getUserData();
          $this->UserGrid->dataBind();
        }
    }
 
    //////////////////////////////////////////////////////////////////////////////////////
    //
    //Ingredients Grid
    //
    //////////////////////////////////////////////////////////////////////////////////////

    //Used for storing Ingredient Table data for viewstate
    private $_IngredientData = null;
        
    protected function loadData()
     /*
     * Purpose: Returns the data for the grid either from view state or the database
     * Parameters None
     * Returns: array() of Ingredient Rows
     * Side-effects: None
     */
    {
        // We use viewstate keep track of data.
        // In real applications, data should come from database using an SQL SELECT statement.
        // In the following tabular data, field 'ISBN' is the primary key.
        // All update and delete operations should come with an 'id' value in order to go through.
        if(($this->_IngredientData=$this->getViewState('IngredientData',null))==null)
        {
            $this->IngredientGrid = getData();
        }
        $this->saveData();
    }

    public function getData()
     /*
     * Purpose: Gets All Ingredients from the database
     * Parameters (None)
     * Returns: Data rows of ingredients
     * Side-effects: none
     */
    {
        return IngredientSQL::GetIngredientList();
    }

    protected function saveData()
     /*
     * Purpose:Save the current ingredient data into viewstate
     * Parameters (None)
     * Returns: nothing
     * Side-effects: Viewstate now contains the data rows
     */
    {
       $this->setViewState('IngredientData',$this->$_IngredientData);
    }

    //////////////////////////////////////////////////////////////////////////////////////
    //
    //User Grid
    //
    //////////////////////////////////////////////////////////////////////////////////////

     //Used for storing User Table data for viewstate
    private $_UserData = null;

    public function getUserData()
     /*
     * Purpose: Returns all Users from the database
     * Parameters (None)
     * Returns: array of data rows from user table
     * Side-effects: none
     */
    {
        //Makes connection to the database
        $resultsList = array();
        $connection=new TDbConnection($this->getApplication()->Parameters['DSN'],$this->getApplication()->Parameters['dbUser'],$this->getApplication()->Parameters['dbPass']);
        $connection->Active = true;
        //Creates sql command
        $command= $connection->createCommand("Select * FROM User");
        $reader= $command->query();
        $i = 0;
        foreach($reader as $row)
        {
            $resultsList[$i] = $row;
            $i++;
        }
        //Returns the actual rows
        return $resultsList;
    } 

    protected function saveUserData()
     /*
     * Purpose: Saves the user data into view state
     * Parameters (None)
     * Returns: nothing
     * Side-effects: Viewstate now contains user data
     */
    {
       $this->setViewState('UserData',$this->$_UserData);
    }

     protected function loadUserData()
     /*
     * Purpose: Returns the user data either from view state or the database
     * Parameters (None)
     * Returns: Data Rows of users
     * Side-effects: Grids display with data
     */
    {
        // We use viewstate keep track of data.
        // In real applications, data should come from database using an SQL SELECT statement.
        // In the following tabular data, field 'ISBN' is the primary key.
        // All update and delete operations should come with an 'id' value in order to go through.
        if(($this->_UserData=$this->getViewState('UserData',null))==null)
        {
            $this->_UserData = getUserData();
        }
        $this->saveUserData();
    }

   public function itemCreated($sender, $param)
    /*
     * Purpose: When the gird rows are being created if the userRole = 1
     * then display admin if not display user
     * Used to load grids for display
     * Parameters
     *      @param $sender : Grid
     *      @param $param  : DataRowItem
     * Returns: nothing
     * Side-effects: Grids display with data
     */
  {
    	$item=$param->Item;
    	 if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='EditItem')
        {
             //If the row with the column Role has text of 1 then display admin
            if(strpos($item->Role->Text,'1') !== false)
            {
                $item->Role->Text = "Admin";
            }
            else
            {
                $item->Role->Text = "User";
            }
    	}
     
    }
}
?>