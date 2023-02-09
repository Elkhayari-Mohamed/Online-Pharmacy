<div class="flex-gallery">
  <aside>
    <div class="offwhite-box">
      <h2>Mes favoris</h2>
      <p>
        Vous êtes enfin là! Tous les produits que vous avez déjà aimé sont tous à votre disposition. <br><strong>Qu'attendez-vous pour les acheter?</strong>
      </p>

      <menu>
        <div class="flex-table ak-cuteRows" id="cartTable">
          <section class="ak-head">
            <aside class="ak-stretch">
              <span>Produit</span>
            </aside>
            <aside>
              <button type="button" data-ash-tooltip="Tout retirer" class="ak-uncart">
                <i class="far fa-times"></i>
              </button>
            </aside>
          </section>
          <?php
            $count = 0;
            foreach($SqlConnection->FetchAll("SELECT * FROM allopharma_wishlist WHERE (ClientId='$Client->id' AND !Deleted) ORDER BY CreationDate DESC") as $i => $wish) {
              $article = $SqlConnection->Fetch("SELECT * FROM allopharma_articles WHERE Id='$wish->ArticleId'");
              if( !$article ) continue;
              $article->illustrations = explode(", ", $article->IllustrationCSV);
              $article->soldout = $article->Stock <= 0;
              $article->secondaryPrice = $article->Price * ( 1 - ($article->Promotion/100));
              ?>
              <section data-wish-id="<?php print $wish->Id ?>">
                <aside>
                  <img src="<?php print $article->illustrations[0] ?>" alt="illustration" class="ak-illustration">
                </aside>
                <aside class="ak-stretch">
                  <span class="ak-name">
                    <a href="<?php print SITE_ROOT ?>product/<?php print $article->Id ?>/<?php print str_replace(" ", "-", $article->Title) ?>">
                      <?php print htmlentities($article->Title); ?>
                    </a>
                  </span>
                  <span class="ak-stock-status <?php if( $article->soldout ) print 'ak-soldout' ?>">
                    <span class="ak-unit-price">
                      <?php print formatPrice($article->secondaryPrice) ?>
                    </span>
                    <span>
                      ● <?php print $article->soldout ? "Epuisé" : "En stock <font style='color: #f44336; font-size: .9em; text-transform: none;'>($article->Stock)</font>"; ?>
                    </span>
                  </span>
                </aside>
                <aside>
                  <button type="button" data-ash-tooltip="Retirer" class="ak-uncart">
                    <i class="far fa-times"></i>
                  </button>
                </aside>
              </section>
              <?php
              $count = $i + 1;
            }

            if( $count <= 0 ) {
              ?>
              <center>
                Aucun favori n'a été enregistré.
              </center>
              <?php
            }
          ?>
        </div>
      </menu>
    </div>
  </aside>
</div>
