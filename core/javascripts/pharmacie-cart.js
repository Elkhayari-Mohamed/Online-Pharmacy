class ASHCart {

  constructor(onDefaultUpdate=undefined) {
    let _ = this;
    this.ondefaultupdate = onDefaultUpdate;
    this.onchange = undefined;

    this.storage = new Storage("ashcart_");
    this.articles = {

      add(buffer) {
        let needles = {
          id: undefined,
          quantity: 1
        }

        Object.keys(buffer).map(_ => needles[_] = buffer[_]);

        let newList = this.all;
        let highstack = {
          id: false,
          quantity: 0
        };
        newList.map(_ => {
          if( highstack.id ) return;
          if( _.id == needles.id ) {
            highstack = {
              id: _.id,
              quantity: _.quantity
            };

          }
        });

        if( highstack.id ) {
          this.update(highstack.id, {
            quantity: needles.quantity + highstack.quantity
          }, true);
        }
        else newList.push(needles);

        highstack.id ? this.all : _.storage.update("cart", newList)
        if( typeof _.onchange == "function" ) _.onchange.apply(_, [_.articles]);
        if( typeof _.ondefaultupdate == "function" ) _.ondefaultupdate.apply(_, [_.articles]);
        return newList;
      },

      remove(id) {
        let newList = [];
        this.all.map(_ => {
          if( _.id == id ) return;

          newList.push(_);
        });

        _.storage.update("cart", newList)
        if( typeof _.onchange == "function" ) _.onchange.apply(_, [_.articles]);
        if( typeof _.ondefaultupdate == "function" ) _.ondefaultupdate.apply(_, [_.articles]);
        return newList;
      },

      removeAll() {
        const newList = [];

        _.storage.update("cart", newList);
        if( typeof _.onchange == "function" ) _.onchange.apply(_, [_.articles]);
        if( typeof _.ondefaultupdate == "function" ) _.ondefaultupdate.apply(_, [_.articles]);
        return newList;
      },

      update(id, buffers={}, emulated=false) {
        let newList = [];
        this.all.map(_ => {
          if( _.id != id ) {
            newList.push(_);
            return;
          };

          Object.keys(buffers).forEach(k => {
            _[k] = buffers[k];
          });
          newList.push(_);
        });

        _.storage.update("cart", newList);
        if( !emulated && typeof _.onchange == "function" ) _.onchange.apply(_, [_.articles]);
        return newList;
      },

      getById(id) {
        let index = false;
        this.all.map((_, i) => {
          if( _.id != id ) return;
          index = i;
        });

        return index === false ? false : this.all[index];
      },

      getByName(name, uppercaseSensitive=false) {
        let index = false;
        this.all.map((_, i) => {
          if( uppercaseSensitive ) {
            if( _.name != name ) return;
          }
          else {
            if( _.name.toUpperCase() != name.toUpperCase() ) return;
          }

          index = i;
        });

        return index === false ? false : this.all[index];
      },

      get all () {
        return _.storage.datas.cart;
      },

      get count () {
        return this.all.length;
      }

    }


    this.Init();
    return this;
  }

  async Init() {
    if( !this.storage.datas.cart || !JSON.isValid(JSON.stringify(this.storage.datas.cart)) ) this.storage.add("cart", []);

    if( typeof this.ondefaultupdate == "function" ) this.ondefaultupdate.apply(this, [this.articles]);
    return this;
  }

  get countProducts() {
    return this.articles.count;
  }

  get countProductsAll() {
    var total = 0;
    this.articles.all.map(_ => {
      total += _.quantity;
    });

    return total;
  }

}

const Cart = new ASHCart(
  function() {
    if( typeof cartHeaderBtn == "undefined" || !cartHeaderBtn ) return;
    cartHeaderBtn.dataset.bulle = this.countProductsAll;
  }
);
