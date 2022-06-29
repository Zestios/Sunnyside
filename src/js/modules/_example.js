window.site = (window.site || {});

/**
 * Utilities for Google Analytics.
 * @class Track
 * @static
 */
site.Example = (function Example() {
  /**
   * Has the class been initialized?
   * @private
   */
  let inited = false;

  /**
   * Initializes the class.
   * @public
   */
  const init = function () {
    // Abort if already initialized
    if (inited) {
      return false;
    }

    inited = true;

    // Your code here

    return true;
  };

  // Expose public methods & properties
  return {
    init: init,
  };

}());
