<!--
  Module: SiteMap.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: This module is the actual menu tabs above and this controls
 * what tab should be selected based on what the page is.
 -->
<com:TPanel CssClass="sitemap" Visible="true">
<ul class="level1">
	<li class="<com:TPlaceHolder ID="HomeMenu" />">
		<a class="menuitem" href="?page=Home">
		<span>Home</span></a>
	</li>
	<li class="<com:TPlaceHolder ID="RecipeMenu" />">
		<a class="menuitem" href="?page=RecipeHome">
		<span>Recipes</span></a>
	</li>
        <li class="<com:TPlaceHolder ID="MyGroceryListMenu" />">
		<a class="menuitem" href="?page=MyGroceryListHome">
		<span>My Grocery List</span></a>
	</li>
           <li class="<com:TPlaceHolder ID="PantryMenu" />">
		<a class="menuitem" href="?page=PantryHome">
		<span>Pantry</span></a>
	</li>
           <li class="<com:TPlaceHolder ID="MyAccountMenu" />">
		<a class="menuitem" href="?page=MyAccountHome">
		<span>My Account</span></a>
	</li>
</ul>
<com:TClientScript PradoScripts="prado">
	Event.OnLoad(function()
	{
                <!-- Jquery for when mouse over set it to be highlighted -->
		menuitems = $$(".menuitem");
		menuitems.each(function(el)
		{
			Event.observe(el, "mouseover", function(ev)
			{	
				menuitems.each(function(item)
				{
					Element.removeClassName(item.parentNode, "active");
				});
				node = Event.element(ev).parentNode;
				if(node.tagName.toLowerCase() != 'li')
					node = node.parentNode;
				Element.addClassName(node, "active");
			});
		});
	});
</com:TClientScript>
</com:TPanel>