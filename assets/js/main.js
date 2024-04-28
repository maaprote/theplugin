/**
 * Main
 * 
 */

'use strict';

var ThePlugin = ThePlugin || {};
ThePlugin = {
  init: function init() {},
  componentExists: function componentExists(componentSelector) {
    return document.body.contains(document.querySelector(componentSelector));
  }
};

// Initialize.
document.addEventListener('DOMContentLoaded', function () {
  ThePlugin.init();
});