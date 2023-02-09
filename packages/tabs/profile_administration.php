<section class="ak-login" style="margin: 2rem auto;">

  <div class="login-panel">

    <form autocomplete="off" method="post">
      <h1>Préparation!</h1>
      <p>
        Redirection en cours...
        <br>
        Veuillez patientez s'il vous plait!
      </p>
      <figure class="ak-block-loader"></figure>
      <script type="text/javascript">
        setTimeout(() => {
          window.location.href = `${location.rootHref}dashboard`;
        }, 2000);
      </script>
    </form>
  </div>

</section>
