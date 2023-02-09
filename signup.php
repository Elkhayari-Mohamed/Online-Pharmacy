<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php include __DIR__."/packages/incs/head" ?>
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
              Profile
            </a>
          </li>
          <li>
            <a href="./login">
              Connexion
            </a>
          </li>
        </ul>
      </div>

      <section class="ak-login" style="margin: 2rem auto;">

        <div class="login-panel">
          <?php

            $_POST = toClass($_POST);

            $error = null;
            if(
              isset($_POST->firstName) &&
              isset($_POST->lastName) &&
              isset($_POST->civility) &&
              isset($_POST->login) &&
              isset($_POST->password) &&
              isset($_POST->password2) &&
              isset($_POST->phone_code) &&
              isset($_POST->phone_number)
            ) {

              $feeds = toClass([
                "Login" => $_POST->login,
                "Password" => md5($_POST->password),
                "FirstName" => $_POST->firstName,
                "LastName" => $_POST->lastName,
                "CreationDate" => date("Y-m-d H:i:s"),
                "Civility" => $_POST->civility,
                "PhoneCode" => $_POST->phone_code,
                "PhoneNumber" => $_POST->phone_number
              ]);

              if( $_POST->password == $_POST->password2 ) {

                if( strlen($_POST->password) >= 6 && strlen($_POST->password) <= 30 ) {

                  if( strlen(trim($_POST->firstName)) > 0 && strlen(trim($_POST->firstName)) <= 30 ) {

                    if( strlen(trim($_POST->lastName)) > 0 && strlen(trim($_POST->lastName)) <= 30 ) {

                      if( strlen(trim($_POST->phone_number)) >= 9 ) {

                        if( !$Client->getBy(["Login" => $feeds->Login]) ) {

                          $Client->Register($feeds);
                          $Client->Connect(
                            $feeds->Login,
                            $feeds->Password
                          );

                        }
                        else $error = "Cette adresse email n'est pas disponible.";

                      }
                      else $error = "Veuillez insérer un numéro de téléphone valid.";

                    }
                    else $error = "Veuillez insérer un prénom valid.";

                  }
                  else $error = "Veuillez insérer un prénom valid.";

                }
                else $error = "Le mot de passe doît contenir au moins 6 caractères, et 30 au maximum.";

              }
              else $error = "Les deux mot de passes entrés ne sont pas corréspondants.";


              if( $error !== null ) {
                ?>
                <figure class="login-alert ak-error">
                  <i class="fad fa-times-circle"></i>
                  <span>
                    <?php print $error ?>
                  </span>
                </figure>
                <?php
              }
              else {
                ?>
                <figure class="login-alert ak-success">
                  <i class="fad fa-check-circle"></i>
                  <span>
                    Félicitation, vous vennez juste de crée un nouveau compte avec succès.
                  </span>
                </figure>
                <script type="text/javascript">
                  (function() {
                    setTimeout(() => window.location.href = './login?fast=<?php print $_POST->login ?>', 2500);
                  }());
                </script>
                <?php
              }
            }


          ?>

          <form method="post">
            <h1>Inscription</h1>
            <p>
              Créez un compte gratuitement et profitez des exclusivités dans chaque commande.
            </p>

            <fieldset>
              <input type="text" name="login" value="" required>
              <legend>Adresse Email</legend>
            </fieldset>
            <fieldset class="ak-x-small">
              <select name="civility" required>
                <option value="0">Mr.</option>
                <option value="1">Mme.</option>
                <option value="2">Ne pas préciser</option>
              </select>
              <legend>Civilité</legend>
            </fieldset>
            <fieldset class="ak-x-small">
              <input type="text" name="lastName" value="" required>
              <legend>Nom</legend>
            </fieldset>
            <fieldset class="ak-x-small">
              <input type="text" name="firstName" value="" required>
              <legend>Prénom</legend>
            </fieldset>
            <fieldset>
              <input type="password" name="password" value="" required>
              <legend>Mot de passe</legend>
            </fieldset>
            <fieldset>
              <input type="password" name="password2" value="" required>
              <legend>Confirmer le mot de passe</legend>
            </fieldset>
            <fieldset class="ak-small">
              <input type="text" name="phone_code" placeholder="+212" value="+212" required readonly>
              <legend>Code</legend>
            </fieldset>
            <fieldset class="ak-small">
              <input type="text" name="phone_number" required>
              <legend>Téléphone</legend>
            </fieldset>
            <button type="submit" class="ak-cuteBtn ak-blue">
              <span>
                Terminer
              </span>
            </button>
            <span class="ak-login-href">
              <span>Vous rencontrez des problèmes?</span>
              <a href="./contact" style="margin-left: 4px; color: #2188ff;">
                Contactez-nous
              </a>
            </span>
            <figure class="ak-seprator">Vous avez déjà un compte?</figure>
            <a href="./login" role="button" class="ak-cuteBtn ak-orange ak-alphaModed">
              <span>
                Connectez-vous
              </span>
            </a>
          </form>
        </div>

      </section>

    </main>

    <?php require __DIR__."/packages/incs/footer" ?>

  </body>
</html>
