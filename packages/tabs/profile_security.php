<div class="flex-gallery">
  <aside>
    <div class="offwhite-box">
      <h2>Compte & Sécurité</h2>
      <p>
        Vos informations sont privés et bien sécurisés, vous êtes le seule autorisé à les voir et modifier.
      </p>

      <menu>
        <form method="post">
          <fieldset>
            <input type="password" name="pasword" value="" required>
            <legend>Mot de passe actuel</legend>
          </fieldset>
          <fieldset>
            <input type="password" name="newPasword" value="" required>
            <legend>Nouveau mot de passe</legend>
          </fieldset>
          <fieldset>
            <input type="password" name="newPasword2" value="" required>
            <legend>Confirmer le mot de passe</legend>
          </fieldset>

          <button type="submit" class="ak-cuteBtn ak-blue">
            <span>
              Enregistrer
            </span>
          </button>
        </form>
      </menu>
    </div>
  </aside>
  <aside>
    <div class="offwhite-box">
      <h2>Historique de connexions</h2>
      <p>
        A chaque fois que vous connectez à ce compte, notre système collécte quelques informations de cette dernière afin de vous mieux protéger.
      </p>

      <menu>
        <table border=0 cellpadding=0 cellspacing=0>
          <tr>
            <th>Token</th>
            <th>Date & heure</th>
          </tr>
          <?php
            $count  = 0;
            foreach($SqlConnection->FetchAll("SELECT Token, CreationDate FROM allopharma_accounts_sessions WHERE ClientAccount='$Client->id' ORDER BY CreationDate DESC LIMIT 0,25") as $i => $connection) {
              $count = $i + 1;
              ?>
              <tr>
                <td><?php print substr($connection->Token, 0, 20) ?>...</td>
                <td><?php print dateToString($connection->CreationDate) ?></td>
              </tr>
              <?php
            }
          ?>
        </table>
        <?php
          if( $count <= 0 ) {
            ?>
            <p style="text-align: center">Aucune connexion n'a été enregistrée.</p>
            <?php
          }
        ?>
      </menu>
    </div>
  </aside>
</div>
