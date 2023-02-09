<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php include __DIR__."/packages/incs/head" ?>
  <body>

    <?php include __DIR__."/packages/incs/header" ?>


    <?php

      $req = toClass([
        "id" => isset($_GET['id']) ? intval($_GET['id']) : 0,
        "title" => isset($_GET['title']) ? trim($_GET['title']) : ""
      ]);


      $article = $SqlConnection->Fetch("SELECT * FROM allopharma_articles WHERE Id='$req->id'");

    ?>

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
              Espace client
            </a>
          </li>
          <li>
            <a href="<?php print SITE_ROOT ?>cart">
              Panier
            </a>
          </li>
        </ul>
      </div>

      <section>

        <div class="flex-gallery">

          <aside>

            <div class="flex-table ak-cuteRows" id="cartTable">
              <section class="ak-head">
                <aside class="ak-stretch">
                  <span>Produits</span>
                </aside>
                <aside>
                  <span>Sous-total</span>
                </aside>
                <aside>
                  <button type="button" data-ash-tooltip="Tout retirer" class="ak-uncart" onclick="Cart.articles.removeAll()">
                    <i class="far fa-times"></i>
                  </button>
                </aside>
              </section>
            </div>
          </aside>

          <aside class="ak-small">

            <div class="invoiceCartbox">
              <section>
                <h3>Total Panier</h3>
              </section>
              <section>
                <li class="ak-pricers">
                  <label>Sous-total</label>
                  <span id="cart_subtotal">0,00 Dhs</span>
                </li>
                <li class="ak-pricers">
                  <label>Promotions</label>
                  <span id="cart_promotions">0,00 Dhs</span>
                </li>
                <li class="ak-pricers ak-total">
                  <label>Total</label>
                  <span id="cart_total">0,00 Dhs</span>
                </li>
                <a id="cart_checkout" role="button" class="ak-cuteBtn ak-blue" disabled>
                  <span>Valider la commande</span>
                </a>
                <span>
                  <a href="<?php print SITE_ROOT ?>articles">
                    <i class="far fa-arrow-left"></i> Continuer le shopping
                  </a>
                </span>
              </section>
              <section>
                <div class="motivers">
                  <li>
                    <i class="far fa-history"></i>
                    <label>1An</label>
                    <p>Satisfait ou remboursé</p>
                  </li>
                  <li>
                    <i class="fa fa-truck"></i>
                    <label>Livraison</label>
                    <p>Livraison partout au Maroc</p>
                  </li>
                </div>
              </section>
            </div>

          </aside>

        </div>

      </section>


      <section style="margin-top: 6rem">
        <label>
          Vous pourriez aussi aimer
        </label>

        <div class="product-gallery">
          Loading ...
        </div>
        <br>
        <div class="buttons-wrapper ak-right">
          <button type="button" class="ak-cuteBtn ak-blue ak-alphaModed ak-rounded">
            <span>
              Voir plus <i class="far fa-arrow-right"></i>
            </span>
          </button>
        </div>

        <script type="text/javascript" async>
          (async function(){
            let gallery = document.currentScript.parentNode.querySelector(".product-gallery");
            const list = await api_fetch("articles/~/~/1/4");
            listArticles(list, gallery);
          }());


          (function() {
            if( !cartTable ) return;

            var totalPrice = 0.00, totalSecondaryPrice = 0.00, totalPromotions=0.00;

            Cart.onchange = function() {
              totalPrice = 0.00, totalSecondaryPrice = 0.00;
              cartTable.classList.add("_loading");

              cartTable.querySelectorAll("section[data-product-id]").forEach(_ => _.remove());
              // and then list the new exsiting products list
              Cart.articles.all.map(async _ => {
                let article = await api_fetch(`articles/${_.id}`);
                if( article.length <= 0 ) {
                  Cart.articles.remove(_.id);
                  return;
                }
                article = article.articles[0];

                // edit total Counting
                totalPrice += article.price * _.quantity;
                totalSecondaryPrice += article.secondaryPrice * _.quantity;

                totalPromotions = parseFloat(totalPrice) - parseFloat(totalSecondaryPrice);

                // generate template
                `<section data-product-id="${_.id}">
                  <aside>
                    <img src="${article.illustrations[0]}" alt="illustration" class="ak-illustration">
                  </aside>
                  <aside class="ak-stretch">
                    <span class="ak-name">
                      <a href="${location.rootHref}product/${_.id}/${article.title.replaceAll(" ", "-")}">
                        ${article.title}
                      </a>
                    </span>
                    <div class="input-numerator">
                      <button type="button" data-operator="-"><i class="far fa-minus"></i></button>
                      <input type="text" value="${_.quantity}" min="1" max="${article.stock}" readonly>
                      <button type="button" data-operator="+"><i class="far fa-plus"></i></button>
                    </div>
                    <span class="ak-stock-status ${article.soldout ? "ak-soldout": ""}">
                      <span class="ak-unit-price">
                        ${article.secondaryPrice.format(2, ",")} Dhs
                      </span>
                      <span>
                        ● ${article.soldout ? "Epuisé" : "En stock"}
                      </span>
                    </span>
                  </aside>
                  <aside>
                    <span class="ak-price">
                      ${(article.secondaryPrice * _.quantity).format(2, ",")} Dhs
                    </span>
                  </aside>
                  <aside>
                    <button type="button" data-ash-tooltip="Retirer" class="ak-uncart">
                      <i class="far fa-times"></i>
                    </button>
                  </aside>
                </section>
                `.toTemplate(function() {

                  const totalPriceHandler = this.querySelector("span.ak-price");
                  const quantityModifier = this.querySelector(".input-numerator input");
                  const uncartBtn = this.querySelector("button.ak-uncart");

                  quantityModifier.addEventListener("input", function() {
                    totalPriceHandler.innerHTML = `${(article.secondaryPrice * parseFloat(this.value)).format(2, ",")} Dhs`;
                    Cart.articles.update(_.id, {quantity: parseFloat(this.value)});
                  });

                  uncartBtn.onclick = () => {
                    Cart.articles.remove(_.id);
                    this.remove();
                  }

                })
                .appendTo(cartTable, function(){
                  cartTable.classList.remove("_loading");
                  cart_subtotal.innerHTML = `${parseFloat(totalPrice).format(totalPrice > 999 ? 0 : 2, ",", ".")} Dhs`;
                  cart_total.innerHTML = `${parseFloat(totalSecondaryPrice).format(totalSecondaryPrice > 999 ? 0 : 2, ",", ".")} Dhs`;
                  cart_promotions.innerHTML = `-${parseFloat(totalPromotions).format(totalPromotions > 999 ? 0 : 2, ",", ".")} Dhs`;
                });
              });

              cart_checkout.disable = Cart.articles.count <= 0 ? true : false;
              if( Cart.articles.count <= 0 ) {
                cart_subtotal.innerHTML = `${parseFloat(0).format(2, ",")} Dhs`;
                cart_total.innerHTML = `${parseFloat(0).format(2, ",")} Dhs`;
                cart_promotions.innerHTML = `${parseFloat(0).format(2, ",")} Dhs`;
              }

              Cart.ondefaultupdate();
            }

            Cart.onchange();


            cart_checkout.onclick = function() {
              var submissionQuery = [];
              for(let article of Cart.storage.datas.cart) {
                submissionQuery.push({i:article.id, q:article.quantity});
              }

              submissionQuery = JSON.stringify(submissionQuery);
              return location.href = `${location.rootHref}checkout/${btoa(submissionQuery)}`;
            }
          }());
        </script>
      </section>

    </main>

    <?php require __DIR__."/packages/incs/footer" ?>
  </body>
</html>
