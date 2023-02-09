<?php

  $_POST = toClass($_POST);

  if( $Client->isConnected && intval($Client->get->Role) >= 0 ) {

    if( isset($_POST->id) && isset($_POST->status) ) {

      $id = $_POST->id;
      $status = $_POST->status;
      $now = time();

      $SqlConnection->Query("UPDATE allopharma_orders SET Status='$status', LastUpdate='$now' WHERE Id='$id'");


      $statusNames = ["waiting", "confirmed", "delivered", "cancelled", "declined"];
      $render = [
        "validated" => true,
        "response" => "La commande a été modifiée.",
        "statusName" => $statusNames[$status],
        "updatedDate" => dateToString($now)
      ];

    }
    else $render['response'] = "Action impossible, l'opération a été annulée.";

  }
  else $render['response'] = "Vous devez être connecté pour profiter de cet fonction.";

?>
