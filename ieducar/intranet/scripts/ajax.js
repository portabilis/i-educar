/*  Prototype JavaScript framework, version 1.4.0_pre10_ajax
 *  (c) 2005 Sam Stephenson <sam@conio.net>
 *
 *  This is a downcut version for AJAX by Alexander Kirk http://alexander.kirk.at/
 *
 *  Prototype is freely distributable under the terms of an MIT-style license.
 *
 *  For details, see the Prototype web site: http://prototype.conio.net/
 *
/*--------------------------------------------------------------------------*/

var Prototype = {
	Version: '1.4.0_pre10_ajax',

	emptyFunction: function() {},
	K: function(x) {return x}
}

var Class = {
	create: function() {
		return function() {
			this.initialize.apply(this, arguments);
		}
	}
}

var Abstract = new Object();

Object.extend = function(destination, source) {
	for (property in source) {
		destination[property] = source[property];
	}
	return destination;
}

Object.inspect = function(object) {
	try {
		if (object == undefined) return 'undefined';
		if (object == null) return 'null';
		return object.inspect ? object.inspect() : object.toString();
	} catch (e) {
		if (e instanceof RangeError) return '...';
		throw e;
	}
}

Function.prototype.bind = function(object) {
	var __method = this;
	return function() {
		return __method.apply(object, arguments);
	}
}

Function.prototype.bindAsEventListener = function(object) {
	var __method = this;
	return function(event) {
		return __method.call(object, event || window.event);
	}
}

Object.extend(Number.prototype, {
	toColorPart: function() {
		var digits = this.toString(16);
		if (this < 16) return '0' + digits;
		return digits;
	},

	succ: function() {
		return this + 1;
	},

	times: function(iterator) {
		$R(0, this, true).each(iterator);
		return this;
	}
});

var Try = {
	these: function() {
		var returnValue;

		for (var i = 0; i < arguments.length; i++) {
			var lambda = arguments[i];
			try {
				returnValue = lambda();
				break;
			} catch (e) {}
		}

		return returnValue;
	}
}

/*--------------------------------------------------------------------------*/

var PeriodicalExecuter = Class.create();
PeriodicalExecuter.prototype = {
	initialize: function(callback, frequency) {
		this.callback = callback;
		this.frequency = frequency;
		this.currentlyExecuting = false;

		this.registerCallback();
	},

	registerCallback: function() {
		setInterval(this.onTimerEvent.bind(this), this.frequency * 1000);
	},

	onTimerEvent: function() {
		if (!this.currentlyExecuting) {
			try {
				this.currentlyExecuting = true;
				this.callback();
			} finally {
				this.currentlyExecuting = false;
			}
		}
	}
}

/*--------------------------------------------------------------------------*/

function $() {
	var elements = new Array();

	for (var i = 0; i < arguments.length; i++) {
		var element = arguments[i];
		if (typeof element == 'string')
			element = document.getElementById(element);

		if (arguments.length == 1)
			return element;

		elements.push(element);
	}

	return elements;
}



var Ajax = {
	getTransport: function() {
		return Try.these(
			function() {return new ActiveXObject('Msxml2.XMLHTTP')},
			function() {return new ActiveXObject('Microsoft.XMLHTTP')},
			function() {return new XMLHttpRequest()}
		) || false;
	}
}

Ajax.Base = function() {};
Ajax.Base.prototype = {
	setOptions: function(options) {
		this.options = {
			method:			 'post',
			asynchronous: true,
			parameters:	 ''
		}
		Object.extend(this.options, options || {});
	},

	responseIsSuccess: function() {
		return this.transport.status == undefined
				|| this.transport.status == 0
				|| (this.transport.status >= 200 && this.transport.status < 300);
	},

	responseIsFailure: function() {
		return !this.responseIsSuccess();
	}
}

Ajax.Request = Class.create();
Ajax.Request.Events =
	['Uninitialized', 'Loading', 'Loaded', 'Interactive', 'Complete'];

