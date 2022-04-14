var isIE = (navigator.appName.indexOf('Microsoft')!= -1) ? 1 : 0

if (! isIE) {
  window.addEventListener('resize', updateMessagePosition, false);
}
else {
  window.onresize = updateMessagePosition;
}

function updateMessagePosition()
{
  if (! last_td) {
    return;
  }

  tamanhoTela();
  var subtract;

  if (findPosX(last_td) > winW / 2) {
    if (navigator.appName.indexOf('Microsoft') == 0) {
      subtract = -317 + 45 +20;
    }
    else {
      subtract = -317 + 45;
    }
  }
  else {
    subtract = 2;
  }

  last.style.left = (findPosX(last_td) + subtract) + 'px';
  last.style.top  = (findPosY(last_td) + 2) + 'px';
}

var cX = 0;
var cY = 0;

var last;
var last_td;
var fechou = false;

function AssignPosition(d,m,y,nome)
{
  var dd = document.getElementById(nome + '_td_dia_' + d + '' + m + '' + y);
  var d  = document.getElementById(nome + '_div_dia_' + d + '' + m + '' + y);

  last_td = dd;
  tamanhoTela();

  var subtract;

  if (findPosX(dd) > winW/2) {
    if (navigator.appName.indexOf('Microsoft') == 0) {
      subtract = -317 + 72 + 20;
    }
    else {
      subtract = -317 + 72;
    }
  }
  else {
    subtract = 2;
  }

  d.style.left = (findPosX(dd) + subtract) + 'px';

  d.style.top = (findPosY(dd) + 2) + 'px';
}

function HideContent(event, d, m, y, nome)
{
  if (d.length < 1) {
    return;
  }

  if (window.event) {
    current = this;
    related = window.event.toElement;
  }
  else {
    current = event.currentTarget;
    related = event.relatedTarget;
  }

  var dv = document.getElementById(nome + '_div_dia_'+ d + '' + m + '' + y);

  b = related;

  while (b.parentNode) {
    if ((b = b.parentNode) == dv && (b.tagName == 'DIV' || b.tagName == 'TD')) {
      return true;
    }
  }

  var nome_div = nome + '_div_dia_'+ d + '' + m + '' + y;

  setTimeout('esconder(' + nome_div + ');', 500);
  setInterval('fechou=false;', 1000);
}

function esconder(el)
{
  fechou = true;
  el.style.display = 'none';
}

function ShowContent(d, m, y, nome)
{
  if (fechou) {
    return;
  }

  var dd = document.getElementById(nome + '_div_dia_' + d + '' + m + '' + y);

  if (dd != last && last != null) {
    last.style.display = 'none';
  }

  if (d.length < 1) {
    return;
  }

  var dd = document.getElementById(nome + '_div_dia_' + d + '' + m + '' + y);

  AssignPosition(d, m, y, nome);
  dd.style.display = '';
  last = dd;
}

function ReverseContentDisplay(d, m, y, nome)
{
  if (d.length < 1) {
    return;
  }

  var dd = document.getElementById(nome + '_div_dia_' + d + '' + m + '' + y);

  AssignPosition(dd);

  if (dd.style.display == 'none') {
    dd.style.display = '';
  }
  else {
    dd.style.display = 'none';
  }
}

function findPosX(obj)
{
  var curleft = 0;

  if (obj.offsetParent) {
    while (obj.offsetParent) {
      curleft += obj.offsetLeft
      obj = obj.offsetParent;
    }
  }
  else if (obj.x) {
    curleft += obj.x;
  }

  return curleft;
}

function findPosY(obj)
{
  var curtop = 0;

  if (obj.offsetParent) {
    while (obj.offsetParent) {
      curtop += obj.offsetTop
      obj = obj.offsetParent;
    }
  }
  else if (obj.y) {
    curtop += obj.y;
  }

  return curtop;
}

var winW;
var winH;

function tamanhoTela()
{
  if (typeof(window.innerWidth ) == 'number') {
      winW = window.innerWidth;
      winH = window.innerHeight;
  }
  else if (
    document.documentElement &&
    (document.documentElement.clientWidth || document.documentElement.clientHeight)
  ) {
    /* IE 6+ in 'standards compliant mode' */
    winW = document.documentElement.clientWidth;
    winH = document.documentElement.clientHeight;
  }
  else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
    /*IE 4 compatible*/
    winW = document.body.clientWidth;
    winH = document.body.clientHeight;
  }
}

function acaoCalendario(nome, dia, mes, ano)
{
  document.getElementById('cal_nome').value = nome;
  document.getElementById('cal_dia').value  = dia;
  document.getElementById('cal_mes').value  = mes;
  document.getElementById('cal_ano').value  = ano;

  document.getElementById('form_calendario').submit();
}
