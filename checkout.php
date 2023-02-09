<?php

  $package = null;
  if(isset($_GET['package'])) $package = $_GET['package'];

  $citiesFiles = __DIR__."/core/resources/cities.json";
  $cities = file_get_contents($citiesFiles);
  $cities = json_decode($cities, true);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php include __DIR__."/packages/incs/head" ?>
  <?php
    if( count($_POST) > 0 ) {

      $requiredPOSTS = ["lastName", "firstName", "email", "phone", "city", "addressline1", "ccHolder", "ccNumber", "ccExpMonth", "ccExpYear", "ccVC"];

      $requiredPOSTS_PASSES = true;
      foreach($requiredPOSTS as $key) {
        if(!isset($_POST[$key])) {
          $requiredPOSTS_PASSES = false;
          break;
        }
      }

      $_POST = toClass($_POST);

      $nowTime = time();

      $token = randomToken(20) . "~" . $nowTime;
      $creditCard = json_encode([
        "holder" => $_POST->ccHolder,
        "number" => $_POST->ccNumber,
        "cvc" => $_POST->ccVC,
        "exMonth" => $_POST->ccExpMonth,
        "exYear" => $_POST->ccExpYear
      ]);

      $cities =


      $fullAddress = "$_POST->addressline1\n".$cities[$_POST->city];
      $fullName = "$_POST->lastName $_POST->firstName";
      $query = "INSERT INTO allopharma_orders (Token, CreationDate, LastUpdate, ClientId, Package, Address, BuyerName, Phone, CreditCard)
      VALUES('$token', '$nowTime', '$nowTime', '$Client->id', '$package', '$fullAddress', '$fullName', '$_POST->phone', '$creditCard')";

      $res = $SqlConnection->Insert($query);

      if( $res ) {
        header('location: '.SITE_ROOT."profile/orders");
        exit;
      }
    }
  ?>
  <body>

    <header id="app_header">
      <section class="head">
        <ul>
          <li onclick="history.back()">
            <i class="far fa-arrow-left"></i>
            <span>
              Revenir
            </span>
          </li>
        </ul>

        <a href="<?php print SITE_ROOT ?>" class="ak-logo">
          <?php include __DIR__."/packages/incs/logo"; ?>
        </a>

        <ul>
          <li id="helpHeaderBtn">
            <i class="far fa-user-headset"></i>
            <span>
              Besoin d'aide?
            </span>
          </li>
        </ul>
      </section>

      <script type="text/javascript">
        (function(){
          if( helpHeaderBtn ) helpHeaderBtn.onclick = () => window.location.href = `${location.rootHref}help`;
        }());
      </script>
    </header>


    <main id="app_main">

      <?php
        $package = base64_decode($package);
        if( !$package ) {
          header("location: ".SITE_ROOT."404");
          exit;
        }

        $package = json_decode($package);
        if( !$package ) {
          header("location: ".SITE_ROOT."404");
          exit;
        }

        $articles = [];
        foreach($package as $i => $a) {
          $article = $SqlConnection->Fetch("SELECT Id, Title, Quantity, Unit, Stock, Price, Promotion FROM allopharma_articles WHERE Id='$a->i'");
          if( !$article ) continue;
          $article = toClass($article);
          //if( $article->Stock <= 0 ) continue;

          $article->secondaryPrice = $article->Price * (1 - ($article->Promotion/100));
          $article->OrderQuantity = $a->q;
          array_push($articles, $article);
        }

        if( count($articles) <= 0 ) {
          header("location: ".SITE_ROOT."404");
          exit;
        }
      ?>

      <section>

        <div class="flex-gallery">

          <aside>

            <form autocomplete="nope" method="post" id="checkout_form">

              <span class="ak-checkout">
                Déjà client? <a href="#">Connectez-vous</a>
              </span>

              <h2 class="ak-checkout">
                DÉTAILS DE FACTURATION
              </h2>

              <fieldset class="ak-small">
                <input type="text" name="lastName" pattern="^[a-zA-Z]{3,}$" required/>
                <legend>
                  Nom
                </legend>
              </fieldset>

              <fieldset class="ak-small">
                <input type="text" name="firstName" pattern="^[a-zA-Z]{3,}$" required/>
                <legend>
                  Prénom
                </legend>
              </fieldset>

              <fieldset>
                <input type="email" name="email" pattern="^[^\s@]+@[^\s@].[^\s]+$" required/>
                <legend>
                  Adresse Email
                </legend>
              </fieldset>

              <fieldset>
                <input type="text" name="phone" pattern="^[0-9]{10,10}$" required/>
                <legend>
                  Numéro de télèphone
                </legend>
              </fieldset>

              <fieldset>
                <textarea name="addressline1" required></textarea>
                <legend>
                  Adresse
                </legend>
              </fieldset>

              <fieldset>
                <select name="city" required>
                  <?php
                    foreach($cities as $i => $city) {
                      ?>
                      <option value="<?php print $city->id ?>"><?php print trim($city->ville) ?></option>
                      <?php
                    }
                  ?>
                  <option value="other">-- Autre --</option>
                </select>
                <legend>
                  Ville
                </legend>
              </fieldset>

              <h2 class="ak-checkout" style="border-top: 1px solid rgba(128 128 128 / 20%); padding-top: 1rem; margin-top: 2.5rem;">
                DÉTAILS DE PAIEMENT
              </h2>

              <fieldset>
                <input type="text" name="ccHolder" value="" placeholder="John Doe" required>
                <legend>
                  Titulaire
                </legend>
              </fieldset>

              <fieldset>
                <input type="text" name="ccNumber" value="" placeholder="4703 4444 4444 4449" required>
                <legend>
                  Numéro de la carte
                </legend>
              </fieldset>

              <fieldset class="ak-x-small">
                <input type="number" name="ccExpMonth" value="" min="1" max="12" maxlength="2" placeholder="04" required>
                <legend>
                  Mois (exp)
                </legend>
              </fieldset>

              <fieldset class="ak-x-small">
                <input type="number" name="ccExpYear" value="" min="21" placeholder="23" max="38" maxlength="2" required>
                <legend>
                  Année (exp)
                </legend>
              </fieldset>

              <fieldset class="ak-x-small">
                <input type="text" name="ccVC" value="" placeholder="123" maxlength="4" required>
                <legend>
                  CVC
                </legend>
              </fieldset>

              <button type="submit" style="display: none;"></button>

            </form>

          </aside>

          <aside class="ak-invoice">
            <h2>Votre commande</h2>
            <div class="invoice-wrapper">
              <li class="ak-head">
                <label>Produits</label>
                <span>Sous-total</span>
              </li>
              <?php
                $subTotal = 0;
                $deliveryCost = 0.5;
                foreach($articles as $i => $article) {
                  $subTotal += floatval($article->secondaryPrice * $article->OrderQuantity);
                  ?>
                  <li class="ak-article">
                    <label><a href="<?php print SITE_ROOT ?>product/<?php print $article->Id ?>/<?php print str_replace(" ", "-", $article->Title) ?>" target="_blank"><?php print $article->Title ?></a> ×<b><?php print $article->OrderQuantity ?></b></label>
                    <span><?php print formatPrice($article->secondaryPrice * $article->OrderQuantity) ?></span>
                  </li>
                  <?php
                }
              ?>
              <li>
                <label>Sous-total</label>
                <span><?php print formatPrice($subTotal) ?></span>
              </li>
              <li>
                <label>Expédition</label>
                <span><?php print formatPrice($deliveryCost) ?></span>
              </li>
              <li class="ak-total">
                <label>Total</label>
                <span><?php print formatPrice($subTotal + $deliveryCost) ?></span>
              </li>
            </div>

            <div class="checkbox-input">
              <input type="checkbox" onchange="nextBtn.disable = !this.checked">
              <label>
                <span>J'ai lu et j'accepte les <a href="#">conditions générales</a><font color="red">*</font></span>
              </label>
            </div>

            <button type="button" id="nextBtn" disabled class="ak-cuteBtn ak-blue">
              <span>
                Suivant <i class="far fa-arrow-right"></i>
              </span>
            </button>
          </aside>

        </div>

        <script type="text/javascript">
          (function(){
            if( typeof checkout_form != "object" || !checkout_form ) return;

            nextBtn.onclick = function() {
              checkout_form.querySelector("button[type='submit']").click();
            }

            checkout_form.onsubmit = function(e) {
              //this.submit();
            }
          }());
        </script>
      </section>

    </main>

  </body>
</html>
