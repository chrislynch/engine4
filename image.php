<?php

/*
 * Render an image at an appropriate size.
 * Do not, at this point in time, save the image
 */

try {
	$imagefile = $_GET['imagefile'];
	
	// Check to see if the file already exists at the target height and width
	
	// First, pull the filename component out.
	$resizedFilepath = explode('/',$imagefile);
	$resizedFilename = array_pop($resizedFilepath);
	$resizedFilename = explode('.',$resizedFilename);
	$resizedFilename = array_shift($resizedFilename);
	$resizedFilepath = implode('/',$resizedFilepath);
	
	// Now build our resized filename
	if (isset($_GET['resizetoHeight'])){ $resizedFilename .= '_H_' . $_GET['resizetoHeight']; } 
	if (isset($_GET['resizetoWidth'])){ $resizedFilename .= '_W_' . $_GET['resizetoWidth']; }
	$resizedFilename .= '.png';	// All engine4 resizes result in a PNG file
	$resizedFilename = $resizedFilepath . '/' . $resizedFilename;
	
	if (file_exists($resizedFilename) && filectime($resizedFilename) > filectime($imagefile)){
		// The file exists, and is more recent than the originating file.
		// Just load up the file and return it. (Or can we give a 302 redirect to this?)
		$image = new WarpImage();
		$image->load($resizedFilename);
		if (!isset($_GET['debug'])) { header('Content-Type: image/png'); }
		$image->output();
	} else {
		// Expand memory limit, only for this script, to deal with large pictures
		ini_set('memory_limit','1024M');
		
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
		$image->save($resizedFilename);
		
		if (!isset($_GET['debug'])) { header('Content-Type: image/png'); }
		$image->output();
	}
	
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
         imagealphablending($this->image, false);
         imagesavealpha($this->image, true);
      }
   }
   
   function save($filename, $image_type=IMAGETYPE_PNG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,100);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$filename);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
      	 imagealphablending($this->image, false);
      	 imagesavealpha($this->image, true);
         imagepng($this->image,$filename,9);
      }   
   }
   
   function output($image_type=IMAGETYPE_PNG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,NULL,100);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image,NULL,9);
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
      
	  // These parameters are required for handling PNG files.
	  imagecolortransparent($new_image, imagecolorallocatealpha($new_image, 0, 0, 0, 127));
      imagealphablending($new_image, false);
      imagesavealpha($new_image, true);
      
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;   
   }
         
}

?>