<?php

  function fetchArticle($id) {
    global $SqlConnection;
    $article = $SqlConnection->Fetch("SELECT * FROM allopharma_articles WHERE Id='$id'");
    if( !$article ) return false;
    $article->Price = round($article->Price * EURO_MAD_RATIO);
    $article->Description = str_replace("\\", "", base64_decode($article->Description));
    return $article;
  }

  $article = 0;
  if( isset($_GET->id) && intval($_GET->id) ) $article = intval($_GET->id);
  $article = fetchArticle($article);

  if( !$article ) {
    echo "Error";
    exit;
  }


  if( count($_POST) > 0 ) {
    $requiredPOSTS = ["Title", "description", "Type", "Quantity", "Unit", "Price", "Promotion", "Stock", "Category", "Brand", "Provider"];

    $requiredPOSTS_PASSES = true;
    foreach($requiredPOSTS as $key) {
      if(!isset($_POST[$key])) {
        $requiredPOSTS_PASSES = false;
        break;
      }
    }

    if( $requiredPOSTS_PASSES && isset($_FILES['illustrationCSV']) ) {
      $input = toClass($_FILES['illustrationCSV']);
      $files = [];

      $allowedExtensions = ["jpg", "jpeg", "png", "bmp", "gif", "svg"];

      for($i=0; $i<count($input->name); $i++) {
        $file = toClass([
          "name" => $input->name[$i],
          "tmp_name" => $input->tmp_name[$i],
          "size" => $input->size[$i],
          "extension" => strtolower(pathinfo($input->name[$i], PATHINFO_EXTENSION)),
          "tmp_identifier" => randomToken(8)."-".time()
        ]);
        if( !in_array($file->extension, $allowedExtensions) ) continue;

        array_push($files, $file);
      }

      $_POST = toClass($_POST);

      $illustrationCSV = $_POST->illustrations;
      foreach($files as $i => $file) {
        if( strlen($illustrationCSV) > 0 ) $illustrationCSV .= ", ";
        $CSV = "assets/dynamics/products/$file->tmp_identifier-$file->name";

        $illustrationCSV .= SITE_ROOT."../$CSV";

        $uploadDir = __DIR__."/../../..";
        move_uploaded_file($file->tmp_name, "$uploadDir/$CSV");
      }


      $_POST->Price = floatval($_POST->Price) / EURO_MAD_RATIO;
      $_POST->Promotion = floatval($_POST->Promotion);
      $_POST->Title = $_POST->Title;
      $_POST->Description = base64_encode($_POST->description);

      $now = date("Y-m-d H:i:s");

      $query = "UPDATE allopharma_articles SET IllustrationCSV='$illustrationCSV', Title='$_POST->Title', Description='$_POST->Description', Quantity='$_POST->Quantity', Unit='$_POST->Unit', Type='$_POST->Type', Price='$_POST->Price', Promotion='$_POST->Promotion', Provider='$_POST->Provider', Stock='$_POST->Stock', Category='$_POST->Category', Brand='$_POST->Category' WHERE Id='$article->Id'";

      $res = $SqlConnection->Query($query);

      if( $res ) $article = fetchArticle($article->Id);
    }

  }

?>


<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="https://cdn.quilljs.com/1.3.6/quill.bubble.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js" onload="console.warn('Quilljs is running...')"></script>
<script type="text/javascript">
  window.$_POST = <?php print json_encode($_POST) ?>;
  window.$_FILES = <?php print json_encode($_FILES) ?>;
  window.$_GET = <?php print json_encode($_GET) ?>;
  var article = <?php print json_encode($article) ?>;
</script>

