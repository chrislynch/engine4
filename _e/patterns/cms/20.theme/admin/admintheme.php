<html>
<head>
    <base href="<?= $this->_basehref() ?>">
    <link rel="stylesheet" href="_e/lib/blueprint/src/grid.css">
    <link rel="stylesheet" href="_e/css/app/app.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script type="text/javascript">
		  var currentMenuItem = "";
		  $(document).ready(function() {
          $("a.MenuTab").click(function() {
           var contentId = "#" + $(this).attr('id').replace("MenuItem","MenuContent");
           var menuContent = $(contentId).html();
		   if(menuContent == null){
			currentMenuItem = contentId;
			$("#MenuContentContainer").slideUp();
			$("#MenuContentContainer").html(menuContent);
			location.href=$(this).attr("href");
		   } else {
			$("#MenuContentContainer").html(menuContent);
			if(currentMenuItem != contentId){
                            currentMenuItem = contentId;
                            $("#MenuContentContainer").slideDown();
			} else {
                            currentMenuItem = "";
                            $("#MenuContentContainer").slideUp();
			}
		   }
          });
    });
    </script>
    <style>
            .MenuContent {
                    display:none;

            }

            #MenuContentContainer {
                    display:none;
                    background-color: #333333;
                    color: white;
                    padding: 15px;

                    font-size: 14px;
                    font-weight: bold;
                    -moz-border-radius: 5px;
                    -webkit-border-radius: 5px;
                    -webkit-box-shadow: 0 1px 3px rgba(0,0,0, .4);
                    -moz-box-shadow: 0 1px 3px rgba(0,0,0, .4);
                    border: 1px solid #333333;
                    background-image: -moz-linear-gradient(100% 100% 90deg, #333333, #444444);
                    background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#444444), to(#333333));
                    padding: 10px;	
                    margin-top: -5px;

            }
            #MenuContentContainer a {
                    color: #ffffff;
                    border-bottom: none;

            }
    </style>
    
    <?php print @$this->process->javascript ?>
</head>
<body onload="initialize()">
    <div class="container" id="BodyContainer">
    <div  class="span-24 last" id="HeaderContainer">
        <div id="Header">
            engine4 Content Management System
            <div id="HeaderUserMenu">
                Connected to <?= $this->_basehref() ?><br>
                <?php if(isset($_COOKIE['user'])) { print "Logged in as {$_COOKIE['user']}"; } ?>
            </div>
        </div>
    </div>
        
    <div class="span-24 last" id="MenuContainer">
        <div id="MainMenu">
            <ul id="MainMenuList">
                <li><a href="admin" class="MenuTab" id="HomeMenuItem">Home</a></li>
                <li><a class="MenuTab" id="ContentMenuItem">Content Management</a></li>
                <li><a href="admin/filesystem" class="MenuTab" id="FileSystemMenuItem">File System</a></li>
		<!-- <li><a href="#" class="MenuTab" id="OrdersMenuItem">Reports</a></li> -->
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
				
    <div id="ContentItemsContainer">
        <!-- 
            Create one XYZMenuContent for each XYZMenuItem in the MainMenuList. The plumbing for the menu relies on the XYZ 
            part being the same for both.
        -->	
        <div id="ContentMenuContent" class="MenuContent">
                <div class="span-6">
                    <ul>
                        <li><a href="admin/content/add/post">Add Post</a>
                            <ul>
                                <li><a href="">Plain Text Post</a></li>
                                <li><a href="">Image Post</a></li>
                                <li><a href="">Media Post</a></li>
                                <li><a href="">Link/URL Post</a></li>
                                <li><a href="">Quote Post</a></li>
                            </ul>
                        </li>
                        <li><a href="admin/content/add/page">Add Page</a></li>
                    </ul>
                    <br>
                    <br>
                </div>
                <div class="span-6">
                    <ul>
                        <li><a href="admin/content/edit/posts">Edit Posts</a></li>
                        <li><a href="admin/content/edit/pages">Edit Pages</a></li>
                    </ul>
                    <br>
                    <br>
                </div>
                <p>A "Post" is a small piece of content; a tweet, a picture, a video, or short piece of text.</p>
                <p>A "Page" is a more significant piece of content that can be independently formatted.</p>
        </div>
    </div>
    
    <div class="span-24 last" id="MainContentContainer">
        <div id="MainContent">
            <div class="span-16">
                
                <?php print $this->content->html; ?>							  

            </div>
            <div class="span-8 last">
                <div class="Section">
                    <div class="SectionHeading">Help &amp; Info</div>
                    <div class="SectionContent">
                        <?= @$this->sidebar->html ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
</body>
</html>
