
// jquery utils

function buildId(id) {
  return typeof(id) == 'string' && id.length > 0 && id[0] != '#' ? '#' + id : id;
}


// console utils

function safeLog(value) {
  if(typeof(console) != 'undefined' && typeof(console.log) == 'function')
    console.log(value);
}


// form utils

function fixupFieldsWidth(){
  var maxWidth = 0;
  var $fields = $j('form select');

  //get maxWidh
  $j.each($fields, function(index, value){
    $value = $j(value);
    if ($value.outerWidth() > maxWidth)
      maxWidth = $value.outerWidth();
  });

  //set maxWidth
  $j.each($fields, function(index, value){
    $j(value).width(maxWidth);
  });
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
}

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
  },

}

// window utils

var windowUtils = {

  // open a new window, for more information see developer.mozilla.org/en-US/docs/DOM/window.open
  open : function(url, name, options) {
    var defaultOptions = {
      name : 'new_window',
      options : {},
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
      status      : 'no',
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
};

function handleMessages(messages, targetId, useDelayClassRemoval) {

  var $feedbackMessages = $j('#feedback-messages');
  var hasErrorMessages   = false;
  var hasSuccessMessages = false;
  var hasNoticeMessages  = false;
  var delayClassRemoval  = 20000;

  var $targetElement = buildId(targetId);
  var $targetElement = $j($targetElement);

  for (var i = 0; i < messages.length; i++) {
    if (messages[i].type == 'success')
      var delay = 5000;
    else if (messages[i].type != 'error')
      var delay = 10000;
    else
      var delay = 20000;

    $j('<p />').hide()
               .addClass(messages[i].type)
               .html(safeUtf8Decode(messages[i].msg))
               .appendTo($feedbackMessages)
               .fadeIn()
               .delay(delay)
               .fadeOut(function() { $j(this).remove() })
               .data('target_id', targetId);

    if (! hasErrorMessages && messages[i].type == 'error')
      hasErrorMessages = true;
    else if(! hasSuccessMessages && messages[i].type == 'success')
      hasSuccessMessages = true;
    else if(! hasNoticeMessages && messages[i].type == 'notice')
      hasNoticeMessages = true;
  }

  if($targetElement) {
    if (hasErrorMessages) {
      $targetElement.addClass('error').removeClass('success').removeClass('notice');
      $targetElement.first().focus();
    }

    else if (hasSuccessMessages)
      $targetElement.addClass('success').removeClass('error').removeClass('notice');

    else if (hasNoticeMessages)
      $targetElement.addClass('notice').removeClass('error').removeClass('sucess');

    else
      $targetElement.removeClass('success').removeClass('error').removeClass('notice');

    if (useDelayClassRemoval) {
      window.setTimeout(function() {
        $targetElement.removeClass('success').removeClass('error').removeClass('notice');
      }, delayClassRemoval);
    }
  }
}


// when page is ready

(function($) {
  $(document).ready(function() {

    // add div for feedback messages
    $j('<div />').attr('id', 'feedback-messages').appendTo($j('form').last().parent());

  }); // ready
})(jQuery);
