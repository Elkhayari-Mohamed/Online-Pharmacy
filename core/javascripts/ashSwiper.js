class ASHSwiper {

  constructor(node, parameters=false, events=false) {
    // handling parameters
    this.parameters = {
      canSwipe: true,
      treshold: .5,
      allowHorizontal: false,
      behavior: "smooth",
      for: "*"
    }
    if( parameters && typeof parameters == "object" ) Object.keys(parameters).forEach(_ => {
      this.parameters[_] = parameters[_];
    });

    // Handling events
    this.events = {
      swipestart: undefined,
      swipeend: undefined,
      swipemove: undefined,
      change: undefined,
      ready: undefined
    }
    if( events && typeof events == "object" ) Object.keys(events).forEach(_ => {
      this.events[`on${_}`] = events[_];
    });

    // Basics
    this.rootNode = node;
    this.steps = this.rootNode.querySelectorAll(this.parameters.for);
    this.currentStep = 0;
    this.initialized = false;

    // Swipe needles
    this.swipping = false;
    this.startCoords = {x: undefined, y: undefined};
    this.movingCoords = {x: undefined, y: undefined};
    this.startOffsets = {x: 0, y: 0};


    // Runtime
    this.Init()
    .Debug();

    return this;
  }

  Init() {

    this.rootNode.addEventListener("mousedown touchstart", e => {
      if( !e.changedTouches || !e.changedTouches[0] ) e.changedTouches = [{clientX: e.clientX, clientY: e.clientY}];

      if( !this.parameters.canSwipe ) return;

      this.trustedSwipe = true;

      this.startCoords = {
        x: e.changedTouches[0].clientX,
        y: e.changedTouches[0].clientY * (this.parameters.allowHorizontal ? 1 : 0)
      }

      // get offsets
      if( this.rootNode.style.transform == "" ) {
        this.rootNode.style.setProperty("transform", "translateX(0) translateY(0)");
      }
      this.startOffsets = {
        x: parseFloat(this.rootNode.style.transform.split(' ')[0].substr(11, 1000).replace("px)", "")),
        y: parseFloat(this.rootNode.style.transform.split(' ')[1].substr(11, 1000).replace("px)", ""))
      }
      this.startOffsets.x = isNaN(this.startOffsets.x) ? 0 : this.startOffsets.x;
      this.startOffsets.y = isNaN(this.startOffsets.y) ? 0 : this.startOffsets.y;

      this.swipping = true;
      this.rootNode.classList.add("_swipping");

      // dispatch the current event
      this.dispatchEvent("swipestart", e);
    });

    window.addEventListener("mouseup touchend", e => {
      if( !this.trustedSwipe ) return;


      this.rootNode.classList.remove("_swipping");

      let difference = {
        x: {
          px: this.movingCoords.x - this.startCoords.x,
          treshold: Math.abs((this.movingCoords.x - this.startCoords.x) / this.rootNode.offsetWidth)
        },
        y: {
          px: this.movingCoords.y - this.startCoords.y,
          treshold: Math.abs((this.movingCoords.y - this.startCoords.y) / this.rootNode.offsetHeight)
        }
      }

      let direction = {
        x: difference.x.px != 0 ? difference.x.px > 0 ? 1 : -1 : 0,
        y: difference.y.px != 0 ? difference.y.px > 0 ? 1 : -1 : 0
      }

      // check if the swipe conditions are all verified
      let swipes = {
        x: Math.abs(difference.x.treshold) >= this.parameters.treshold ? direction.x : 0,
        y: Math.abs(difference.y.treshold) >= this.parameters.treshold ? direction.y : 0
      }


      if( !swipes.x && !swipes.y ) this.GoTo(this.currentStep, true);
      if( swipes.x ) swipes.x == -1 ? this.Next() : this.Previous();
      if( swipes.y ) swipes.y == -1 ? this.Next() : this.Previous();

      // dispatch the current event
      if( this.swipping ) this.dispatchEvent("swipeend", e);

      this.swipping = false;
      this.trustedSwipe = false;
    });

    window.addEventListener("mousemove touchmove", e => {
      if( !e.changedTouches || !e.changedTouches[0] ) e.changedTouches = [{clientX: e.clientX, clientY: e.clientY}];
      if( !this.swipping ) return;

      this.movingCoords = {
        x: e.changedTouches[0].clientX,
        y: e.changedTouches[0].clientY * (this.parameters.allowHorizontal ? 1 : 0)
      };

      let smoothedCoords = {
        x: (this.movingCoords.x - this.startCoords.x) + this.startOffsets.x,
        y: (this.movingCoords.y - this.startCoords.y) + this.startOffsets.y
      }


      if( this.parameters.behavior == "smooth" ) {
        var smoothifyRange = 5;
        var smoothify = {
          x: smoothedCoords.x < 0 ? 1 : 5,
          y: smoothedCoords.y < 0 ? 1 : 5
        }

        this.rootNode.style.setProperty('transform', `translateX(${smoothedCoords.x / smoothify.x}px) translateY(${smoothedCoords.y / smoothify.y}px)`);
      }

      // dispatch the current event
      this.dispatchEvent("swipemove", e);
    });


    window.addEventListener("resize", () => {
      var width = this.rootNode.offsetWidth;
      var height = this.rootNode.offsetHeight;
      this.SlideTo(width * -this.currentStep, height * -this.currentStep);
    });


    this.initialized = true;
    return this;
  }

  Debug() {
    this.rootNode.ashSwiper = this;
    // dispatch the current event
    this.dispatchEvent("ready");
    return this;
  }

  Next(emulated=false) {
    this.GoTo(this.currentStep + 1, emulated);
    return this;
  }

  Previous(emulated=false) {
    this.GoTo(this.currentStep - 1, emulated);
    return this;
  }

  GoTo(index, emulated=false) {
    if( index < 0 || index >= this.steps.length ) return this.GoTo(index < 0 ? 0 : this.steps.length - 1);


    let currentStep = this.currentStep;
    this.currentStep = parseFloat(index);

    var width = this.rootNode.offsetWidth;
    var height = this.rootNode.offsetHeight;
    this.SlideTo(width * -index, height * -index);

    // dispatch the current event
    if( !emulated && index != currentStep ) this.dispatchEvent("change");

    return this;
  }

  SlideTo(x, y) {
    if( !this.parameters.allowHorizontal ) y *= 0;

    this.rootNode.style.setProperty("transform", `translateX(${x}px) translateY(${y}px)`);
    return this;
  }

  dispatchEvent(name, e=false) {
    if( typeof this.events[`on${name}`] == "function" ) this.events[`on${name}`].apply(this, [e]);
    return this;
  }

}
