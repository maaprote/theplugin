/**
 * Main
 * 
 */

'use strict';

var ThePlugin = ThePlugin || {};
ThePlugin = {
  componentExists: function componentExists(componentSelector) {
    return document.body.contains(document.querySelector(componentSelector));
  }

  // as the plugin grows, we can add more methods here
};