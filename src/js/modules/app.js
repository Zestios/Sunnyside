/* global site */
window.site = (window.site || {});

/**
 * Main application class.
 * @class App
 * @static
 */
window.site.App = (function App() {
  /**
   * Has the class been initialized?
   * @private
   */
  let inited = false;

  /**
   * Application config defaults.
   * @private
   * @param config.env     Current server environment
   * @param config.csrf    Security token to submit with forms
   * @param config.csrfName    Security token to submit with forms
   * @param config.locale  Current locale short code
   * @param config.device  Device detection based on browser signature
   * @param config.preview Page is shown through live preview mode
   * @param config.general Settings from general config
   */
  const config = {
    env: 'production',
    csrf: null,
    csrfName: null,
    locale: 'en',
    device: 'desktop',
    preview: false,
    general: {},
  };

  /**
   * Initializes the class.
   * @public
   */
  const init = function init(options) {
    // Abort if already initialized
    if (inited) {
      return false;
    }

    inited = true;

    const version = document.querySelector('html').getAttribute('code-version');
    console.log(` 🎉 Code Version:%c ${version} `, 'color: #ff0000');

    // Store application settings
    options = (options || {});

    if (options.env) { config.env = options.env; }
    if (options.csrf) { config.csrf = options.csrf; }
    if (options.csrfName) { config.csrfName = options.csrfName; }
    if (options.locale) { config.locale = options.locale; }
    if (options.device) { config.device = options.device; }
    if (options.preview) { config.preview = options.preview; }
    if (options.general) { config.general = options.general; }

    // Initialize child classes
    // if (typeof site.Example === 'object') { site.Example.init(); }

    console.table(options);

    // Return success
    return true;
  };

  /**
   * Getter for application config.
   * @public
   */
  const getConfig = function getConfig(option) {
    return config[option] ? config[option] : false;
  };

  /**
   * Expose public methods & properties.
   */
  return {
    init,
    config: getConfig,
  };
}());
