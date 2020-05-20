<?php

//page.php
//this is our main view.


//include the header
include('template-parts/header.php');
?>

<?php
//page specific content determined by the route, see config/routes.php
if(isset($e)) {
    include('template-parts/404.php');
} else {
    include($parameters['content']);
}

//include the footer
include('template-parts/footer.php');