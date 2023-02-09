;(function(){
  Element.prototype._OriginaladdEventListenerFunction = Element.prototype.addEventListener;
  Element.prototype.addEventListener = addEvent;
  Document.prototype._OriginaladdEventListenerFunction = Document.prototype.addEventListener;
  Document.prototype.addEventListener = addEvent;
  Window.prototype._OriginaladdEventListenerFunction = Window.prototype.addEventListener;
  Window.prototype.addEventListener = addEvent;

  function addEvent(events, callback) {
    events = events.split(' ');
    for(let ev of events) {
      this._OriginaladdEventListenerFunction(ev, callback, {passive: false});
    }
  }

  if( Event.prototype.hasOwnProperty("ashPath") !== true ) {
    Object.defineProperty(Event.prototype, "ashPath", {
      get: function(){
        var path = [];
        var el = this.target;
        while( el != document.body ) {
          el = el.parentNode;
          path.push(el);

          if( el == document.documentElement ) {
            path.push(document);
            path.push(window);
          }
        }
        return path;
      },
      enumerable : true
    });

    Event.prototype.inPath = function(element) {
      for(let _ of this.ashPath) {
        if(_ == element) return _;
      }
      return false;
    }

    Event.prototype.pathHasTag = function(tag) {
      for(let _ of this.ashPath) {
        if(_.tagName && _.tagName == tag.toUpperCase()) return _;
      }
      return false;
    }
  }

  console.warn("ashQuery demo is running...");
})();
