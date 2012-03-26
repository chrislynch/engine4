<div class="container">
<div class="span-24 last" id="HeaderContainer">
	<div id="Header">
		System Administration
		<div id="HeaderUserMenu">
			Welcome back Christopher<br>
			<a href="@@configuration.basedir@@">Return to website</a>&nbsp;|&nbsp;
                        <a href="@@configuration.basedir@@?e4_action=security&e4_security_op=deauthenticate">Log Out</a>&nbsp;|&nbsp;
		</div>
	</div>
</div>

<div class="span-24 last" id="MenuContainer">
	<div id="MainMenu">
		<ul id="MainMenuList">
			<li><a href="?e4_action=admin" class="MenuTab" id="HomeMenuItem">Home</a></li>
			<li><a href="#" class="MenuTab" id="ContentMenuItem">Content</a></li>
			<li><a href="#" class="MenuTab" id="UsersMenuItem">Users &amp; Security</a></li>
			<!-- 
			<li><a href="#" class="MenuTab" id="MessagesMenuItem">Messages</a></li>
			 -->
		</ul>
	</div>
	<!-- 
		This is a section if divs that hold the content for each menu item as well as the container where
		we swap them in and out. MenuContentContainer has its content replaced and then it's display toggled.
	-->
	<div id="MenuContentContainer">
	  PLACEHOLDER
	</div>
</div>
				
<div id="OrderItemsContainer">
<!-- 
	Create one XYZMenuContent for each XYZMenuItem in the MainMenuList. The plumbing for the menu relies on the XYZ 
	part being the same for both.
-->	
  <div id="ContentMenuContent" class="MenuContent">
		<ul>
			<li><a href="?e4_action=admin&e4_admin_op=create">Create Content</a><br></li>
			<li><a href="?e4_action=admin&e4_admin_op=search">Edit Content</a></li>
		</ul>
		<p>
			<!-- You can put any HTML content you want for your menu: tables, grids of links, headings, etc. This entire div just displays below the menu when requested.  -->
		</p>
	</div>
  <div id="UsersMenuContent" class="MenuContent">
		<div style="float:right; margin: 15px;">
			All of the mega menu functionality is convention based for naming.
		</div>
		<ul>
			<li><a href="?e4_action=admin&e4_admin_op=create&e4_adminType=user&e4_adminTypeIsContent=0">Create New User</a></li>
			<li><a href="?e4_action=admin&e4_admin_op=search&e4_adminType=user">Find User</a></li>
		</ul>
	</div>
	<div id="MessagesMenuContent" class="MenuContent">
		<ul>
			<li>Find Message</li>
			<li>Create New Message</li>
			<li>Browse All Pending Messages</li>
		</ul>
	</div>
</div>
			
<div class="span-24 last" id="HeaderEnd">&nbsp;</div>
<div class="clear"></div>

<?php 
	if (isset($data['page']['messages'])){
		foreach($data['page']['messages'] as $message){
			print '<div class="' . $message['type'] . 'Message">';
			print $message['type'] . ': ' . $message['message'];
			print '</div>';
		}	
	}
?>


