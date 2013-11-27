<?php

/*
 * Render an image at an appropriate size.
 * Do not, at this point in time, save the image
 */

try {
	$imagefile = $_GET['imagefile'];
	
	// Check to see if the file already exists at the target height and width
	
	// First, pull the filename component out.

        // Expand memory limit, only for this script, to deal with large pictures
        ini_set('memory_limit','256M');       
        
        // Create image class and 
        $image = new WarpImage();
        $image->load($imagefile);

        // Process any resize commands
        if (isset($_GET['resizetoHeight'])){
                if ($image->getHeight() > $_GET['resizetoHeight']){
                        $image->resizeToHeight($_GET['resizetoHeight']);	
                }
        }

        if (isset($_GET['resizetoWidth'])){
                if ($image->getWidth() > $_GET['resizetoWidth']){
                        $image->resizetoWidth($_GET['resizetoWidth']);	
                }
        } 

        // Now save the image file, so that we don't have to do this again
        // $image->save($resizedFilename);

        header('Content-Type: image/jpeg');
        $image->output();
	
} catch (Exception $e) {
	print $e->getMessage();
}

class WarpImage {
   
   var $image;
   var $image_type;
 
   function load($filename) {
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
         $this->image = imagecreatefrompng($filename);
      }
   }
   
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=100, $permissions=null) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$filename);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image,$filename);
      }   
      if( $permissions != null) {
         chmod($filename,$permissions);
      }
   }
   
   function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,'',100);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image,'',100);
      }   
   }
   
   function getWidth() {
      return imagesx($this->image);
   }
   
   function getHeight() {
      return imagesy($this->image);
   }
   
   function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
   
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
   
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100; 
      $this->resize($width,$height);
   }
   
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;   
   }
         
}

?>