Ajax.Request.prototype = Object.extend(new Ajax.Base(), {
	initialize: function(url, options) {
		this.transport = Ajax.getTransport();
		this.setOptions(options);
		this.request(url);
	},

	request: function(url) {
		var parameters = this.options.parameters || '';
		if (parameters.length > 0) parameters += '&_=';

		try {
			if (this.options.method == 'get')
				url += '?' + parameters;
			this.transport.open(this.options.method, url,
				this.options.asynchronous);

			if (this.options.asynchronous) {
				this.transport.onreadystatechange = this.onStateChange.bind(this);
				setTimeout((function() {this.respondToReadyState(1)}).bind(this), 10);
			}

			this.setRequestHeaders();

			var body = this.options.postBody ? this.options.postBody : parameters;
			this.transport.send(this.options.method == 'post' ? body : null);

		} catch (e) {
		}
	},

	setRequestHeaders: function() {
		var requestHeaders =
			['X-Requested-With', 'XMLHttpRequest',
			 'X-Prototype-Version', Prototype.Version];

		if (this.options.method == 'post') {
			requestHeaders.push('Content-type',
				'application/x-www-form-urlencoded');

			/* Force "Connection: close" for Mozilla browsers to work around
			 * a bug where XMLHttpReqeuest sends an incorrect Content-length
			 * header. See Mozilla Bugzilla #246651.
			 */
			if (this.transport.overrideMimeType)
				requestHeaders.push('Connection', 'close');
		}

		if (this.options.requestHeaders)
			requestHeaders.push.apply(requestHeaders, this.options.requestHeaders);

		for (var i = 0; i < requestHeaders.length; i += 2)
			this.transport.setRequestHeader(requestHeaders[i], requestHeaders[i+1]);
	},

	onStateChange: function() {
		var readyState = this.transport.readyState;
		if (readyState != 1)
			this.respondToReadyState(this.transport.readyState);
	},

	evalJSON: function() {
		try {
			var json = this.transport.getResponseHeader('X-JSON'), object;
			object = eval(json);
			return object;
		} catch (e) {
		}
	},

	respondToReadyState: function(readyState) {
		var event = Ajax.Request.Events[readyState];
		var transport = this.transport, json = this.evalJSON();

		if (event == 'Complete')
			(this.options['on' + this.transport.status]
			 || this.options['on' + (this.responseIsSuccess() ? 'Success' : 'Failure')]
			 || Prototype.emptyFunction)(transport, json);

		(this.options['on' + event] || Prototype.emptyFunction)(transport, json);

		/* Avoid memory leak in MSIE: clean up the oncomplete event handler */
		if (event == 'Complete')
			this.transport.onreadystatechange = Prototype.emptyFunction;
	}
});

Ajax.Updater = Class.create();
Ajax.Updater.ScriptFragment = '(?:<script.*?>)((\n|.)*?)(?:<\/script>)';

Object.extend(Object.extend(Ajax.Updater.prototype, Ajax.Request.prototype), {
	initialize: function(container, url, options) {
		this.containers = {
			success: container.success ? $(container.success) : $(container),
			failure: container.failure ? $(container.failure) :
				(container.success ? null : $(container))
		}

		this.transport = Ajax.getTransport();
		this.setOptions(options);

		var onComplete = this.options.onComplete || Prototype.emptyFunction;
		this.options.onComplete = (function(transport, object) {
			this.updateContent();
			onComplete(transport, object);
		}).bind(this);

		this.request(url);
	},

	updateContent: function() {
		var receiver = this.responseIsSuccess() ?
			this.containers.success : this.containers.failure;

		var match		= new RegExp(Ajax.Updater.ScriptFragment, 'img');
		var response = this.transport.responseText.replace(match, '');
		var scripts	= this.transport.responseText.match(match);

		if (receiver) {
			if (this.options.insertion) {
				new this.options.insertion(receiver, response);
			} else {
				receiver.innerHTML = response;
			}
		}

		if (this.responseIsSuccess()) {
			if (this.onComplete)
				setTimeout(this.onComplete.bind(this), 10);
		}

		if (this.options.evalScripts && scripts) {
			match = new RegExp(Ajax.Updater.ScriptFragment, 'im');
			setTimeout((function() {
				for (var i = 0; i < scripts.length; i++)
					eval(scripts[i].match(match)[1]);
			}).bind(this), 10);
		}
	}
});

Ajax.PeriodicalUpdater = Class.create();
Ajax.PeriodicalUpdater.prototype = Object.extend(new Ajax.Base(), {
	initialize: function(container, url, options) {
		this.setOptions(options);
		this.onComplete = this.options.onComplete;

		this.frequency = (this.options.frequency || 2);
		this.decay = 1;

		this.updater = {};
		this.container = container;
		this.url = url;

		this.start();
	},

	start: function() {
		this.options.onComplete = this.updateComplete.bind(this);
		this.onTimerEvent();
	},

	stop: function() {
		this.updater.onComplete = undefined;
		clearTimeout(this.timer);
		(this.onComplete || Ajax.emptyFunction).apply(this, arguments);
	},

	updateComplete: function(request) {
		if (this.options.decay) {
			this.decay = (request.responseText == this.lastText ?
				this.decay * this.options.decay : 1);

			this.lastText = request.responseText;
		}
		this.timer = setTimeout(this.onTimerEvent.bind(this),
			this.decay * this.frequency * 1000);
	},

	onTimerEvent: function() {
		this.updater = new Ajax.Updater(this.container, this.url, this.options);
	}
});
