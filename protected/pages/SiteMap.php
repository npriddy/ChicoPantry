<?php
/*
  Module: SiteMap.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: This module is the actual menu tabs above and this controls
 * what tab should be selected based on what the page is.
 */
class SiteMap extends TTemplateControl
{
	
	public function onPreRender($param)
        /*
         * Purpose: Built in function runs when the page is pre rendering.
         * Sets the active menu item using css class.
         * Parameters
         *      @param TPage
         * Returns: nothing
         * Side-effects: Recipe Grid is populated
         */
	{
		parent::onPreRender($param);

                //Gets the page name
		$page = explode('.',$this->Request->ServiceParameter);
		$active = null;
                //Grab the page name from the URL
		switch($page[count($page)-1])
		{
                    //Based on what page it is highlight the correct tab.
                    case 'RecipeHome':
                    case 'RecipeView':
                    case 'RecipeEdit':
                    case 'RecipeNew':
                            $active = $this->RecipeMenu;
                                    break;
                    case 'MyGroceryListHome':
                    case 'MyGroceryListGeneration':
                            $active = $this->MyGroceryListMenu;
                                    break;
                    case 'PantryHome':
                    case 'PantryRecipeList':
                            $active = $this->PantryMenu;
                                    break;
                    case 'MyAccountHome':
                            $active = $this->MyAccountMenu;
                                    break;
                    default:
                            $active = $this->HomeMenu;
                                    break;
		}
		
		//add 'active' string to place holder body.
		if(!is_null($active))
			$active->Controls[] = 'active';
	}
}

?>