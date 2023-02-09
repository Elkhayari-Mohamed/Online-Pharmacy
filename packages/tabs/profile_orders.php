<div class="flex-gallery">
  <aside>
    <div class="offwhite-box">
      <h2>Mes commandes</h2>
      <p>
        Tous les commandes passés sur ce compte sont enregistrés, et peuvent être modifier plustard.
      </p>

      <menu>
        <div class="flex-table ak-cuteRows" id="cartTable">
          <section class="ak-head">
            <aside class="ak-stretch">
              <span>Commande</span>
            </aside>
            <aside>
              <span>État</span>
            </aside>
            <aside>
              <button type="button" data-ash-tooltip="Tout supprimer" class="ak-uncart">
                <i class="far fa-trash"></i>
              </button>
            </aside>
          </section>
          <?php
            $count = 0;
            foreach($SqlConnection->FetchAll("SELECT * FROM allopharma_orders WHERE ClientId='$Client->id' ORDER BY CreationDate DESC") as $i => $order) {
              $order->LastUpdate = $order->LastUpdate ? $order->LastUpdate : $order->CreationDate;
              ?>
              <section data-order-id="<?php print $order->Id ?>">
                <aside class="ak-stretch">
                  <span class="ak-name">
                    <a href="<?php print SITE_ROOT ?>profile/order/<?php print $order->Token ?>">
                      <?php print $order->Token ?>
                    </a>
                  </span>
                  <span>
                    <?php print dateToString($order->CreationDate) ?>
                  </span>
                </aside>
                <aside>
                  <?php
                    switch($order->Status) {
                      case 0:
                        ?>
                        <span class="ak-status-badge ak-waiting" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?>">
                          En Attente
                        </span>
                        <?php
                      break;

                      case 1:
                        ?>
                        <span class="ak-status-badge ak-confirmed" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?>">
                          Confirmée
                        </span>
                        <?php
                      break;

                      case 2:
                        ?>
                        <span class="ak-status-badge ak-delivered" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?>">
                          Livrée
                        </span>
                        <?php
                      break;

                      case 3:
                        ?>
                        <span class="ak-status-badge ak-cancelled" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?>">
                          Annulée
                        </span>
                        <?php
                      break;

                      default:
                        ?>
                        <span class="ak-status-badge ak-declined" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?> <br><span>Raison: <?php print htmlentities($order->DeclineReason) ?></span>">
                          Refusée
                        </span>
                        <?php
                      break;
                    }
                  ?>
                </aside>
                <aside>
                  <button type="button" data-ash-tooltip="Supprimer" <?php if( $order->Status == 2 || $order->Status == 1 ) print 'disabled' ?> class="ak-uncart">
                    <i class="far fa-trash"></i>
                  </button>
                </aside>
              </section>
              <?php
              $count = $i + 1;
            }


            if( $count <= 0 ) {
              ?>
              <center>
                Aucune commande n'a été enregistrée.
              </center>
              <?php
            }
          ?>
        </div>
      </menu>
    </div>
  </aside>
</div>
