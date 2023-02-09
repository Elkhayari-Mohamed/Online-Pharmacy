<?php

  $_POST = toClass($_POST);

  if( $Client->isConnected ) {

    if( isset($_POST->article) && intval($_POST->article) ) {

      $nowTime = time();

      $wish = $SqlConnection->Fetch("SELECT Id, Deleted FROM allopharma_wishlist WHERE (ClientId='$Client->id' AND ArticleId='$_POST->article')");

      if( $wish ) {
        $deleteState = $wish->Deleted ? 0 : 1;
        $SqlConnection->Query("UPDATE allopharma_wishlist SET Deleted='$deleteState', CreationDate='$nowTime' WHERE (Id='$wish->Id')");

      }
      else $SqlConnection->Insert("INSERT INTO allopharma_wishlist (ClientId, ArticleId, CreationDate) VALUES('$Client->id', '$_POST->article', '$nowTime')");


      $render = [
        "validated" => true,
        "response" => "Votre liste de favoris a été mise à jour."
      ];

    }
    else $render['response'] = "Action impossible, l'opération a été annulée.";

  }
  else $render['response'] = "Vous devez être connecté pour profiter de cet fonction.";

?>
