<div class="flex-gallery">
  <aside>
    <div class="offwhite-box">
      <h2>Mes notifications</h2>
      <p>
        Les notifications sont des petites alerts avec lesquelles notre système communique et transfère tous ce qui est en rapport avec vos commande, favoris et nouveautés.
      </p>

      <menu>
        <div class="flex-table ak-clickablerows" id="cartTable">
          <section class="ak-head">
            <aside class="ak-stretch">

            </aside>
            <aside>
              <button type="button" class="ak-uncart">
                <i class="far fa-ellipsis-h"></i>
              </button>
            </aside>
          </section>
          <?php
            $count = 0;
            foreach($Client->getNotifications()->All as $i => $notification) {
              ?>
              <section data-notification-id="<?php print $notification->Id ?>" data-clickable <?php if( $notification->Opened ) print 'data-opened="'.dateToString($notification->OpenedAt).'"' ?>>
                <aside class="ak-stretch">
                  <span class="ak-name">
                    <?php print htmlentities($notification->Title) ?>
                  </span>
                  <span>
                    <?php print substr(htmlentities($notification->Content), 0, 250) ?>...
                  </span>
                </aside>
                <aside>
                  <button type="button" class="ak-uncart">
                    <i class="far fa-ellipsis-v"></i>
                  </button>

                  <figure class="context-menu">
                    <button onclick="window.location.href = `${location.rootHref}profile/notification/<?php print $notification->Id ?>`">
                      <i class="far fa-book-reader"></i>
                      <span>Ouvrir</span>
                    </button>
                    <button onclick="deleteNotification(<?php print $notification->Id ?>)">
                      <i class="far fa-trash"></i>
                      <span>Supprimer</span>
                    </button>
                  </figure>
                </aside>
              </section>
              <?php
            }
          ?>

          <script type="text/javascript">
            (function(){
              document.currentScript.parentNode.querySelectorAll("[data-notification-id][data-clickable]").forEach(_ => {
                _.onclick = function(e) {
                  if( e.target != this ) return;
                  window.location.href = `${location.rootHref}profile/notification/${this.dataset.notificationId}`;
                }

                const contextMenu = _.querySelector("figure.context-menu");

                document.addEventListener("click", function(e) {
                  if( !contextMenu.classList.contains("open") ) return;

                  if( !e.inPath(contextMenu) || e.target.tagName != "BUTTON" ) {
                    contextMenu.classList.remove("open");
                  }
                  const btn = e.target;

                  contextMenu.classList.remove("open");
                });

                _.querySelector("button.ak-uncart").onclick = function() {
                  setTimeout(() => {
                    contextMenu.classList.add("open");
                  }, 10);
                }
              });

            }());
          </script>
        </div>
      </menu>
    </div>
  </aside>
</div>
