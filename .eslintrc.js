module.exports = {
  root: true,
  parser: 'babel-eslint',
  env: {
    browser: true,
    node: true
  },
 extends: 'airbnb-base',
  // required to lint *.vue files
  plugins: [
    'html'
  ],
  // add your custom rules here
  rules: {
    "no-underscore-dangle": "off",
    "func-names": "off",
    "object-shorthand": "off"
  },
  globals: {}
}
