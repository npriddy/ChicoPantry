<!--
  Module: MainLayout.php
  Author Name(s): Nate Priddy
  Date Created: 12/7/2010
  Purpose: This is the site template (Master Page) for all the other content pages.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<com:THead Title="ChicoPantry.com" />
<body>

<com:TForm>

<h1 class="heading">
    <a href="index.php">
        <span class="subheading"> <com:TImage Id="logo" Width="300px" ImageUrl="images/chicopantry2.png" /></span>
    </a>
</h1>
<div class="minheading">
<h2 class="login">
    <com:TLabel CssClass="name" Text="Welcome <%=($this->User->Name) %>" />
    <com:THyperLink NavigateUrl="<%= $this->Service->constructUrl('Login') %>"
                    Text="Login"
                    Visible="<%= $this->User->IsGuest %>" />
    <com:TLinkButton ID="Logout"
                    Text="Logout"
                    OnClick="Logout_Clicked"
                    Visible="<%= !$this->User->IsGuest %>"/>
</h2>
</div>
    <com:Application.pages.SiteMap/>
<div class="main">
        <com:TContentPlaceHolder ID="Main" />
</div>
 
</com:TForm>
    <div class="copyrights">
Copyright &copy; 2010 <a class="aWhite" href="http://www.ChicoPantry.com">ChicoPantry</a>.
<a class="aWhite" href="mailto:chicopantry@gmail.com">Contact Us</a>
<br />    <com:THyperLink NavigateUrl="<%= $this->Service->constructUrl('Admin') %>"
                    Text="Administration"
                    Visible="<%= $this->User->IsAdmin %>" />
 <com:TLabel CssClass="name" Visible="false" Text="Welcome <%=($this->User->getUserId()) %>" />
<com:THyperLink NavigateUrl="https://p3nlmysqladm001.secureserver.net/nld50/55/index.php?uniqueDnsEntry=chicopantry.db.3419564.hostedresource.com" Text="Php My Admin"  Visible="<%= $this->User->IsAdmin %>"  />
</div>

</body>
</html>