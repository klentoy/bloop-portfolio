<?php 
function bloop_email_template(){
  
ob_start(); ?>
Hi Developer, Account manager shared this collection:
<a href="http://bloop.test/collection" target="_blank">Collection Name</a>
<?php 

return ob_get_clean();  
}