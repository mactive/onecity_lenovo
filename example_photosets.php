<?php
require_once("includes/phpFlickr.php");
// Create new phpFlickr object
$f = new phpFlickr();
$set_id=$_POST['set_id'];

$i = 0;
if ($set_id) {
 
    // Get the friendly URL of the user's photos
    //$photos_url = $f->urls_getUserPhotos($person['id']);
    $photosets = $f->photosets_getPhotos($set_id);
	
 
    // Get the user's first 36 public photos
    //$photos = $f->people_getPublicPhotos($person['id'], NULL, NULL, 36);
 
    // Loop through the photos and output the html
    foreach ((array)$photosets['photoset']['photo'] as $photo) {
        echo "<a href=$photos_url$photo[id]>";
        echo "<img border='0' alt='$photo[title]' ".
            "src=" . $f->buildPhotoURL($photo, "Square") . ">";
        echo "</a>";
        $i++;
        // If it reaches the sixth photo, insert a line break
        if ($i % 6 == 0) {
            echo "<br>\n";
        }
    }
}
?>
 
<h3>photosets:<?=$set_id;?></h3>
<form method='post'>
    <input name='set_id'><br>
    <input type='submit' value='Display Photosets'>
</form>
 
<p><a href="source">View Source</a></p>