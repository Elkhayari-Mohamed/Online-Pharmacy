(async function(){

  window.Alert = window.alert;
  window.Confirm = window.confirm;
  window.Prompt = window.prompt;

  var promptsTemplate = `
    <figure class="blended-prompts">
      <header>
        <label>
          {title}
        </label>
        <button type="button" data-actor="close">
          <span>
            <i class="fal fa-times"></i>
          </span>
        </button>
      </header>
      <main>
        <p data-actor="message">{message}</p>
        <form autocomplete="off" method="post" data-actor="form">
          <input type="text" name="response" data-actor="response" placeholder="Votre réponse..." tabindex="0" />
          <section>
            <button type="button" data-actor="cancel">
              <span>
                {cancel_text}
              </span>
            </button>
            <button type="submit">
              <span>
                {ok_text}
              </span>
            </button>
          </section>
        </form>
      </main>
    </figure>
  `;
  const request = async function(opts, onReady=undefined) {
    if( document.querySelectorAll("figure.blended-prompts").length > 0 ) return;

    const buffers = {
      title: "",
      message: "",
      type: "default",
      cancel_text: "Annuler",
      ok_text: "OK"
    };

    if( typeof opts == "object" ) Object.entries(opts).map(_ => buffers[_[0]] = _[1]);
    else buffers.message = opts;

    switch(buffers.type) {
      case "error":
        buffers.title = `<i class="fad fa-times-circle" style="color: #ff7272;"></i> ${buffers.title}`;
      break;
      case "warning":
        buffers.title = `<i class="fad fa-exclamation-triangle" style="color: #ff7600;"></i> ${buffers.title}`;
      break;
      case "success":
        buffers.title = `<i class="fad fa-check-circle" style="color: #4caf50;"></i> ${buffers.title}`;
      break;
    }


    let template = promptsTemplate;
    Object.entries(buffers).map(_ => template = template.replaceAll(`{${_[0]}}`, _[1]));

    return new Promise((resolve, reject) => {
      template.toTemplate(async function() {
        let _ = this;

        this.actors.close.onclick = () => this.remove();
        this.actors.cancel.onclick = () => {
          this.actors.response.value = "";
          this.actors.form.dispatchEvent(new Event("submit"));
        };
        this.actors.message.onclick = () => {
          if( !this.actors.response ) return;
          this.actors.response.focus();
        };
        this.actors.form.onsubmit = async function(e) {
          e.preventDefault();
          var result = typeof this.data.response != "undefined" ? this.data.response : "";
          resolve(result);
          _.actors.close.click();
          return false;
        }

        if( typeof onReady == "function" ) onReady.apply(this, [template]);
        this.appendTo(document.body);
      });
    });
  }

  window.alert = async opts => await request(opts, function(tmp) {
    this.actors.cancel.remove();
    this.actors.response.remove();
  });
  window.confirm = async opts => await request(opts, function(tmp) {
    this.actors.response.value = 1;
    this.actors.response.style.display = "none";
  });;
  window.prompt = async opts => await request(opts);
}());
