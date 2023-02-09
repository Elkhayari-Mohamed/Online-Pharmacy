Object.defineProperty(HTMLFormElement.prototype, "data", {
  enumerable: true,
  get: function() {
    const formData = {};
    for(let _ of this.querySelectorAll("input:not(:disabled), select:not(:disabled), textarea:not(:disabled)")) {
      formData[_.name] = _.value;
    }
    return formData;
  }
});

Object.defineProperty(Date, "months", {
  enumerable: true,
  get: function() {
    return [
      "Jan",
      "Fév",
      "Mar",
      "Avr",
      "Mai",
      "Jui",
      "Juil",
      "Août",
      "Sep",
      "Oct",
      "Nov",
      "Déc"
    ];
  }
});

Object.defineProperty(Element.prototype, "disable", {
  set(toggle) {
    switch(toggle) {
      case false: this.removeAttribute("disabled"); break;
      default: this.setAttribute("disabled", 1); break;
    }
    return this;
  }
});

String.prototype.toUrl = function() {
  return location.rootHref + this;
}

async function api_fetch(endPoint) {
  return fetch(
    new Request(`api/${endPoint}`.toUrl())
  ).then(
    req => req.json()
  );
}

async function post(params) {
  let error = false;
  let needles = {
    url: undefined,
    method: "POST",
    contentType: 'application/x-www-form-urlencoded',
    data: {},
    done: undefined,
    fail: undefined
  };

  Object.keys(params).map(_ => {
    needles[_] = params[_];
  });

  var data = new FormData();
  Object.keys(needles.data).map(_ => {
    data.append(_, needles.data[_]);
  });


  let req = await fetch(
    new Request(needles.url),
    {
      headers: {
        //'Content-Type' : needles.contentType
      },
      method: needles.method,
      body: data
    }
  );

  const res = {
    raw: await req.text(),
    headers: await req.headers,
    status: await req.status
  }

  res.status = await res.status;
  res.headers = await res.headers;

  res.raw = await res.raw;
  res.raw = JSON.isValid(res.raw) ? JSON.parse(res.raw) : res.raw;


  let responseAppliers = {response: res.raw, status: res.status, headers: res.headers};
  if( res.status == 200 ) {
    if( typeof needles.done == "function" ) needles.done.apply(req, [responseAppliers]);
  }
  else if( typeof needles.fail == "function" ) needles.fail.apply(req, [responseAppliers]);

  return req;
}

JSON.isValid = function(value){
  try {
    JSON.parse(value);
  }
  catch (e) {
    return false;
  }
  return true;
}

Number.prototype.format = function(decimals, dec_point=",", thousands_sep="") {
  number = (this + '').replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
      prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
      sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
      dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
      s = '',
      toFixedFix = function (n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
      };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
      s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
      s[1] = s[1] || '';
      s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

String.prototype.toTemplate = function(onCreate=false) {
  const template = document.createElement("tmp-template");
  template.innerHTML = this;
  let realDeal = template.firstElementChild;
  if( !realDeal ) realDeal = template;

  realDeal.actors = {};
  realDeal.querySelectorAll("[data-actor]").forEach(_ => {
    realDeal.actors[_.dataset.actor] = _;
  });

  if( typeof onCreate == "function" ) onCreate.apply(realDeal, []);
  return realDeal;
}


Element.prototype.appendTo = function(target, callback=false) {
  target.appendChild(this);
  if(typeof callback == "function") callback.apply(this, [target]);
  return this;
}


// handle the input-numerator events
document.addEventListener("click", e => {
  let _ = e.target, input = _.parentNode.querySelector("input");
  if( !e.isTrusted || _.tagName != "BUTTON" || !_.parentNode.classList.contains("input-numerator") || !_.dataset.operator || !input ) return;

  if( !input.step ) input.step = 1;

  let self = {
    do(val) {
      let nextVal = parseFloat(input.value) + parseFloat(val);

      if( nextVal < input.min || nextVal > input.max ) return input.value;

      input.value = nextVal;

      input.dispatchEvent(new Event("input"));
      input.dispatchEvent(new Event("change"));
      return input.value;
    }
  };

  switch( _.dataset.operator ) {
    case "+":
      self.do(+input.step);
    break;

    case "-":
      self.do(-input.step);
    break;
  }
});


function overflowComponentUpdate() {
  var length = document.querySelectorAll("[data-overflow-component]").length;
  if( length > 0 )
    document.documentElement.classList.add("overflow-component-activated");
  else
    document.documentElement.classList.remove("overflow-component-activated");

  return length;
}


(function() {
  window.originalConfirm = window.confirm;
  window.confirm = function(params, callback=false) {
    const needles = {
      title: "",
      text: "",
      buttons: {
        ok: "Oui",
        no: "Non"
      }
    }

    if( typeof params == "string" ) needles.text = params;
    else Object.keys(params).map(key => needles[key] = params[key]);

    window.originalConfirm(needles.text);
  }
}());


class Storage {
  constructor(prefix) {
    this.prefix = prefix;
    return this;
  }

  add(name, value) {
    if( typeof value == "object" ) value = JSON.stringify(value);
    return localStorage.setItem(`${this.prefix + name}`, value.toString());
  };

  update(name, value) {
    return this.add(name, value);
  };

  remove(name) {
    return localStorage.removeItem(`${this.prefix + name}`);
  };

  data(name) {
    var value = localStorage.getItem(`${this.prefix + name}`);
    value = isNaN(value) ? value : parseFloat(value);
    value = !JSON.isValid(value) ? value : JSON.parse(value);

    return value;
  };

  get datas() {
    var data = {};
    Object.keys(localStorage).map(_ => {
      if( !new String(_).startsWith(this.prefix) ) return;
      _ = _.substr(this.prefix.length, _.length);

      data[_] = this.data(_);
    });
    return data;
  };
}




(function(){
  document.addEventListener("click focusin", function(e) {
    const needle = e.pathHasTag("fieldset");
    if( !needle ) return;

    needle.childElements = {
      legend: needle.querySelector("legend"),
      input: needle.querySelector("input, select, textarea")
    }

    if( e.target != needle ) needle.childElements.input.focus();


    if( needle.initialized ) return;
    needle.childElements.input.addEventListener("input", function() {

      let noCatch = false;
      if( !this.pattern ) noCatch = true;

      const regx = new RegExp(this.pattern, 'g');
      const res = regx.test(this.value);

      noCatch || res ? needle.setAttribute("data-is-valid", true) : needle.setAttribute("data-is-valid", false);
    });

    needle.initialized = true;
  });
}());
