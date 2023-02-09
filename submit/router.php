<?php

  $render = [
    "validated" => false,
    "response" => "(".basename(__FILE__)."):5 Uncaught Error, Cannot find any response"
  ];

  require __DIR__."/../packages/app.unite";


  $to = "";
  if( isset($_GET['to']) ) $to = trim($_GET['to']);

  if( is_file(__DIR__."/$to.php") ) {
    require __DIR__."/$to.php";
  }

  header("Content-type: application/json");
  echo json_encode($render, JSON_PRETTY_PRINT);
  exit;

?>
