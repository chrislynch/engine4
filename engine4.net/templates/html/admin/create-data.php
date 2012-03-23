<div class="span-16">
	<h2 class="SectionHeading">Create Content</h2>
	<form action="?e4_action=admin&e4_op=save&e4_ID=0" method="POST">
		<table>
			<tr><td>Name</td><td><input name="e4_form_content_name" type="text" size="60" value=""></td></tr>
			<tr><td>Type</td><td>
                                <br><strong>Posting Data Types</strong><br>
                                <input type="radio" name="e4_form_content_type" value="Blog" checked>Post: The type used for blog posts and news articles<br>                          
                                <input type="radio" name="e4_form_content_type" value="Image">Image: The type used when posting a blog/news article about a specific image<br>
                                <input type="radio" name="e4_form_content_type" value="Video">Video: The type used when posting a blog/news article about a specific video<br>
                                <br><strong>Static Data Types</strong><br>
                                <input type="radio" name="e4_form_content_type" value="Content">Page: The generic content type used for static pages<br>
                                <?php
                                if (isset($data['configuration']['datatypes'])){
                                    print '<br><strong>Your Data Types</strong><br>';
                                    foreach($data['configuration']['datatypes'] as $typekey=>$config){
                                        print '<input type="radio" name="e4_form_content_type" value="' . $typekey . '">' . $config['name'] . ': ' . $config['description'] . '<br>';
                                    }
                                }
                                ?>
                                <br><strong>SEO Data Types</strong><br>    
                                <input type="radio" name="e4_form_content_type" value="Action">Action Alias: Create a clean URL for an action<br>
                                
                                <br><strong>eCommerce Data Types</strong><br>    
                                <input type="radio" name="e4_form_content_type" value="Product">Product: Create a product that can be sold<br>
                        </td></tr>
			<tr><td>&nbsp;</td><td><input name="save" type="submit" value="Create Content"></td></tr>			
		</table>
	</form>
</div>