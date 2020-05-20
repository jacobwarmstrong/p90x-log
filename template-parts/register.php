<?php
//requireAdmin();
?>

<div class="container mx-auto col-sm-10 col-md-4 pt-5">
    <div class="alert"><?php echo display_errors(); echo display_success(); ?></div>
    <div class="card p-3">
        <h3 class="form-signin-heading my-2">Registration</h3>
        <form class="form-signin-heading p-3" method="post" action="/controllers/registerUser.php">
            <label for="inputEmail" class="">Email address</label>
            <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required autofocus>
            <br>
            <label for="inputPassword" class="">Password</label>
            <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
            <br>
            <label for="inputConfirmPassword" class="">Confirm Password</label>
            <input type="password" id="inputConfirmPassword" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            <br>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
        </form>
    </div>
</div>

