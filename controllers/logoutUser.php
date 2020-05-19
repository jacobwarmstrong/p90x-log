<?php
//logout.php
//logs out our user by resetting the session cookie
include( __DIR__ . '/../config/bootstrap.php');

try {
    $accessToken = \Symfony\Component\HttpFoundation\Cookie::create("access_token", "Expired", time()-3600, '/', getenv('COOKIE_DOMAIN'));
    $session->getFlashBag()->add('success', 'User Logged Out.');
    redirect('/login', ['cookies' => [$accessToken] ]);

} catch (\exception $e) {
    $session->getFlashBag()->add('error', 'Could Not Log Out. Try Again, Or Contact Support For Help.');
    redirect('/projects');
}
