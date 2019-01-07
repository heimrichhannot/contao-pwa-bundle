"use strict";

function HuhContaoPwaButtons() {
  this.buttons = [];
  this.debug = false;
  this.isInit = false;
  this.subscriptionAction = '';
  document.addEventListener('huh_pwa_push_isSubscribed', this.setUnsubscribe.bind(this));
  document.addEventListener('huh_pwa_push_isUnsubscribed', this.setSubscribe.bind(this));
  document.addEventListener('huh_pwa_push_permission_denied', this.setBlocked.bind(this));
}

HuhContaoPwaButtons.prototype.onReady = function () {
  if (!this.isInit) {
    this.init();
  }
};

HuhContaoPwaButtons.prototype.init = function () {
  this.collectElementsToUpdate();
  this.isInit = true;
};

HuhContaoPwaButtons.prototype.collectElementsToUpdate = function () {
  var _this = this;

  this.buttons = document.querySelectorAll('.huhPwaWebSubscription');
  this.buttons.forEach(function (button) {
    button.addEventListener('click', function () {
      _this.changeSubscriptionStatus(button);
    });
  });
};

HuhContaoPwaButtons.prototype.beforeEvent = function (debugMessage) {
  if (this.debug) {
    console.log('[Push Notification Buttons] ' + debugMessage);
  }

  if (!this.isInit) {
    this.init();
  }
};

HuhContaoPwaButtons.prototype.setSubscribe = function (event) {
  this.beforeEvent('Update Buttons to "Subscribe"');
  this.subscriptionAction = 'subscribe';
  this.buttons.forEach(function (button) {
    button.disabled = false;
    button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.subscribe;
    button.classList.add('unsubscribed');
    button.classList.remove('subscribed');
    button.classList.remove('blocked');
  });
};

HuhContaoPwaButtons.prototype.setUnsubscribe = function (event) {
  this.beforeEvent('Update Buttons to "Unsubscribe"');
  this.subscriptionAction = 'unsubscribe';
  this.buttons.forEach(function (button) {
    button.disabled = false;
    button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.unsubscribe;
    button.classList.add('subscribed');
    button.classList.remove('unsubscribed');
    button.classList.remove('blocked');
  });
};

HuhContaoPwaButtons.prototype.setBlocked = function (event) {
  this.beforeEvent('Update Buttons to blocked');
  this.buttons.forEach(function (button) {
    button.querySelector('.label').textContent = HuhPwaTranslator.pushnotifications.blocked;
    button.classList.add('blocked');
    button.classList.remove('unsubscribed');
    button.classList.remove('subscribed');
    button.disabled = true;
  });
};

HuhContaoPwaButtons.prototype.changeSubscriptionStatus = function (button) {
  console.log("Fire huh_pwa_push_changeSubscriptionState event");
  button.disabled = true;
  document.dispatchEvent(new CustomEvent('huh_pwa_push_changeSubscriptionState', {
    detail: this.subscriptionAction
  }));
};

exports = HuhContaoPwaButtons;
"use strict";

function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

