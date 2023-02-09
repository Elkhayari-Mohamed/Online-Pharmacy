<?php

  $_POST = toClass($_POST);
  if( $Client->isConnected ) {

    if( isset($_POST->notification) && intval($_POST->notification) ) {

      $SqlConnection->Query("DELETE FROM allopharma_notifications WHERE (ClientId='$Client->id' AND Id='$_POST->notification')");

      $render = [
        "validated" => true,
        "response" => "La notification a été supprimée."
      ];

    }
    else $render['response'] = "Action impossible, l'opération a été annulée.";

  }
  else $render['response'] = "Action impossible, vous ne pouvez pas supprimer une notification qui ne vous corréspand pas.";

?>
