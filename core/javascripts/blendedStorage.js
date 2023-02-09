class BlendedStorage {
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
