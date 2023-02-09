<?php
  $page = 1;
  if( isset($_GET->page) && intval($_GET->page) ) $page = intval($_GET->page);

  $search = false;
  if( isset($_GET->search) && trim($_GET->search) ) $search = trim($_GET->search);

  $searchFilter = "";
  if( $search ) $searchFilter = " AND (Token LIKE '%$search%')";

  $countAllProducts = $SqlConnection->NumRows("SELECT Id FROM allopharma_orders WHERE (1=1$searchFilter)");
  $productPerPage = 15;
  $startIndex = ($page - 1) * $productPerPage;
  $availablePages = ceil($countAllProducts / $productPerPage);
  $availablePages = $availablePages <= 0 ? 1 : $availablePages;
?>
<div class="offwhite-box">
  <h2>Commandes</h2>
  <p>
    Analytiques de commandes, avec possibilité de modifier ou supprimer des commandes.
  </p>

  <canvas style="margin-top: 1rem;"></canvas>
  <script type="text/javascript">
    (function(){
      const chart = document.currentScript.parentNode.querySelector("canvas");
      new Chart(chart, {
        type: "bar",
        data: {
          labels: Date.months,
          datasets: [
            {
              label: "Commandes",
              data: [10, 15, 35, 22, 42],
              borderColor: "#2196f3",
              backgroundColor: "#2196f3"
            },
            {
              label: "Acceptés",
              data: [5, 9, 25, 30, 62],
              borderColor: "#127dd2",
              backgroundColor: "#127dd2"
            },
            {
              label: "Annulés",
              data: [5, 9, 25, 30, 62],
              borderColor: "tomato",
              backgroundColor: "tomato"
            }
          ]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {display: false},
            title: {display: false}
          },
          radius: 3
        }
      })
    }());
  </script>

  <menu>
    <header class="ak-sticky">
      <nav class="ak-products-head">
        <aside>
          <label class="ak-products-off"><?php print $countAllProducts ?> - <?php print ($page * $productPerPage) > $countAllProducts ? $countAllProducts : ($page * $productPerPage); ?> Affichés</label>
        </aside>
        <aside>
          <a href="<?php print ADMIN_SITE_ROOT ?>dashboard/gestion_products/new" class="ak-cuteBtn ak-blue ak-alphaModed ak-rounded" data-ash-tooltip="Ajouter un nouveau produit" />
            <span>
              <i class="far fa-plus"></i>
              Ajouter
            </span>
          </a>
        </aside>
      </nav>
      <nav class="ak-products-head">
        <aside>
          <a class="toggle-input">
            <input type="checkbox" id="toggle_all" data-ash-tooltip="Tout séléctionner">
            <p></p>
          </a>
          <a class="ak-cuteBtn ak-red ak-alphaModed ak-circle" id="delete_all" data-ash-tooltip="Tout supprimer" disabled>
            <i class="far fa-trash"></i>
          </a>
        </aside>
        <aside>
          <?php
            if( $search ) {
              ?>
              <a class="ak-cuteBtn ak-alphaModed ak-orange ak-circle" onclick="history.back()" data-ash-tooltip="Annuler la recherche">
                <i class="far fa-times"></i>
              </a>
              <?php
            }
          ?>
          <a class="ak-cuteBtn ak-alphaModed ak-green ak-circle" onclick="globalSearch()" data-ash-tooltip="Recherche">
            <i class="far fa-search"></i>
          </a>
          <a onclick="goPrevPage(<?php print $page ?>)" class="ak-cuteBtn ak-alphaModed ak-blue ak-circle ak-strict ak-nomargins" <?php if( $page <= 1 ) print 'disabled' ?> data-ash-tooltip="Page précedente">
            <i class="far fa-arrow-left"></i>
          </a>
          <a class="ak-cuteBtn ak-nomargins" style="background: transparent !important; opacity: 1;" disabled>
            <span style="color: rgba(128 128 128 / 50%)"><b style="color: #333"><?php print $page ?></b> : <?php print $availablePages ?></span>
          </a>
          <a onclick="goNextPage(<?php print $page ?>)" class="ak-cuteBtn ak-alphaModed ak-blue ak-circle ak-strict ak-nomargins" <?php if( $page >= $availablePages ) print 'disabled' ?> data-ash-tooltip="Page suivante">
            <i class="far fa-arrow-right"></i>
          </a>
        </aside>
      </nav>
    </header>
    <table class="ak-products" id="products_table" cellspacing=0>
      <tr></tr>
      <?php
        $count = 0;
        foreach($SqlConnection->FetchAll("SELECT * FROM allopharma_orders WHERE (1=1$searchFilter) ORDER BY CreationDate DESC LIMIT $startIndex, $productPerPage") as $i => $order) {
          $client = $Client->getBy(["Id" => $order->ClientId]);
          ?>
          <tr data-order="<?php print $order->Id ?>">
            <td>
              <div class="toggle-input">
                <input type="checkbox" value="<?php print $order->Id ?>">
                <p></p>
              </div>
            </td>
            <td class="ak-stretch">
              <span class=ak-name>
                <?php print htmlentities($order->Token) ?>
              </span>
              <span class="ak-product-stats">
                <span class="ak-unit-price"><?php print "$client->LastName $client->FirstName" ?></span>
              </span>
            </td>
            <td>
              <span>
                <?php
                  switch($order->Status) {
                    case 0:
                      ?>
                      <span class="ak-status-badge ak-waiting" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?>">
                        ● En Attente
                      </span>
                      <?php
                    break;

                    case 1:
                      ?>
                      <span class="ak-status-badge ak-confirmed" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?>">
                        ● Confirmée
                      </span>
                      <?php
                    break;

                    case 2:
                      ?>
                      <span class="ak-status-badge ak-delivered" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?>">
                        ● Livrée
                      </span>
                      <?php
                    break;

                    case 3:
                      ?>
                      <span class="ak-status-badge ak-cancelled" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?>">
                        ● Annulée
                      </span>
                      <?php
                    break;

                    default:
                      ?>
                      <span class="ak-status-badge ak-declined" data-ash-tooltip="<?php print dateToString($order->LastUpdate) ?> <br><span>Raison: <?php print htmlentities($order->DeclineReason) ?></span>">
                        ● Refusée
                      </span>
                      <?php
                    break;
                  }
                ?>
              </span>
            </td>
          </tr>
          <?php
          $count = $i + 1;
        }
      ?>
    </table>

    <script type="text/javascript">
      (function(){
        if( (typeof toggle_all == "undefined" || !toggle_all) || (typeof products_table == "undefined" || !products_table) ) return;


        products_table.querySelectorAll("tr[data-order]").forEach((_, i) => {
          _.onclick = function(e) {
            if( !e.isTrusted || e.target.tagName == "INPUT" ) return;
            window.location.href = `${location.rootHref}dashboard/gestion_orders_edit?id=${this.dataset.order}`;
          }
        });


        const toggles = products_table.querySelectorAll("input[type=checkbox]");

        function togglesChanged(toggles) {

          if(typeof delete_all == "object" && delete_all.tagName) {
            delete_all.disable = !toggles.length;
          }

        }

        toggle_all.onchange = function(e) {
          toggles.forEach(_ => {
            _.checked = this.checked;
          });

          togglesChanged.apply(this, [products_table.querySelectorAll("input:checked")]);
        }

        toggles.forEach(_ => {
          _.onchange = function() {
            toggle_all.checked = toggles.length <= products_table.querySelectorAll("input:checked").length;

            togglesChanged.apply(this, [products_table.querySelectorAll("input:checked")]);
          }
        });

        delete_all.onclick = async function() {
          var ids = [];
          var checkedProducts = products_table.querySelectorAll("input:checked:not(#toggle_all)");

          checkedProducts.forEach(_ => ids.push(parseInt(_.value)));

          this.initialText = this.innerHTML;
          this.disable = true;
          this.innerHTML = '<i class="far fa-spinner fa-spin"></i>';
          await post({
            url: `${location.rootHref}submit/delete_orders`,
            data: {
              ids: ids
            },
            fail: err => {
              console.error(err);
              this.innerHTML = this.initialText;
              this.disable = false;
            },
            done: promise => {
              if( promise.response.validated ) {

                checkedProducts.forEach(_ => {
                  let tr = _.parentNode;
                  while(tr != document.body && tr.tagName != "TR") tr = tr.parentNode;
                  tr.remove();
                });

              }
              else {
                alert("Un problème s'est produit lors de la tentative, nous n'avons pas pu complèter la commande.");
              }

              this.disable = true;
              this.innerHTML = this.initialText;
            }
          });
        }
      }());
    </script>
  </menu>
</div>
<template id="search_template">
  <figure class="search-global" data-actor="root">
    <form autocomplete="off" method="post" data-actor="form">
      <input type="text" name="search" value="" data-actor="input" placeholder="Nom de produit, marque...">
      <button type="submit">
        <span>
          <i class="far fa-search"></i>
        </span>
      </button>
    </form>
    <section class="history" data-actor="history" data-label="Historique de recherches"></section>
  </figure>
</template>
