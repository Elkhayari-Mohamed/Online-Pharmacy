;(function(){

  const _ = {
    target: undefined,
    tooltip: undefined,
    closeLastOne: false,
    params: undefined,
    offsets: {
      x: 10,
      y: 10
    },


    open(params) {

      const needles = {
        text: null,
        styles: [],
        closingType: "onTargetLeave"
      }

      Object.keys(params).map(_ => needles[_] = params[_]);

      const template = this.createTemplate(`
        <figure class="ashTooltip ${needles.styles.map(_ => `ak-${_}`).join(" ")}">
          <span>
            ${needles.text}
          </span>
        </figure>
      `);

      this.tooltip = template;
      document.body.appendChild(this.tooltip);

      this.MoveTo = this.coords;

      this.params = needles;
      return this;
    },

    set MoveTo(coords) {
      this.tooltip.style.setProperty("left", `${coords.x}px`);
      this.tooltip.style.setProperty("top", `${coords.y}px`);
      return coords;
    },

    leave() {

      switch(this.params.closingType.toLowerCase()) {

        case "onanothertooltip":
          this.closeLastOne = true;
        break;

        case "never":
          console.warn("never closing type defined!");
        break;

        default:
          this.tooltip.remove();
        break;
      }
      return this;
    },


    createTemplate(content) {
      const template = document.createElement("div");
      template.innerHTML = content;
      return template.firstElementChild;
    },

    get coords() {
      const targetBounds = this.target.getBoundingClientRect();
      const tooltipBounds = this.tooltip.getBoundingClientRect();

      const togoCoords = {
        x: targetBounds.x + (targetBounds.width / 2) - (tooltipBounds.width / 2),
        y: targetBounds.y + targetBounds.height + this.offsets.y
      }

      return togoCoords;
    }

  }

  document.addEventListener("mouseover touchstart", e => {
    document.querySelectorAll("figure.ashTooltip").forEach(_ => _.remove());

    if(_.tooltip && _.target.isConnected !== true) _.tooltip.remove();
    const target = e.target;
    if( !e.isTrusted || typeof target.dataset.ashTooltip == "undefined" ) return;

    if( _.closeLastOne ) {
      _.tooltip.remove();
      _.closeLastOne = false;
    }


    _.target = target;

    const params = {
      text: target.dataset.ashTooltip,
      styles: target.dataset.ashTooltipStyles ? target.dataset.ashTooltipStyles.split(' ') : [],
      closingType: target.dataset.ashTooltipClosingtype ? target.dataset.ashTooltipClosingtype : "onTargetLeave"
    }

    _.open(params);

    if( target.ashTooltipInitialized ) return;

    target.addEventListener("mouseleave touchend" , () => _.leave());

    target.ashTooltipInitialized = true;
  });

  window.addEventListener("scroll resize", () => {
    if( !_.tooltip ) return;
    _.MoveTo = _.coords;
  });

}());