function PushNotificationSubscription(subscribePath, unsubscribePath) {
  var _this = this;

  this.debug = true;
  this.subscribePath = subscribePath;
  this.unsubscribePath = unsubscribePath;

  this.init = function () {
    document.addEventListener('huh_pwa_push_changeSubscriptionState', this.changeSubscriptionStatus.bind(this));
  };

  this.subscribe = function () {
    if (_this.debug) console.log('[Push Notification Subscription] Trying to Subscribe');
    navigator.serviceWorker.ready.then(
    /*#__PURE__*/
    function () {
      var _ref = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee(registration) {
        var responce, publicKey;
        return regeneratorRuntime.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _context.next = 2;
                return fetch('./api/notifications/publickey');

              case 2:
                responce = _context.sent;
                _context.next = 5;
                return responce.text();

              case 5:
                publicKey = _context.sent;
                return _context.abrupt("return", registration.pushManager.subscribe({
                  userVisibleOnly: true,
                  applicationServerKey: _this.urlBase64ToUint8Array(publicKey)
                }).then(function (subscription) {
                  if (_this.debug) console.log('[Push Notification Subscription] Successful Subscribed', subscription.endpoint);
                  fetch(_this.subscribePath, {
                    method: 'post',
                    headers: {
                      'Content-type': 'application/json'
                    },
                    body: JSON.stringify({
                      subscription: subscription
                    })
                  });
                }).then(function () {
                  _this.setIsSubscribed();
                }).catch(function (reason) {
                  document.dispatchEvent(new CustomEvent('huh_pwa_push_subscription_failed', {
                    detail: {
                      reason: reason
                    }
                  }));
                }));

              case 7:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      return function (_x) {
        return _ref.apply(this, arguments);
      };
    }());
  };

  this.unsubscribe = function () {
    if (_this.debug) console.log('[Push Notification Subscription] Trying to unsubscribe');
    navigator.serviceWorker.ready.then(function (registration) {
      return registration.pushManager.getSubscription();
    }).then(function (subscription) {
      return subscription.unsubscribe().then(function () {
        if (_this.debug) console.log('[Push Notification Subscription] Successful Unsubscribed', subscription.endpoint);
        return fetch(_this.unsubscribePath, {
          method: 'post',
          headers: {
            'Content-type': 'application/json'
          },
          body: JSON.stringify({
            subscription: subscription
          })
        });
      });
    }).then(function () {
      _this.setIsUnsubscribed();
    }).catch(function (reason) {
      document.dispatchEvent(new CustomEvent('huh_pwa_push_unsubscription_failed', {
        detail: {
          'reason': reason
        }
      }));
    });
  };

  this.setIsUnsubscribed = function () {
    if (!_this.checkPermission()) return;
    document.dispatchEvent(new Event('huh_pwa_push_isUnsubscribed'));
    if (_this.debug) console.log('[Push Notification Subscription] Fired huh_pwa_push_isUnsubscribed');
  };

  this.setIsSubscribed = function () {
    if (!_this.checkPermission()) return;
    document.dispatchEvent(new Event('huh_pwa_push_isSubscribed'));
    if (_this.debug) console.log('[Push Notification Subscription] Fired huh_pwa_push_isSubscribed"');
  };

  this.checkPermission = function () {
    if (Notification.permission === 'denied') {
      document.dispatchEvent(new Event('huh_pwa_push_permission_denied'));
      if (_this.debug) console.log('[Push Notification Subscription] Fired huh_pwa_push_permission_denied');
      return false;
    }

    return true;
  };

  this.changeSubscriptionStatus = function (event) {
    console.log("CHANGE Subscription state");
    if (!this.checkPermission()) return;

    if (event.detail === 'subscribe') {
      this.subscribe();
    } else if (event.detail === 'unsubscribe') {
      this.unsubscribe();
    }
  };

  this.urlBase64ToUint8Array = function (base64String) {
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
  };
}

exports = PushNotificationSubscription;
"use strict";

require("core-js/modules/es6.array.copy-within");

require("core-js/modules/es6.array.fill");

require("core-js/modules/es6.array.find");

require("core-js/modules/es6.array.find-index");

require("core-js/modules/es6.array.from");

require("core-js/modules/es7.array.includes");

require("core-js/modules/es6.array.iterator");

require("core-js/modules/es6.array.of");

require("core-js/modules/es6.array.sort");

require("core-js/modules/es6.array.species");

require("core-js/modules/es6.date.to-primitive");

require("core-js/modules/es6.function.has-instance");

require("core-js/modules/es6.function.name");

require("core-js/modules/es6.map");

require("core-js/modules/es6.math.acosh");

require("core-js/modules/es6.math.asinh");

require("core-js/modules/es6.math.atanh");

require("core-js/modules/es6.math.cbrt");

require("core-js/modules/es6.math.clz32");

require("core-js/modules/es6.math.cosh");

require("core-js/modules/es6.math.expm1");

require("core-js/modules/es6.math.fround");

require("core-js/modules/es6.math.hypot");

require("core-js/modules/es6.math.imul");

require("core-js/modules/es6.math.log1p");

require("core-js/modules/es6.math.log10");

require("core-js/modules/es6.math.log2");

require("core-js/modules/es6.math.sign");

require("core-js/modules/es6.math.sinh");

require("core-js/modules/es6.math.tanh");

require("core-js/modules/es6.math.trunc");

require("core-js/modules/es6.number.constructor");

require("core-js/modules/es6.number.epsilon");

require("core-js/modules/es6.number.is-finite");

require("core-js/modules/es6.number.is-integer");

require("core-js/modules/es6.number.is-nan");

require("core-js/modules/es6.number.is-safe-integer");

require("core-js/modules/es6.number.max-safe-integer");

require("core-js/modules/es6.number.min-safe-integer");

require("core-js/modules/es6.number.parse-float");

require("core-js/modules/es6.number.parse-int");

require("core-js/modules/es6.object.assign");

require("core-js/modules/es7.object.define-getter");

require("core-js/modules/es7.object.define-setter");

require("core-js/modules/es7.object.entries");

require("core-js/modules/es6.object.freeze");

require("core-js/modules/es6.object.get-own-property-descriptor");

require("core-js/modules/es7.object.get-own-property-descriptors");

require("core-js/modules/es6.object.get-own-property-names");

require("core-js/modules/es6.object.get-prototype-of");

require("core-js/modules/es7.object.lookup-getter");

require("core-js/modules/es7.object.lookup-setter");

require("core-js/modules/es6.object.prevent-extensions");

require("core-js/modules/es6.object.is");

require("core-js/modules/es6.object.is-frozen");

require("core-js/modules/es6.object.is-sealed");

require("core-js/modules/es6.object.is-extensible");

require("core-js/modules/es6.object.keys");

require("core-js/modules/es6.object.seal");

require("core-js/modules/es6.object.set-prototype-of");

require("core-js/modules/es7.object.values");

require("core-js/modules/es6.promise");

require("core-js/modules/es7.promise.finally");

require("core-js/modules/es6.reflect.apply");

require("core-js/modules/es6.reflect.construct");

require("core-js/modules/es6.reflect.define-property");

require("core-js/modules/es6.reflect.delete-property");

require("core-js/modules/es6.reflect.get");

require("core-js/modules/es6.reflect.get-own-property-descriptor");

require("core-js/modules/es6.reflect.get-prototype-of");

require("core-js/modules/es6.reflect.has");

require("core-js/modules/es6.reflect.is-extensible");

require("core-js/modules/es6.reflect.own-keys");

require("core-js/modules/es6.reflect.prevent-extensions");

require("core-js/modules/es6.reflect.set");

require("core-js/modules/es6.reflect.set-prototype-of");

require("core-js/modules/es6.regexp.constructor");

require("core-js/modules/es6.regexp.flags");

require("core-js/modules/es6.regexp.match");

require("core-js/modules/es6.regexp.replace");

require("core-js/modules/es6.regexp.split");

require("core-js/modules/es6.regexp.search");

require("core-js/modules/es6.regexp.to-string");

require("core-js/modules/es6.set");

require("core-js/modules/es6.symbol");

require("core-js/modules/es7.symbol.async-iterator");

require("core-js/modules/es6.string.anchor");

require("core-js/modules/es6.string.big");

require("core-js/modules/es6.string.blink");

require("core-js/modules/es6.string.bold");

require("core-js/modules/es6.string.code-point-at");

require("core-js/modules/es6.string.ends-with");

require("core-js/modules/es6.string.fixed");

require("core-js/modules/es6.string.fontcolor");

require("core-js/modules/es6.string.fontsize");

require("core-js/modules/es6.string.from-code-point");

require("core-js/modules/es6.string.includes");

require("core-js/modules/es6.string.italics");

require("core-js/modules/es6.string.iterator");

require("core-js/modules/es6.string.link");

require("core-js/modules/es7.string.pad-start");

require("core-js/modules/es7.string.pad-end");

require("core-js/modules/es6.string.raw");

require("core-js/modules/es6.string.repeat");

require("core-js/modules/es6.string.small");

require("core-js/modules/es6.string.starts-with");

require("core-js/modules/es6.string.strike");

require("core-js/modules/es6.string.sub");

require("core-js/modules/es6.string.sup");

require("core-js/modules/es6.typed.array-buffer");

require("core-js/modules/es6.typed.int8-array");

require("core-js/modules/es6.typed.uint8-array");

require("core-js/modules/es6.typed.uint8-clamped-array");

require("core-js/modules/es6.typed.int16-array");

require("core-js/modules/es6.typed.uint16-array");

require("core-js/modules/es6.typed.int32-array");

require("core-js/modules/es6.typed.uint32-array");

require("core-js/modules/es6.typed.float32-array");

require("core-js/modules/es6.typed.float64-array");

require("core-js/modules/es6.weak-map");

require("core-js/modules/es6.weak-set");

require("core-js/modules/web.timers");

require("core-js/modules/web.immediate");

require("core-js/modules/web.dom.iterable");

require("regenerator-runtime/runtime");
"use strict";

HuhContaoPwaButtons = require('./HuhContaoPwaButtons.es6');
HuhPwaSubscription = require('./PushNotificationSubscription.es6');
PwaButtons = new HuhContaoPwaButtons();
document.addEventListener('DOMContentLoaded', function () {
  PwaButtons.onReady();
});
