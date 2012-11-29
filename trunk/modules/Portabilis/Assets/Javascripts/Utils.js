
// jquery utils

function buildId(id) {
  return typeof(id) == 'string' && id.length > 0 && id[0] != '#' ? '#' + id : id;
}

function safeLog(value) {
  if(typeof(console) != 'undefined' && typeof(console.log) == 'function')
    console.log(value);
}


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
    else if (typeof value == 'function')
      value = value(selfOptions);

    return value;
  },

  merge : function(defaultOptions, options) {
    if (typeof options == 'undefined')
      options = {};

    return $j.extend(defaultOptions, options);
  }
}

// string utils

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
  try {
    s = decodeURIComponent(escape(s));
  }
  catch(e) {
    safeLog('Erro ao decodificar string utf8: ' + s);
  }

  return s;
}


// feedback messages

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

    $j('<p />').addClass(messages[i].type).html(messages[i].msg).appendTo($feedbackMessages).delay(delay).fadeOut(function() {$j(this).remove()}).data('target_id', targetId);

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
      $targetElement.focus();
    }

    else if (hasSuccessMessages)
      $targetElement.addClass('success').removeClass('error').removeClass('notice');

    else if (hasNoticeMessages)
      $targetElement.addClass('notice').removeClass('error').removeClass('sucess');

    else
      $targetElement.removeClass('success').removeClass('error').removeClass('notice');

    if (useDelayClassRemoval) {
      window.setTimeout(function() {$targetElement.removeClass('success').removeClass('error').removeClass('notice');}, delayClassRemoval);
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