<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php include __DIR__."/packages/incs/head" ?>
  <?php
    $Client->Disconnect();
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
            <a href="./logout">
              Déconnexion
            </a>
          </li>
        </ul>
      </div>

      <section class="ak-login" style="margin: 2rem auto;">

        <div class="login-panel">

          <form autocomplete="off" method="post">
            <h1>Au revoir!</h1>
            <p>
              Déconnexion en cours...
              <br>
              Veuillez patientez s'il vous plait!
            </p>
            <figure class="ak-block-loader"></figure>
            <script type="text/javascript">
              setTimeout(() => {
                window.location.href = `${location.rootHref}login`;
              }, 2000);
            </script>
          </form>
        </div>

      </section>

    </main>

    <?php require __DIR__."/packages/incs/footer" ?>

  </body>
</html>
