<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php include __DIR__."/packages/incs/head" ?>
  <body>

    <?php

      if( !$Client->isConnected ) {
        header("Location: ./login");
        exit;
      }



      $availableTabs = [
        "general" => "Général",
        "security" => "Sécurité",
        "orders" => "Mes commandes",
        "wishlist" => "Mes favoris",
        "notifications" => "Notifications",
        "logout" => "Déconnexion",
        "order" => "Commande",
        "notification" => "Notification",
        "administration" => "Univers d'administrateurs"
      ];
      $tab = isset($_GET['tab']) && isset($availableTabs[$_GET['tab']]) ? $_GET['tab'] : array_keys($availableTabs)[0]; // default tab

    ?>

    <?php include __DIR__."/packages/incs/header" ?>

    <main id="app_main">

      <div class="access-path" style="margin-top: 1rem;">
        <ul>
          <li>
            <a href="<?php print SITE_ROOT ?>">
              <i class="fa fa-home"></i>
            </a>
          </li>
          <li>
            <a href="<?php print SITE_ROOT ?>profile">
              Profile
            </a>
          </li>
          <li>
            <a>
              <?php print $availableTabs[$tab] ?>
            </a>
          </li>
        </ul>
      </div>

      <section>

        <div class="flex-gallery">
          <aside class="ak-x-small">
            <menu class="profile-menu">
              <li class="active" data-href="./profile/general">
                <i class="far fa-tachometer-average"></i>
                <span>Général</span>
              </li>
              <li data-href="./profile/security">
                <i class="far fa-shield-check"></i>
                <span>Sécurité</span>
              </li>
              <li data-href="./profile/orders">
                <i class="far fa-cart-arrow-down"></i>
                <span>Mes commandes</span>
              </li>
              <li data-href="./profile/wishlist">
                <i class="far fa-heart"></i>
                <span>Mes favoris</span>
              </li>
              <li data-href="./profile/notifications" data-bulle="<?php print count($Client->getNotifications()->unOpened) ?>">
                <i class="far fa-bell"></i>
                <span>Notifications</span>
              </li>
              <?php
                if( intval($Client->get->Role) > 0 ) {
                  ?>
                  <li data-href="./profile/administration">
                    <i class="far fa-user-secret"></i>
                    <span>Univers d'administrateurs</span>
                  </li>
                  <?php
                }
              ?>
              <li class="ak-red" data-href="./profile/logout">
                <i class="far fa-sign-out"></i>
                <span>Se déconnecter</span>
              </li>
              <script type="text/javascript">
                (function(){
                  document.currentScript.parentNode.querySelectorAll("li[data-href]").forEach(_ => {
                    if( `./profile/<?php print $tab ?>` == _.dataset.href ) _.classList.add("active");
                    else _.classList.remove("active");

                    _.onclick = () => window.location.href = `${location.rootHref}${_.dataset.href}`;
                  });
                }());
              </script>
            </menu>
          </aside>
          <aside>
            <?php
              try {
                include __DIR__."/packages/tabs/profile_$tab.php";
              }
              catch (Exception $e) {
                echo $e->getMessage();
              }
            ?>
          </aside>
        </div>

      </section>

    </main>

    <?php require __DIR__."/packages/incs/footer" ?>

  </body>
</html>
