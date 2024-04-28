/**
 * Newsletter Form Shortcode.
 * 
 * The main.js file is a dependency for this file.
 */

'use strict';

function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
ThePlugin.newsletterForm = {
  mainSelector: '.rtp-newsletter-form',
  submitSelector: '.rtp-newsletter-form__form-submit',
  successSelector: '.rtp-newsletter-form__success',
  errorSelector: '.rtp-newsletter-form__error',
  init: function init() {
    var self = this;
    if (typeof thepluginData === 'undefined') {
      return false;
    }
    if (!ThePlugin.componentExists(this.mainSelector)) {
      return false;
    }
    var newsletters = document.querySelectorAll(this.mainSelector);
    if (newsletters.length === 0) {
      return false;
    }
    var _iterator = _createForOfIteratorHelper(newsletters),
      _step;
    try {
      var _loop = function _loop() {
          var newsletter = _step.value;
          var form = newsletter.querySelector('form');
          if (!form) {
            return {
              v: false
            };
          }
          form.addEventListener('submit', function (e) {
            e.preventDefault();
            var data = new FormData(form);
            var url = thepluginData.ajaxurl + '?action=' + thepluginNewsletterFormData.ajaxAction + '&nonce=' + thepluginNewsletterFormData.nonce;
            var newsletterFormWrapper = document.querySelector(self.mainSelector);
            var submitButton = document.querySelector(self.submitSelector);
            var initialSubmitText = document.querySelector(self.submitSelector).innerHTML;
            var successWrapper = document.querySelector(self.successSelector);
            var errorWrapper = document.querySelector(self.errorSelector);
            submitButton.innerHTML = submitButton.getAttribute('data-loading');
            fetch(url, {
              method: 'POST',
              body: data
            }).then(function (response) {
              return response.json();
            }).then(function (response) {
              if (response.success) {
                form.reset();
                newsletterFormWrapper.classList.remove('has-error');
                newsletterFormWrapper.classList.add('has-success');
              } else {
                newsletterFormWrapper.classList.remove('has-success');
                newsletterFormWrapper.classList.add('has-error');
                errorWrapper.innerHTML = response.data.message;
              }
              submitButton.innerHTML = initialSubmitText;
            }).catch(function (error) {
              console.error(error);
            });
          });
        },
        _ret;
      for (_iterator.s(); !(_step = _iterator.n()).done;) {
        _ret = _loop();
        if (_ret) return _ret.v;
      }
    } catch (err) {
      _iterator.e(err);
    } finally {
      _iterator.f();
    }
  }
};

// Initialize.
document.addEventListener('DOMContentLoaded', function () {
  ThePlugin.newsletterForm.init();
});