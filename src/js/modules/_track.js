window.site = (window.site || {});

/**
 * Utilities for Google Analytics.
 * @class Track
 * @static
 */
site.Track = (function Track() {

  'use strict';

  /**
   * Has the class been initialized?
   * @private
   */
  var inited = false;

  /**
   * Initializes the class.
   * @public
   */
  var init = function() {

    // Abort if already initialized
    if (inited) {
      return false;
    }

    inited = true;

    [].slice.call($('[data-track]')).forEach((item) => {
      console.log(item);
      item.addEventListener('click', (e) => {
        console.log(e.target, e.target.dataset.category, e.target.dataset.action, e.target.dataset.bid);
        let $item = e.target;

        if (typeof $item.dataset.category === 'string' && typeof $item.dataset.action === 'string' && typeof $item.dataset.bid === 'string') {
          sendEvent($item.dataset.category, $item.dataset.action, $item.dataset.bid);
        }
      });
    });

    return true;

  };

  /**
   * Send a tracking event.
   * @public
   */
  var sendEvent = function(category, action, bid) {

    console.log("ga('send', 'event', category, action, bid);");

    console.log(`
      window.dataLayer = window.dataLayer || [];
      dataLayer.push({
        'event': 'click',
        'category': ${category},
        'action': ${action},
        'bid': ${bid}
      });
    `);

    if (category && action && bid && site.App.config('env') === 'production') {

      window.dataLayer = window.dataLayer || [];
      dataLayer.push({
        'event': 'click',
        'category': category,
        'action': action,
        'bid': bid
      });

      return true;
    }

    return false;

  };

  /**
   * Send a page view event.
   * @public
   */
  var sendPage = function(url) {

    var parser;

    if (url === undefined || url === '') {
      url = location.pathname;
    } else {
      parser = document.createElement('a');
      parser.href = url;
      url = parser.pathname + parser.search;
    }

    if (site.App.config('env') === 'production') {
      return ga('send', 'pageview', url);
    }

    return false;

  };

  // Expose public methods & properties
  return {
    init: init,
    page: sendPage,
    event: sendEvent,
  };

}());
