<div class="offwhite-box">
  <h2>Informations générales</h2>
  <p>
    Vos informations sont privés et bien sécurisés, vous êtes le seule autorisé à les voir et modifier.
  </p>

  <menu>
    <?php
      $infos = $Client->get;

      $civlities = ["Mr", "Mme"];
    ?>
    <li>
      <label>Email</label>
      <span><?php print $infos->Login ?></span>
    </li>
    <li>
      <label>Civilité/Nom complet</label>
      <?php $civility = isset($civlities[$infos->Civility]) ? $civlities[$infos->Civility] : ""; ?>
      <span><?php print "$civility.$infos->LastName $infos->FirstName" ?></span>
    </li>
    <li>
      <label>Numéro de téléphone</label>
      <span><?php print "$infos->PhoneCode-$infos->PhoneNumber" ?></span>
    </li>
    <li>
      <label></label>
      <span>
        <a href="<?php print SITE_ROOT ?>contact" class="ak-cuteBtn ak-blue ak-rounded">
          <span>
            <i class="far fa-envelope"></i>
            Contactez-nous
          </span>
        </a>
      </span>
    </li>
  </menu>
</div>
