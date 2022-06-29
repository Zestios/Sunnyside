/* -- A listener to ensure the fonts we need to use have been loaded */

if (document.documentElement.className.indexOf("fonts-loaded") < 0) {
  // var fontello = new FontFaceObserver("fontello", {});
  var Nexa = new FontFaceObserver("Nexa", {});

  Promise.all([
    // fontello.load(''),
    Nexa.load()
  ]).then(function () {

    document.documentElement.className += " fonts-loaded";
    Cookie.set('fonts-loaded', 1, {expires: '7D', secure: true});
  });
}
