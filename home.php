<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php include __DIR__."/packages/incs/head" ?>
  <body>

    <?php include __DIR__."/packages/incs/header" ?>

    <main id="app_main">

      <div class="ads-wrapper">
        <img src="https://is.shop-pharmacie.fr/banners/3379/files/00/1f/48/ab/000002050219.png" alt="ad_wallpaper">
      </div>

      <section>
        <label>
          Pharmacie et parapharmacie en ligne
        </label>
        <div class="fastAccessWrapper">
          <span class="landingFastAccess">
            <a href="#">
              <i class="far fa-percent"></i> Promotions
            </a>
          </span>
          <span class="landingFastAccess">
            <a href="#">
              <i class="far fa-envelope"></i> Newsletter
            </a>
          </span>
          <span class="landingFastAccess">
            <a href="#">
              <i class="far fa-mobile"></i> Appli Android/iOS
            </a>
          </span>
          <span class="landingFastAccess">
            <a href="#">
              <i class="far fa-euro-sign"></i> Lots & offres spéciales
            </a>
          </span>
          <span class="landingFastAccess">
            <a href="#">
              <i class="far fa-gift"></i> Coffrets & formats voyage
            </a>
          </span>
        </div>
      </section>


      <section style="margin-top: 6rem">
        <label>
          Meilleures ventes
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
            const list = await api_fetch("articles/~/~/0/8");
            listArticles(list, gallery);
          }());
        </script>
      </section>


      <section style="margin-top: 6rem">
        <label>
          Meilleures marques
        </label>

        <div class="brands-gallery">
          loading...
        </div>

        <br>
        <div class="buttons-wrapper ak-right">
          <button type="button" class="ak-cuteBtn ak-blue ak-alphaModed ak-rounded">
            <span>
              Tous les marques <i class="far fa-arrow-right"></i>
            </span>
          </button>
        </div>

        <script type="text/javascript">
          (async function(){
            let gallery = document.currentScript.parentNode.querySelector(".brands-gallery");

            let galleryWrapper = document.createElement("div");
            let list = await api_fetch("brands/~/~/~/6");

            list.map(_ => {
               var template = `
               <div class="brand-box">
                 <a href="brands/${_.id}/${_.name.trim().replaceAll(" ", "-").toLowerCase()}">
                  <img src="${_.logos[0]}" alt="brand_01">
                 </a>
               </div>
              `;
              galleryWrapper.innerHTML += template;
            });

            gallery.innerHTML = galleryWrapper.innerHTML;

          }());

        </script>

      </section>

    </main>

    <?php require __DIR__."/packages/incs/footer" ?>

  </body>
</html>
