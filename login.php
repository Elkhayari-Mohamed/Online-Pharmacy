<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php include __DIR__."/packages/incs/head" ?>
  <?php
    $_POST = toClass($_POST);
    $_GET = toClass($_GET);
    if( isset($_POST->login) && isset($_POST->password) )
      $connexion = $Client->Connect(
        $_POST->login,
        $_POST->password
      );
  ?>
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
            if( isset($connexion) ) {
              if( $connexion ) {
                switch($connexion->Status) {
                  case 0:
                    ?>
                    <figure class="login-alert ak-success">
                      <i class="fad fa-check-circle"></i>
                      <span>
                        Vous êtes bien connecté, redirection en cours...
                      </span>
                    </figure>
                    <script type="text/javascript">
                      (function(){
                        setTimeout(() => window.location.href = "./profile" , 2500);
                      }());
                    </script>
                    <?php
                  break;

                  default:
                    ?>
                    <figure class="login-alert ak-warning">
                      <i class="fad fa-exclamation-circle"></i>
                      <span>
                        Ce compte est suspendu.
                      </span>
                    </figure>
                    <?php
                  break;
                }
              }
              else {
                ?>
                <figure class="login-alert ak-error">
                  <i class="fad fa-times-circle"></i>
                  <span>
                    Les informations entrés sont invalides.
                  </span>
                </figure>
                <?php
              }
            }
          ?>

          <form autocomplete="off" method="post">
            <h1>Authentification</h1>
            <p>
              Seulent les clients avec des comptes peuvent profiter de promotions et de plusieurs exclusivités.
            </p>
            <fieldset>
              <input type="text" name="login" value="<?php print isset($_GET->fast) ? $_GET->fast : '' ?>">
              <legend>Adresse Email</legend>
            </fieldset>
            <fieldset>
              <input type="password" name="password" value="">
              <legend>Mot de passe</legend>
            </fieldset>
            <button type="submit" class="ak-cuteBtn ak-blue">
              <span>
                Connexion
              </span>
            </button>
            <span class="ak-login-href">
              <a href="./recover_password">
                Mot de passe oublié?
              </a>
            </span>
            <figure class="ak-seprator">Vous n'avez pas de compte?</figure>
            <a href="./signup" role="button" class="ak-cuteBtn ak-orange ak-alphaModed">
              <span>
                Créer un compte
              </span>
            </a>
          </form>
        </div>

      </section>

    </main>

    <?php require __DIR__."/packages/incs/footer" ?>

  </body>
</html>
