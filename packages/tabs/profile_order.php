<?php
  $order = 0;
  $_GET = toClass($_GET);
  if( isset($_GET->key) ) $order = $_GET->key;
  $order = $SqlConnection->Fetch("SELECT * FROM allopharma_orders WHERE Token='$order'");
  if( !$order ) {
    echo "error";
    exit;
  }


?>
<script type="text/javascript">
  var orderStatus = <?php print intval($order->Status) ?>;
</script>
<div class="flex-gallery">
  <aside>
    <div class="offwhite-box">
      <a onclick="history.back()" class="ak-cuteBtn ak-blue ak-alphaModed" style="width: fit-content; margin-bottom: 1rem;">
        <span><i class="far fa-arrow-left"></i> Revenir</span>
      </a>
      <div class="flex-table ak-cuteRows" id="cartTable">
        <section class="ak-head">
          <aside class="ak-stretch">
            <span>Produits</span>
          </aside>
          <aside>
            <span>Sous-total</span>
          </aside>
        </section>

        <?php
          $package = json_decode(base64_decode($order->Package));
          foreach($package as $i => $orderEntry) {
            $article = $SqlConnection->Fetch("SELECT * FROM allopharma_articles WHERE Id='$orderEntry->i'");
            if( !$article ) continue;

            $article->secondaryPrice = $article->Price * (1 - ($article->Promotion/100))
            ?>
            <section data-product-id="<?php print $article->Id ?>">
              <aside>
                <img src="<?php print explode(", ", $article->IllustrationCSV)[0] ?>" alt="illustration" class="ak-illustration">
              </aside>
              <aside class="ak-stretch">
                <span class="ak-name">
                  <a href="<?php print SITE_ROOT ?>product/<?php print $article->Id ?>/<?php print str_replace(" ", "-", $article->Title) ?>">
                    <?php print trim($article->Title) ?>
                  </a>
                </span>
                <div class="input-numerator">
                  <button type="button" data-operator="-" disabled><i class="far fa-minus"></i></button>
                  <input type="text" value="<?php print $orderEntry->q ?>" min="1" max="<?php print $article->Stock ?>" readonly>
                  <button type="button" data-operator="+" disabled><i class="far fa-plus"></i></button>
                </div>
                <span class="ak-stock-status <?php if( $article->Stock <= 0 ) print 'ak-soldout' ?>">
                  <span class="ak-unit-price">
                    <?php print formatPrice($article->secondaryPrice) ?>
                  </span>
                  <span>
                    ● <?php print $article->Stock <= 0 ? "Epuisé" : "En stock" ?>
                  </span>
                </span>
              </aside>
              <aside>
                <span class="ak-price">
                  <?php print formatPrice(+$article->secondaryPrice * +$orderEntry->q) ?>
                </span>
              </aside>
            </section>
            <?php
          }
        ?>
      </div>
    </div>
  </aside>
  <aside class="ak-small">
    <div class="offwhite-box">
      <h2><?php print trim($order->Token) ?></h2>
      <p>
        Vous pouvez modifier, annuler ou même supprimer cette commande.
      </p>
      <menu style="padding-top: 1rem;">
        <li>
          <label>Etat</label>
          <span>
            <?php
              switch($order->Status) {
                case 0:
                  ?>
                  <span class="ak-status-badge ak-waiting" style="padding: .35rem .75rem; font-size: .9em;" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?>">
                    ● En Attente
                  </span>
                  <?php
                break;

                case 1:
                  ?>
                  <span class="ak-status-badge ak-confirmed" style="padding: .35rem .75rem; font-size: .9em;" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?>">
                    ● Confirmée
                  </span>
                  <?php
                break;

                case 2:
                  ?>
                  <span class="ak-status-badge ak-delivered" style="padding: .35rem .75rem; font-size: .9em;" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?>">
                    ● Livrée
                  </span>
                  <?php
                break;

                case 3:
                  ?>
                  <span class="ak-status-badge ak-cancelled" style="padding: .35rem .75rem; font-size: .9em;" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?>">
                    ● Annulée
                  </span>
                  <?php
                break;

                default:
                  ?>
                  <span class="ak-status-badge ak-declined" style="padding: .35rem .75rem; font-size: .9em;" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?> <br><span>Raison: <?php print htmlentities($order->DeclineReason) ?></span>">
                    ● Refusée
                  </span>
                  <?php
                break;
              }
            ?>
          </span>
        </li>
        <li>
          <label>Date de commande</label>
          <span><?php print dateToString($order->CreationDate) ?></span>
        </li>
        <li>
          <label>Address</label>
          <span><?php print str_replace("\n", ", ", $order->Address) ?></span>
        </li>
        <li>
          <label>Téléphone</label>
          <span><?php print $order->Phone ?></span>
        </li>
        <li>
          <label>Nom & prenom</label>
          <span><?php print $order->BuyerName ?></span>
        </li>

        <li style="align-items: center;">
          <label></label>
          <span>
            <button type="button" id="update_status" <?php if( $order->Status != 0 ) print 'disabled' ?> class="ak-cuteBtn ak-red ak-alphaModed" style="width: 100%; justify-content: center;">
              <span>Annuler la commande</span>
              <?php
                if( $order->Status == 0 ) {
                  ?>
                  <script type="text/javascript">
                    (function(){
                      document.currentScript.parentNode.onclick = function() {
                        this.disable = true;
                        this.initialText = this.innerHTML;
                        this.innerHTML = '<span><i class="far fa-spinner fa-spin"></i></span>';

                        var status = 3;
                        var statusLabel = "Annulée";
                        post({
                          url: `${location.rootHref}submit/update_order_status`,
                          data: {
                            id: <?php print intval($order->Id) ?>,
                            status: status
                          },
                          fail: err => {
                            console.error(err);
                            this.innerHTML = this.initialText;
                            this.disable = false;
                          },
                          done: promise => {
                            if( promise.response.validated ) {
                              this.innerHTML = '<span><i class="far fa-check"></i></span>';
                              setTimeout(() => {
                                this.innerHTML = this.initialText;
                              }, 1500);

                              const badge = document.querySelector(".ak-status-badge");
                              badge.className = `ak-status-badge ak-${promise.response.statusName}`;
                              badge.innerHTML = `● ${statusLabel}`;
                              badge.dataset.ashTooltip = promise.response.updatedDate;
                              orderStatus = status;
                            }
                            else {
                              alert(promise.response.response);
                              this.innerHTML = this.initialText;
                              this.disable = false;
                            }
                          }
                        });
                      }
                    }())
                  </script>
                  <?php
                }
              ?>
            </button>
          </span>
        </li>
      </menu>
    </div>
  </aside>
</div>
