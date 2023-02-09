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


      if( !$article ) {
        header("Location: ".SITE_ROOT."404");
        exit();
      }

      $article = toClass($article);
      $article->secondaryPrice = $article->Price * ( 1 - ($article->Promotion/100));

      /*
      if( $article->Title != str_replace("-", " ", $req->title) ) {
        header("Location: ".SITE_ROOT."404");
        exit();
      }
      */

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
            <a href="<?php print SITE_ROOT ?>articles">
              Produits
            </a>
          </li>
          <li>
            <a href="#">
              <?php print $req->title ?>
            </a>
          </li>
        </ul>
      </div>

      <section class="ak-product-page">

        <label>
          <?php print $article->Title ?>
        </label>
        <label class="ak-subtitle">
          <?php print $article->Type ?>
        </label>
        <br>
        <div class="product-estabslishers">
          <aside class="ak-illustrator">
            <div class="product-illustrator">
              <div class="ashSwiper">
                <?php
                  foreach(explode(",", $article->IllustrationCSV) as $i => $src) {
                    ?>
                    <img src="<?php print $src ?>" alt="illustration_<?php print $i ?>" draggable="false">
                    <?php
                  }
                ?>
              </div>
              <div class="beautifiers">
                <button type="button" onclick="fullScreenImgFromSwiper(this.parentNode.parentNode.querySelector('.ashSwiper').ashSwiper)">
                  <span>
                    <i class="far fa-expand"></i>
                  </span>
                </button>
              </div>
              <script type="text/javascript">
                function fullScreenImgFromSwiper(swiper) {
                  return fullScreenImg(swiper.steps[swiper.currentStep]);
                }
              </script>
            </div>
            <div class="product-illustrator-icons">
              <?php
                foreach(explode(",", $article->IllustrationCSV) as $i => $src) {
                  ?>
                  <figure <?php if( $i <= 0 ) print 'class="active"' ?> data-index="<?php print $i ?>">
                    <img src="<?php print $src ?>" alt="illustration_<?php print $i ?>" draggable="false">
                  </figure>
                  <?php
                }
              ?>
            </div>

            <script type="text/javascript">
              (function(){
                let wrapper = document.currentScript.parentNode;
                let icons = wrapper.querySelectorAll(".product-illustrator-icons figure");
                let swiperNode = wrapper.parentNode.querySelector(".ashSwiper");

                new ASHSwiper(
                  swiperNode,
                  {
                    treshold: .35,
                    for: "img"
                  },
                  {
                    change: function(e) {
                      icons[this.currentStep].click();
                    },

                    ready: function(e) {
                      icons.forEach(_ => {
                        _.onclick = () => {
                          icons.forEach(_ => _.classList.remove("active"));
                          _.classList.add("active");

                          this.GoTo(_.dataset.index);
                        }
                      });
                    }
                  }
                );
              }());
            </script>
          </aside>

          <aside class="ak-informizer">

            <div class="ak-description">
              <?php
                print str_replace("\\", "", htmlspecialchars_decode(base64_decode($article->Description)));
              ?>
            </div>
          </aside>

          <aside class="ak-finalizer">
            <div class="product-finalizer">
              <label class="ak-price" data-original="<?php print $article->Price != $article->secondaryPrice ? formatPrice($article->Price) : "" ?>">
                <span>
                  <?php print formatPrice($article->secondaryPrice) ?>
                </span>
              </label>
              <div class="flex-row">
                <span>
                  Quantity
                </span>
                <div class="input-numerator">
                  <button type="button" data-operator="-"><i class="far fa-minus"></i></button>
                  <input type="text" id="fastOrderProductInput" value="1" min="1" max="10" readonly>
                  <button type="button" data-operator="+"><i class="far fa-plus"></i></button>
                </div>
              </div>
              <div class="buttons-wrapper">
                <button onclick="fastOrderProduct(this)" id="fastOrderProductBtn" data-id="<?php print $req->id ?>" data-quantity="1" type="button" <?php if( $article->Stock <= 0 ) print "disabled" ?> class="ak-cuteBtn ak-blue ak-rounded ak-large">
                  <span>
                    <i class="fa fa-shipping-fast"></i>
                    Commander
                  </span>
                </button>
                <?php
                  if( $article->Stock <= 0 ) {
                    ?>
                    <button onclick="productNotify(this)" data-id="<?php print $article->Id ?>" type="button" class="ak-cuteBtn ak-blue ak-alphaModed">
                      <span>
                        <i class="fa fa-bell"></i>
                        Me notifier d'arrivage
                      </span>
                    </button>
                    <?php
                  }
                  else {
                    ?>
                    <button onclick="addToCart(this)" data-id="<?php print $article->Id ?>" type="button" class="ak-cuteBtn ak-blue ak-alphaModed">
                      <span>
                        <i class="fa fa-cart-arrow-down"></i>
                        Ajouter au panier
                      </span>
                    </button>
                    <?php
                  }
                ?>
              </div>
              <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <th>Dispo WEB:</th>
                  <th>Dispo Boutique:</th>
                </tr>
                <tr>
                  <?php
                    if( $article->Stock > 0 ) {
                      ?>
                      <td class="ak-available">
                        En Stock
                      </td>
                      <?php
                    }
                    else {
                      ?>
                      <td>
                        Epuisé
                      </td>
                      <?php
                    }
                  ?>
                  <td>
                    <a href="#">
                      Voir ma pharmacie
                    </a>
                  </td>
                </tr>
              </table>
              <div class="fastActions">
                <button type="button" onclick="Wish(<?php print $req->id ?>)" data-ash-tooltip="Ajouter au favoris" data-ash-tooltip-closingType="default" data-ash-tooltip-styles="none" class="ak-cuteBtn ak-alphaModed ak-pink ak-rounded">
                  <span>
                    <i class="fa fa-heart"></i>
                  </span>
                </button>
                <button type="button" data-ash-tooltip="Partager l'article" class="ak-cuteBtn ak-alphaModed ak-blue ak-rounded">
                  <span>
                    <i class="fa fa-share-alt"></i>
                  </span>
                </button>
                <button type="button" data-ash-tooltip="Commentaires" class="ak-cuteBtn ak-alphaModed ak-green ak-rounded">
                  <span>
                    <i class="fa fa-comment-dots"></i>
                  </span>
                </button>
              </div>
              <div class="foot">
                <a href="<?php print SITE_ROOT ?>articles">
                  <i class="far fa-arrow-left"></i>
                  <span>Continuer le shopping</span>
                </a>
              </div>
            </div>
          </aside>
        </div>

      </section>


      <section style="margin-top: 6rem">
        <label>
          Produits similaires
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

        <script type="text/javascript">
          (async function(){
            let gallery = document.currentScript.parentNode.querySelector(".product-gallery");
            const list = await api_fetch("articles/~/~/1/4");
            listArticles(list, gallery);
          }());

          (function(){
            if( !fastOrderProductBtn || !fastOrderProductInput ) return;
            fastOrderProductInput.oninput = function() {
              fastOrderProductBtn.dataset.quantity = this.value;
            }

            fastOrderProductBtn.onclick = function() {
              window.location.href = `checkout/${btoa(JSON.stringify([{
                i: parseFloat(this.dataset.id),
                q: parseFloat(this.dataset.quantity)
              }]))}`.toUrl();
            }
          }());
        </script>
      </section>

    </main>

    <?php require __DIR__."/packages/incs/footer" ?>

  </body>
</html>
