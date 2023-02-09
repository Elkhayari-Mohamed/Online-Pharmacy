<?php
  include_once __DIR__."/../packages/app.unite";
  const ADMIN_SITE_ROOT = SITE_ROOT."../";

  if( !$Client->isConnected || intval($Client->get->Role) < 1 ) {
    header("location: ". ADMIN_SITE_ROOT . "login");
    exit;
  }

  $_GET = toClass($_GET);


  // Administration tab pages routes handler
  $tab = "analytics";
  $key = "";
  if( isset($_GET->tab) ) {
    is_file(__DIR__."/core/tabs/$_GET->tab.php") ? $tab  = trim($_GET->tab) : header("location: $tab");
    unset($_GET->tab);
  }
  if( isset($_GET->key) ) {
    $key = trim($_GET->key);
    unset($_GET->key);
  }



?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php include_once __DIR__."/core/incs/head"; ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.4.0/dist/chart.min.js" onload="console.warn('ChartJs is running...');" charset="utf-8"></script>
  <script type="text/javascript">
    $_GET = <?php print json_encode($_GET) ?>;
  </script>
  <body>

    <main id="app_main">

      <section>
        <div class="widthLimiter">

          <div class="flex-gallery ak-absoluteSizing">
            <aside class="ak-x-small ak-stickyY">

              <menu class="nav-menu">
                <ul data-legend="Générals">
                  <a href="<?php print SITE_ROOT ?>analytics" class="active">
                    <i class="far fa-analytics"></i>
                    <span>Analytiques</span>
                  </a>
                </ul>
                <ul data-legend="Gestion/Produits">
                  <a href="<?php print SITE_ROOT ?>gestion_products">
                    <i class="far fa-flask-potion"></i>
                    <span>Produits</span>
                  </a>
                  <a href="<?php print SITE_ROOT ?>gestion_orders">
                    <i class="far fa-barcode"></i>
                    <span>Commandes</span>
                  </a>
                  <a href="<?php print SITE_ROOT ?>gestion_categories">
                    <i class="far fa-object-group"></i>
                    <span>Catégories</span>
                  </a>
                </ul>
                <ul data-legend="Clients">
                  <a href="<?php print SITE_ROOT ?>client_accounts">
                    <i class="far fa-users-medical"></i>
                    <span>Comptes</span>
                  </a>
                  <a href="<?php print SITE_ROOT ?>client_requests">
                    <i class="far fa-users-crown"></i>
                    <span>Demandes spéciales</span>
                  </a>
                </ul>
                <ul data-legend="Messagerie">
                  <a href="<?php print SITE_ROOT ?>contact_inbox" data-bulle="1">
                    <i class="far fa-inbox"></i>
                    <span>Boite de récéption</span>
                  </a>
                  <a href="<?php print SITE_ROOT ?>contact_others">
                    <i class="far fa-ellipsis-v"></i>
                    <span>Autres</span>
                  </a>
                </ul>

                <script type="text/javascript">
                  (function(){
                    document.currentScript.parentNode.querySelectorAll("a[href]").forEach(_ => {
                      const i = _.querySelector("i[class^='fa']");

                      var basename = _.href.split('/').slice().reverse()[0];
                      if( basename == "<?php print $tab ?>" ) {
                        _.classList.add("active");
                        i.classList.remove("far");
                        i.classList.add("fad");
                      }
                      else {
                        _.classList.remove("active");
                        i.classList.remove("fad");
                        i.classList.add("far");
                      }
                    });
                  }());
                </script>
              </menu>

            </aside>
            <aside>
              <?php
                try {
                  $tab = $key ? $tab."_".$key : $tab;
                  include_once __DIR__."/core/tabs/$tab.php";
                }
                catch(Exception $ex) {
                  echo $ex->getMessage();
                }
              ?>
            </aside>
          </div>

        </div>
      </section>

    </main>

  </body>
</html>
