<?php

//login.php

?>

<div class="container mx-auto col-sm-10 col-md-4 pt-5">
    <div class="alert"><?php echo display_errors(); echo display_success(); ?></div>
    <div class="card p-3">
        <h3 class="form-signin-heading my-2">Sign In</h3>
        <form class="form-signin p-3" method="post" action="/controllers/loginUser.php">
            <label for="inputEmail" class="">Email</label>
            <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required autofocus>
            <br>
            <label for="inputPassword" class="">Password</label>
            <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
            <br>
            <button class="btn btn-lg btn-primary btn-block my-3" type="submit">Sign in</button>
        </form>
    </div>
    
</div>