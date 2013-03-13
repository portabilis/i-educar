
// jquery utils

function buildId(id) {
  return typeof(id) == 'string' && id.length > 0 && id.charAt(0) != '#' ? '#' + id : id;
}


// console utils

function safeLog(value) {
  if(typeof(console) != 'undefined' && typeof(console.log) == 'function')
    console.log(value);
}


// form utils

var formUtils = {
  submit : function(form) {

    if (form == undefined)
      form = $j('form:first');

    form.removeAttr('onsubmit');

    if (validationUtils.validatesFields()) {
      $j('form:first').find('#btn_enviar').attr('disabled', 'disabled').val('Aguarde...');
      form.submit();
    }
  }
};

function fixupFieldsWidth(additionalFields, force){
  if (! $j(document).data('fixed-fields-width') || force) {
    var maxWidth = 0;
    var $fields = $j('form select');

    if (additionalFields)
      $j.merge($fields, additionalFields);

    //get maxWidh
    $j.each($fields, function(index, field){
      $field = $j(field);
      if ($field.outerWidth() > maxWidth)
        maxWidth = $field.outerWidth();
    });

    //set maxWidth
    $j.each($fields, function(index, field){
      $j(field).width(maxWidth);
    });

    $j(document).data('fixed-fields-width', true);
  }
  else
    safeLog('fixupFieldsWidth already called, skipping!');
};


// options (hash) utils

var optionsUtils = {
  get : function(options, optionName) {
    var value = options[optionName];

    if (typeof value == 'undefined')
      throw new Error("Option '" + optionName +  "' not defined in simpleSearchOptions.");

    return value;
  },

  merge : function(defaultOptions, options) {
    if (typeof options == 'undefined')
      options = {};

    return $j.extend({}, defaultOptions, options);
  }
};

// key-value object (hash) utils

var objectUtils = {
  length : function(hash) {
    return Object.getOwnPropertyNames(hash).length;
  },

  join : function(object, glue, separator) {
    if (glue == undefined)
      glue = '=';

    if (separator == undefined)
      separator = ' ';

    return $j.map(Object.getOwnPropertyNames(object), function(k) { return [k, object[k]].join(glue) }).join(separator)
  }
};

// window utils

var windowUtils = {

  // open a new window, for more information see developer.mozilla.org/en-US/docs/DOM/window.open
  open : function(url, name, options) {
    var defaultOptions = {
      name : 'new_window',
      options : {}
    };

    var defaultWindowOptions = {
      top         : 0,
      left        : 0,
      height      : $j(window).height(),
      width       : $j(window).width(),

      resizable   : 'yes',
      scrollbars  : 'yes',

      menubar     : 'no',
      toolbar     : 'no',
      location    : 'no',
      personalbar : 'no',
      status      : 'no'
    };

    options         = optionsUtils.merge(defaultOptions, options);
    options.options = optionsUtils.merge(defaultWindowOptions, options.options);

    window.open(url, options.name,  objectUtils.join(options.options)).focus();
  }
};

// string utils

var stringUtils = {
  toUtf8 : function(s) {
    try {
      s = decodeURIComponent(escape(s));
    }
    catch(e) {
      safeLog('Erro ao decodificar string utf8: ' + s);
    }

    return s;
  }
};

// #TODO migrar restante funcoes string utils para hash stringUtils / migrar referencias a estas.

function safeToUpperCase(value) {
  if (typeof(value) == 'string')
    value = value.toUpperCase();

  return value;
}


function safeToLowerCase(value) {
  if (typeof(value) == 'string')
    value = value.toLowerCase();

  return value;
}


function safeCapitalize(value) {
  if (typeof(value) == 'string') {
    value        = value.toLowerCase();
    var words    = value.split(' ');

    $j.each(words, function(index, word){
      words[index] = word.substr(0, 1).toUpperCase() + word.substr(1);
    });

    value = words.join(' ');
  }

  return value;
}


function safeSort(values) {
  try{
    var sortedValues = values.sort(function(a, b) {
      if (typeof(a) == 'string' && typeof(b) == 'string')
        var isGreaterThan = a.toLowerCase() > b.toLowerCase();
      else
        var isGreaterThan = a > b;

     return isGreaterThan ? 1 : -1;
    });
    return sortedValues;
  }
  catch(e) {
    safeLog('Erro ao ordenar valores: ' + e);
    safeLog(values);
    return values;
  }
}


function safeUtf8Decode(s) {
  return stringUtils.toUtf8(s);
}

// feedback messages

// #TODO migrar todas referencias de "handleMessages([{type*" para "messageUtils.<type>"

var messageUtils = {
  error : function(msg, targetId) {
    handleMessages([{type : 'error', msg : safeUtf8Decode(msg)}], targetId);
  },

  success : function(msg, targetId) {
    handleMessages([{type : 'success', msg : safeUtf8Decode(msg)}], targetId);
  },

  notice : function(msg, targetId) {
    handleMessages([{type : 'notice', msg : safeUtf8Decode(msg)}], targetId);
  },

  removeStyle : function(targetElementOrId, delay) {
    // 30 seconds, by default
    if (delay == undefined)
      delay = 30000;

    var $targetElement = $j(buildId(targetElementOrId));

    window.setTimeout(function() {
      $targetElement.removeClass('success').removeClass('error').removeClass('notice');
    }, delay);

  },

  handleMessages : function(messages, targetElementOrId) {

    var $feedbackMessages = $j('#feedback-messages');
    var $targetElement    = $j(buildId(targetElementOrId));
    var messagesType      = [];

    for (var i = 0; i < messages.length; i++) {
      var delay = messages[i].type == 'success' ? 5000 : 10000;

      $j('<p />').hide()
                 .data('target_id', $targetElement.attr('id'))
                 .addClass(messages[i].type)
                 .html(stringUtils.toUtf8(messages[i].msg))
                 .appendTo($feedbackMessages)
                 .fadeIn().delay(delay).fadeOut(function() {
                    $j(this).remove()
                  });

      if (messagesType.indexOf(messages[i].type < 0))
        messagesType.push(messages[i].type);
    }

    if ($targetElement) {
      $targetElement.removeClass('error').removeClass('success').removeClass('notice');

      $targetElement.addClass(messagesType.join(' '));
      messageUtils.removeStyle($targetElement);

      if (messagesType.indexOf('error') > -1)
        $targetElement.first().focus();
    }
  }
};

// backward compatibility
var handleMessages = messageUtils.handleMessages;


// when page is ready

(function($) {
  $(document).ready(function() {

    // add div for feedback messages
    $j('<div />').attr('id', 'feedback-messages').appendTo($j('#corpo'));

  }); // ready
})(jQuery);
