<?php

  include __DIR__."/../packages/app.unite";

  define('skipSelector', "~");

  function killRouter($msg=false) {
    !$msg ? "Error! Invalid signature." : $msg;
    echo $msg;
    exit;
  }

  $routes = "";
  if(isset($_GET['routes'])) $routes = trim($_GET['routes']);
  $routes = explode("/", $routes);

  if( count($routes) < 1 ) killRouter();

  $route = $routes[0];
  unset($routes[0]);
  $routes = explode("/", join("/", $routes));

  $_ROUTES = $routes;
  unset($routes);


  $targetPath = __DIR__."/$route.api";
  if( !is_file($targetPath) ) killRouter();


  require $targetPath;

?>
