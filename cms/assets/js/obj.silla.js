/**
 * Base class, which has an custom event management.
 *
 * Warning: if the properties are of Reference type, they won't be copied, only referenced.
 *
 * @param {Object} obj An object, for copying all his properties to the new Obj.
 *
 * @version 1.0
 */
var Obj = function (obj) {
  'use strict';

  if (obj && !(obj instanceof Object)) {
    throw 'Invalid argument. Obj must be an object.';
  }

  var i;
  for (i in obj) {
    this[i] = obj[i];
  }
};

/**
 * Attach an event
 * @param {String} event
 * @param {Object} handler
 * @param {String} scope
 *
 * @return {Obj} object
 *
 * @TODO add attaching of multiple handlers
 */
Obj.prototype.attach = function (event, handler, scope) {
  'use strict';
  if (!(this.events instanceof Object)) {
    this.events = {};
  }

  if (!this.events[event]) {
    this.events[event] = [];
  }

  this.events[event].push({
    scope: scope || null,
    handler: handler
  });

  return this;
};

/**
 * Detach a particular event handler or all handler if omitted.
 *
 * @param {String} event
 * @param {Object} handler
 */
Obj.prototype.detach = function (event, handler) {
  'use strict';
  if (handler !== undefined) {
    var i;
    for (i = 0; i < this.events[event].length; i++) {
      if (this.events[event][i].handler === handler) {
        this.events[event].splice(i, 1);
        break;
      }
    }
  } else {
    if (this.events) {
      this.events[event] = [];
    }
  }
};

/**
 * Execute all handlers for the event.
 *
 * @param {String} event
 * @param {Object} param Value to be received in the handlers
 */
Obj.prototype.fire = function (event, param) {
  'use strict';

  if (!this.events || !this.events[event]) {
    return;
  }

  try {
    var i, toCall = [];
    for (i = 0; i < this.events[event].length; i++) {
      toCall.push(this.events[event][i]);
    }

    for (i = 0; i < toCall.length; i++) {
      if (toCall[i]) {
        if (param instanceof Array) {
          toCall[i].handler.apply(toCall[i].scope || this, param);
        } else {
          toCall[i].handler.call(toCall[i].scope || this, param);
        }
      }
    }
  } catch (e) {
    console.log(e, e.message);
  }
};
