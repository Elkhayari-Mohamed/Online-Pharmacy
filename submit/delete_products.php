<?php

  $_POST = toClass($_POST);

  if( $Client->isConnected && intval($Client->get->Role) > 0 ) {

    if( isset($_POST->ids) ) {

      $ids = explode(",", $_POST->ids);


      $ids_filtered = [];

      foreach($ids as $i => $id) {
        if( !is_numeric($id) ) continue;
        array_push($ids_filtered, intval($id));
      }

      $filterQuery = "Id='".implode("' OR Id='", $ids_filtered)."'";

      $SqlConnection->Query("DELETE FROM allopharma_articles WHERE ($filterQuery)");

      $render = [
        "validated" => true,
        "response" => "Produits supprimés."
      ];

    }
    else $render['response'] = "Action impossible, l'opération a été annulée.";

  }
  else $render['response'] = "Vous devez être connecté pour profiter de cet fonction.";

?>