<div class="offwhite-box">
  <a onclick="history.back()" class="ak-cuteBtn ak-blue ak-alphaModed" style="max-width: fit-content; margin-bottom: 1rem;">
    <span>
      <i class="far fa-arrow-left"></i>
      Revenir
    </span>
  </a>
  <h2>Modifier d'un produit</h2>
  <p>
    Vous pouvez modifier autant de fois que vous voullez.
  </p>

  <menu class="ak-product-creation">

    <form method="post" enctype="multipart/form-data">
      <textarea name="illustrations" style="display: none;"></textarea>
      <textarea name="description" style="display: none;"></textarea>
      <section class="illustrations-area">
        <div class="illustrations-drop-area">
          <section>
            <i class="far fa-trash ak-red" dropzone="move" id="removeIllustration"></i>
          </section>
        </div>
        <figure id="addIllutration">
          <i class="far fa-plus"></i>
          <input type="file" name="illustrationCSV[]" data-ash-tooltip="Ajouter une photo du produit" draggable="false" accept="image/*" multiple>
          <script type="text/javascript">
            (function(){
              const prototype = document.currentScript.parentNode;
              const fileInput = prototype.querySelector("input[type='file']");

              let form = prototype;
              while(form != document.body && form.tagName != "FORM") form = form.parentNode;

              let illustrationsField = form.elements.illustrations;

              fileInput.onchange = function(e, files) {
                if( !files ) files = this.files;

                var i = 0;
                var startIndex = prototype.parentNode.querySelectorAll("figure:not(#addIllutration)").length;
                for(let file of files) {
                  file.data = "";
                  file.canUpload = true;
                  file.blobSrc = file.blobSrc ? file.blobSrc : null;

                  const clone = document.createElement("figure");
                  clone.draggable = true;
                  clone.dataset.ashTooltip = "Maintenez et glissez pour retirer";
                  clone.dataset.index = startIndex + i;
                  clone.base64Content = "";

                  clone.ondragstart = function(e) {
                    window.dropData = {
                      illustrationFigure: this,
                      file: file
                    }

                    this.classList.add("dragging");
                    prototype.parentNode.classList.add("dragging");
                  }
                  clone.ondragend = function() {
                    this.classList.remove("dragging");
                    prototype.parentNode.classList.remove("dragging");
                  }


                  clone.img = new Image();
                  clone.img.alt = `Illustration_${clone.dataset.index}`;

                  try {
                    clone.img.src = URL.createObjectURL(file);
                  }
                  catch(err) {
                    clone.img.src = file.blobSrc;
                  }
                  clone.img.draggable = false;
                  clone.img.onload = function() {
                    URL.revokeObjectURL(this.src);
                    file.blobSrc = this.src;
                  }

                  clone.appendChild(clone.img);
                  prototype.parentNode.appendChild(clone);

                  i++;
                }
              }

              if( typeof removeIllustration == "object" && removeIllustration ) {
                removeIllustration.ondragover = function(e) {
                  e.preventDefault();
                  this.classList.add('dragover');
                }
                removeIllustration.ondragleave = function() {
                  this.classList.remove('dragover');
                }
                removeIllustration.ondrop = function(e) {
                  e.preventDefault();
                  if( typeof window.dropData == "undefined" || !window.dropData ) return;
                  dropData.file.canUpload = false;

                  var chneeks = illustrationsField.value.split(', ').filter( _ => _.toString().trim().replace("dashboard/../", "") != dropData.file.blobSrc.toString().trim() );
                  illustrationsField.value = chneeks.join(", ");
                  dropData.illustrationFigure.remove();
                }
              }


              var absoluteFiles = [];
              article.IllustrationCSV.split(', ').map(_ => {
                absoluteFiles.push({
                  blobSrc: _
                });
              });

              fileInput.onchange.apply(fileInput, [window.event, absoluteFiles]);
            }())
          </script>
        </figure>
      </section>
      <div class="flex-gallery">
        <aside class="ak-form-suportive">
          <fieldset>
            <input type="text" name="Title" value="" required>
            <legend>Nom du produit</legend>
          </fieldset>
          <fieldset>
            <input type="text" name="Type" list="types" value="" required>
            <legend>Type de produit</legend>
            <datalist id="types">
              <option value="Eau pour le visage">Eau pour le visage</option>
              <option value="Protection de la bouche">Protection de la bouche</option>
            </datalist>
          </fieldset>
          <fieldset class="ak-small">
            <input type="number" name="Quantity" value="0" required>
            <legend>Quantité de produit</legend>
          </fieldset>
          <fieldset class="ak-small">
            <input type="text" name="Unit" value="" required>
            <legend>Unité (E.g: Kg, ml)</legend>
          </fieldset>
        </aside>

        <aside class="ak-form-suportive">
          <fieldset>
            <input type="number" name="Price" value="" required>
            <legend>Prix (Dhs)</legend>
          </fieldset>
          <fieldset>
            <input type="text" name="Promotion" max="100" min="0" value="0" required>
            <legend>Réduction (en %)</legend>
          </fieldset>
          <fieldset>
            <input type="number" name="Stock" value="" required>
            <legend>Stock</legend>
          </fieldset>
        </aside>

        <aside class="ak-form-suportive">
          <fieldset>
            <select name="Category" required>
              <?php
                foreach($SqlConnection->FetchAll("SELECT Id, Name FROM allopharma_categories ORDER BY CreationDate") as $i => $category) {
                  ?>
                  <option value="<?php print $category->Id ?>"><?php print trim(htmlentities($category->Name)) ?></option>
                  <?php
                }
              ?>
            </select>
            <legend>Catégorie</legend>
          </fieldset>
          <fieldset>
            <input type="text" name="Brand" list="brands" value="">
            <legend>Marque</legend>
            <datalist id="brands">
              <?php
              foreach($SqlConnection->FetchAll("SELECT Id, Name FROM allopharma_brands ORDER BY CreationDate") as $i => $brand) {
                ?>
                <option value="<?php print trim(htmlentities($brand->Name)) ?>"><?php print trim(htmlentities($brand->Name)) ?></option>
                <?php
              }
              ?>
            </datalist>
          </fieldset>
          <fieldset>
            <input type="text" name="Provider" list="providers" value="">
            <legend>Forunisseur</legend>
            <datalist id="providers">
              <?php
              foreach($SqlConnection->FetchAll("SELECT Provider FROM allopharma_articles ORDER BY CreationDate") as $i => $article) {
                ?>
                <option value="<?php print trim(htmlentities($article->Provider)) ?>"><?php print trim(htmlentities($article->Provider)) ?></option>
                <?php
              }
              ?>
            </datalist>
          </fieldset>
        </aside>
      </div>
      <figure style="width: 100%">
        <div id="quilljsRenderer"></div>
        <script type="text/javascript">
          Quill.prototype.getHTML = function(){
            return this.root.innerHTML.trim();
          }
          var quilljsEditor = new Quill('#quilljsRenderer', {
            modules: {
              toolbar: toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{'header': 1}, {'header': 2}],
                [{'list': 'ordered'}, {'list': 'bullet'}],
                [{'script': 'sub'}, {'script': 'super'}],
                [{'indent': '-1'}, {'indent': '+1'}],
                [{'direction': 'rtl'}],
                [{'size': ['small', false, 'large', 'huge']}],
                ['link', 'image', 'video', 'formula'],
                [{'color': []}, {'background': []}],
                [{'font': []}],
                [{'align': []}]
              ]
            },
            placeholder: "Rédiger une description qui convient le produit...",
            readOnly: false,
            theme: 'snow'
          });

        </script>
      </figure>

      <br><br>
      <button type="submit" class="ak-cuteBtn ak-blue" style="margin-top: 1rem;">
        <span>Sauvegarder</span>
      </button>

      <script type="text/javascript">
        (function(){
          const form = document.currentScript.parentNode;
          form.addEventListener("submit", function(e) {
            e.preventDefault();

            let files = this.querySelector("input[type='file']").files;
            var countFiles = this.elements.illustrations.value.split(", ").length;
            Object.entries(files).map(([key, file]) => countFiles += file.canUpload ? 1 : 0);

            if( countFiles <= 0 ) return alert("Veuillez sélectionner au moins 1 photo pour ce produit.");

            this.elements.description.value = quilljsEditor.getHTML();

            this.submit();
          });

          Object.entries(article).map(([key, value]) => {
            if( key == "Description" ) quilljsEditor.root.innerHTML = value.toString().trim();
            if( key == "IllustrationCSV" ) form.elements.illustrations.value = value.toString().trim();

            if( !form.elements.hasOwnProperty(key) ) return;
            form.elements[key].value = value;
          });
        }());
      </script>
    </form>
  </menu>
</div>
