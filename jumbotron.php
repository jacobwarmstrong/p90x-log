<?php

//page.php
//this is our main view.


//include the header
include('template-parts/header.php');
?>

<div>
Welcome to P90X Log, the app that you can't do P90X without! Are you ready to get in the best shape of your life? Knowledge is power! What gets measured gets managed!
</div>

<?php
//page specific content determined by the route, see config/routes.php
if(isset($e)) {
    include('template-parts/404.php');
} elseif ( has_content($parameters) ) {
    include($parameters['content']);
}

//include the footer
include('template-parts/footer.php');