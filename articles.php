<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php include __DIR__."/packages/incs/head" ?>
  <?php

    $_GET = toClass($_GET);

  ?>
  <script type="text/javascript">
    var $_GET = JSON.parse("<?php print addslashes(json_encode($_GET)) ?>");
    var filters = {
      q: "<?php print isset($_GET->q) ? htmlentities(trim($_GET->q)) : "~" ?>",
      page: "<?php print isset($_GET->page) ? intval($_GET->page) : "1" ?>",
      records: "<?php print isset($_GET->r) ? intval($_GET->r) : "14" ?>",
      category: "<?php print isset($_GET->c) ? intval($_GET->c) : "~" ?>",
      brand: "<?php print isset($_GET->brand) && trim($_GET->brand) ? htmlentities(trim($_GET->brand)) : "~" ?>",
      min: "<?php print isset($_GET->min) ? floatval($_GET->min) : "~" ?>",
      max: "<?php print isset($_GET->max) ? floatval($_GET->max) : "~" ?>"
    }
  </script>
  <body>

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
              Tous les articles
            </a>
          </li>
          <?php
            if( isset($_GET->q) ) {
              ?>
              <li>
                <a>
                  Recherche: <?php print htmlentities(trim($_GET->q)) ?>
                </a>
              </li>
              <?php
            }
          ?>
        </ul>
      </div>

      <section style="margin-top: 6rem">
        <div class="flex-gallery">
          <aside class="ak-small">
            <div class="offwhite-box">
              <h2>Filterage rapide</h2>
              <p>
                Filtrer les produits tant que vous voullez afin de retrouver les produits souhaités plus rapidement.
              </p>

              <menu>
                <form method="get" class="ak-fast-search">
                  <fieldset>
                    <input type="text" name="q" value="<?php print isset($_GET->q) ? $_GET->q : "" ?>" required placeholder="Produit, mot clé, marque...">
                    <legend>Recherche</legend>
                  </fieldset>
                  <fieldset>
                    <input type="text" name="c" list="categories" value="<?php print isset($_GET->c) ? $_GET->c : "" ?>">
                    <legend>Catégorie</legend>
                    <datalist id="categories">
                      <option value="">Tous les catégories</option>
                      <?php
                        foreach($SqlConnection->FetchAll("SELECT Id, Name FROM allopharma_categories ORDER BY CreationDate LIMIT 0, 4") as $i => $category) {
                          ?>
                          <option value="<?php print $category->Id ?>"><?php print trim($category->Name) ?></option>
                          <?php
                        }
                      ?>
                    </datalist>
                  </fieldset>
                  <fieldset>
                    <input type="text" name="brand" list="brands" value="<?php print isset($_GET->brand) ? $_GET->brand : "" ?>">
                    <legend>Marque</legend>
                    <datalist id="brands">
                      <option value="">Tous les marque</option>
                      <?php
                        foreach($SqlConnection->FetchAll("SELECT Name FROM allopharma_brands ORDER BY CreationDate DESC") as $i => $brand) {
                          ?>
                          <option value="<?php print htmlentities(trim($brand->Name)) ?>"><?php print htmlentities(trim($brand->Name)) ?></option>
                          <?php
                        }
                      ?>
                    </datalist>
                  </fieldset>
                  <fieldset class="ak-small">
                    <input type="number" name="min" step="1" value="<?php print isset($_GET->min) ? $_GET->min : "" ?>">
                    <legend>Prix Minimal</legend>
                  </fieldset>
                  <fieldset class="ak-small">
                    <input type="number" name="max" step="1" value="<?php print isset($_GET->max) ? $_GET->max : "" ?>">
                    <legend>Prix Maximal</legend>
                  </fieldset>
                  <button type="submit" class="ak-cuteBtn ak-blue">
                    <span>
                      <i class="far fa-search"></i>
                      Rechercher
                    </span>
                  </button>
                </form>
              </menu>
            </div>
          </aside>
          <aside>
            <div class="product-gallery" style="margin-top: -1rem" data-placeholder="Aucune résultat trouvé.">
              Loading ...
            </div>
            <br>

            <figure class="ak-navpage">
              <a data-actor="goFirst">
                <i class="far fa-chevron-double-left"></i>
              </a>
              <a data-actor="goBack">
                <i class="far fa-chevron-left"></i>
              </a>
              <a data-actor="goForward">
                <i class="far fa-chevron-right"></i>
              </a>
              <a data-actor="goLast">
                <i class="far fa-chevron-double-right"></i>
              </a>
            </figure>

            <script type="text/javascript">
              (async function(){
                let gallery = document.currentScript.parentNode.querySelector(".product-gallery");
                const list = await api_fetch(`articles/~/${ Object.entries(filters).map(([key, value]) => value).join("/") }`);
                listArticles(list, gallery);

                initNavpage(gallery.parentNode.querySelector("figure.ak-navpage"));

                function initNavpage(target) {
                  target.actors = {};
                  target.querySelectorAll("[data-actor]").forEach(_ => target.actors[_.dataset.actor] = _);

                  target.actors.goFirst.onclick = function() {
                    if( parseInt(filters.page) <= 1 ) return;

                    const customFilters = JSON.parse(JSON.stringify($_GET));
                    customFilters.page = 1;
                    window.location.href = `?${Object.entries(customFilters).map(([key, value]) => `${key}=${encodeURIComponent(value)}`).join("&")}`;
                  }
                  target.actors.goBack.onclick = function() {
                    if( parseInt(filters.page) <= 1 ) return;

                    const customFilters = JSON.parse(JSON.stringify($_GET));
                    customFilters.page = parseInt(filters.page)-1;
                    window.location.href = `?${Object.entries(customFilters).map(([key, value]) => `${key}=${encodeURIComponent(value)}`).join("&")}`;
                  }

                  target.actors.goForward.onclick = function() {
                    if( parseInt(filters.page) >= availablePages ) return;

                    const customFilters = JSON.parse(JSON.stringify($_GET));
                    customFilters.page = parseInt(filters.page) + 1;
                    window.location.href = `?${Object.entries(customFilters).map(([key, value]) => `${key}=${encodeURIComponent(value)}`).join("&")}`;
                  }
                  target.actors.goLast.onclick = function() {
                    if( parseInt(filters.page) >= availablePages  ) return;

                    const customFilters = JSON.parse(JSON.stringify($_GET));
                    customFilters.page = availablePages;
                    window.location.href = `?${Object.entries(customFilters).map(([key, value]) => `${key}=${encodeURIComponent(value)}`).join("&")}`;
                  }

                  const availablePages = Math.ceil(list.countAll / filters.records);
                  let currentPage = filters.p;

                  let insertAfterTarget = target.actors.goForward;

                  const prototype = document.createElement("a");
                  for(let i=0; i<availablePages; i++) {
                    let btn = prototype.cloneNode(true);
                    btn.innerHTML = '<span>'+(i+1)+'</span>';
                    const customFilters = JSON.parse(JSON.stringify($_GET));
                    customFilters.page = i+1;
                    btn.href = `?${Object.entries(customFilters).map(([key, value]) => `${key}=${encodeURIComponent(value)}`).join("&")}`;
                    if( i+1 == parseInt(filters.page) ) btn.classList.add("active");
                    target.insertBefore(btn, insertAfterTarget);
                  }
                }
              }());
            </script>
          </aside>
        </div>

      </section>


    </main>

    <?php require __DIR__."/packages/incs/footer" ?>

  </body>
</html>
