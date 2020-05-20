<?php
//routes.php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;

$home = new Route('/', ['class' => null, 'view' => 'page.php', 'content' => 'template-parts/content-dashboard.php']);
$dashboard = new Route('/dashboard', ['class' => null, 'view' => 'page.php', 'content' => 'template-parts/content-dashboard.php']);
$workouts = new Route('/workout', ['class' => 'workout', 'view' => 'page.php', 'content' => 'template-parts/content-workouts.php']);
$login = new Route('/login', ['class' => 'login', 'view' => 'page.php', 'content' => 'template-parts/content-login.php']);

$routes = new RouteCollection();
$routes->add('home_page', $home);
$routes->add('dashboard', $dashboard);
$routes->add('workout_page', $workouts);
$routes->add('login_page', $login);

$context = new RequestContext();
$context->fromRequest(Request::createFromGlobals());

