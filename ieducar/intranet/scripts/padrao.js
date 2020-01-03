/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license   @@license@@
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

/**
 * @TODO Remover função, chamadas no i-Educar comentadas (código nunca invocado).
 *   Ver: $ egrep -rn 'setFocus[ ]{0,3}\(' intranet/
 */
function setFocus(campo)
{
  if (document.getElementById) {
    var campo_ = document.getElementById(campo);
    campo_.focus();
  }
  else {
    if (document.forms[0]) {
      var elements_ = document.forms[0].elements;

      for (var ct = 0 ; ct < elements_.length ; ct++){
        if (elements_[ct].getAttribute('type') != 'hidden' && elements_[ct].disabled == false){
          elements_[ct].focus();
          break;
        }
      }
    }
  }
}

/**
 * @TODO Remover função, chamadas no i-Educar comentadas (código nunca invocado).
 *   Remover também o código legado que o referencia, funcionalidades inúteis.
 *   Ver: $ egrep -rn 'openurl[ ]{0,3}\(' intranet/
 */
function openurl(url)
{
  window.open(url, 'PROCURAR', 'width=800, height=300, top=10, left=10, scrollbars=yes');
}

/**
 * @TODO Remover função, chamadas no i-Educar são em código legado em
 *   funcionalidades inúteis.
 *   Ver: $ egrep -rn 'retorna[ ]{0,3}\(' intranet/
 */
function retorna(form, campo, valor)
{
  window.parent.document.getElementById(campo).value=valor;

  campo = campo + '_';
  window.parent.document.getElementById(campo).value=valor;

  window.parent.insereSubmit();
  window.close();

  window.parent.fechaExpansivel('div_dinamico_' + (parent.DOM_divs.length * 1 - 1));
}

/**
 * @TODO Remover função, chamadas no i-Educar são em código legado em
 *   funcionalidades inúteis.
 *   Ver: $ egrep -rn 'insereSubmit[ ]{0,3}\(' intranet/
 */
function insereSubmit()
{
  document.getElementById('tipoacao').value = '';
  document.getElementById('formcadastro').submit();
}

function insereSubmitLista()
{
  document.getElementById('tipoacao').value = '';
  document.getElementById('lista').value    = '1';

  document.getElementById('formcadastro').submit();
}

/**
 * @TODO Remover função após remover todos os arquivos legados não utilizados
 *   presentes no i-Educar. Ver: $ egrep -rn 'excluirSumit[ ]{0,3}\(' intranet/
 */
function excluirSumit(id, nome_campo)
{
  if (id && nome_campo) {
    document.getElementById(nome_campo).value = id;
  }

  if (id == 0) {
    document.getElementById(nome_campo).value = '0';
  }

  document.getElementById('tipoacao').value = '';
  document.getElementById('formcadastro').submit();
}


// Scripts originários de clsCampos.inc.php
function formataData(campo, e)
{
  if (typeof window.event != 'undefined') {
    if (window.event.keyCode != 47) {
      if ((campo.value.length == 2) || (campo.value.length == 5)) {
        campo.value += '/';
      }
    }
  }
  else {
    if (
      e.which != 47 && e.which != 45 && e.which != 46 && e.which != 8 &&
      e.which != 32 && e.which != 13 && e.which != 0
    ) {
      if ((campo.value.length == 2) || (campo.value.length == 5)) {
        campo.value += '/';
      }
    }
  }
}

function formataHora(campo, e, segundos = false)
{
  if (typeof window.event != 'undefined') {
    if (window.event.keyCode != 58) {
      if ((campo.value.length == 2)) {
        campo.value += ':';
      }

      if (segundos && (campo.value.length == 5)) {
        campo.value += ':';
      }
    }
  }
  else {
    if (
      e.which != 45 && e.which != 46 && e.which != 8 && e.which != 32 &&
      e.which != 13 && e.which != 0
    ) {
      if ((campo.value.length == 2)) {
        campo.value += ':';
      }

      if (segundos && (campo.value.length == 5)) {
        campo.value += ':';
      }
    }
  }
}

function formataCEP(campo, e)
{
  if (typeof window.event != 'undefined') {
    if (window.event.keyCode != 45) {
      if (campo.value.length == 5) {
        campo.value += '-';
      }
    }
  }
  else {
    if (
      e.which != 45 && e.which != 46 && e.which != 8 && e.which != 32 &&
      e.which != 13 && e.which != 0
    ) {
      if (campo.value.length == 5) {
        campo.value += '-';
      }
    }
  }
}

function formataCPF(campo, e)
{
  if (typeof window.event != 'undefined') {
    if (window.event.keyCode != 46) {
      if ((campo.value.length == 3) || (campo.value.length == 7)) {
        campo.value += '.';
      }
    }

    if (window.event.keyCode != 45) {
      if (campo.value.length == 11) {
        campo.value += '-';
      }
    }
  }
  else {
    if (e.which != 8) {
      if (e.which != 46) {
        if ((campo.value.length == 3) || (campo.value.length == 7)) {
          campo.value += '.';
        }
      }

      if (e.which != 45) {
        if (campo.value.length == 11) {
          campo.value += '-';
        }
      }
    }
  }
}

function formataIdFederal(campo, e)
{
  if (campo.value.length > 13) {
    if (typeof window.event != 'undefined') {
      if (
        window.event.keyCode != 45 && window.event.keyCode != 46 &&
        window.event.keyCode != 8
      ) {
        var str = campo.value;
        str = str.replace('.', '');
        str = str.replace('.', '');
        str = str.replace('-', '');
        str = str.replace('/', '');

        temp = str.substr(0, 2);
        if (temp.length == 2) {
          temp += '.';
        }

        temp += str.substr(2, 3);

        if (temp.length == 6) {
          temp += '.';
        }

        temp += str.substr(5, 3);

        if (temp.length == 10) {
          temp += '/';
        }

        temp += str.substr(8, 4);

        if (temp.length == 15) {
          temp += '-';
        }

        temp += str.substr(12, 2);
        campo.value= temp;
      }
    }
    else {
      if (
        e.which != 45 && e.which != 46 && e.which != 8 && e.which != 32 &&
        e.which != 13
      ) {
        var str = campo.value;
        str = str.replace('.', '');
        str = str.replace('.', '');
        str = str.replace('-', '');
        str = str.replace('/', '');

        temp = str.substr(0, 2);

        if (temp.length == 2) {
          temp += '.';
        }

        temp += str.substr(2, 3);

        if (temp.length == 6) {
          temp += '.';
        }

        temp += str.substr(5, 3);

        if (temp.length == 10) {
          temp += '/';
        }

        temp += str.substr(8, 4);

        if (temp.length == 15) {
          temp += '-';
        }

        temp += str.substr(12, 2);
        campo.value= temp;
      }
    }
  }
  else {
    if (typeof window.event != 'undefined') {
      if (
        window.event.keyCode != 45 && window.event.keyCode != 46 &&
        window.event.keyCode != 8
      ) {
        var str = campo.value;
        str = str.replace('.', '');
        str = str.replace('.', '');
        str = str.replace('/', '');
        str = str.replace('-', '');

        temp = str.substr(0, 3);

        if (temp.length == 3) {
          temp += '.';
        }

        temp += str.substr(3,3);

        if (temp.length == 7) {
          temp += '.';
        }

        temp += str.substr(6,3);

        if (temp.length == 11) {
          temp += '-';
        }

        temp += str.substr(9,2);
        campo.value= temp;
      }
    }
    else {
      if (
        e.which != 45 && e.which != 46 && e.which != 8 && e.which != 32 &&
        e.which != 13
      ) {
        var str = campo.value;
        str = str.replace('.', '');
        str = str.replace('.', '');
        str = str.replace('/', '');
        str = str.replace('-', '');

        temp = str.substr(0, 3);

        if (temp.length == 3) {
          temp += '.';
        }

        temp += str.substr(3, 3);

        if (temp.length == 7) {
          temp += '.';
        }

        temp += str.substr(6, 3);

        if (temp.length == 11) {
          temp += '-';
        }

        temp += str.substr(9, 2);
        campo.value= temp;
      }
    }
  }
}

function formataMonetario(campo, e)
{
  if (typeof window.event != 'undefined') {
    if (window.event.keyCode != 44 && window.event.keyCode != 46) {
      var valor = campo.value;

      valor = valor.replace(',', '');
      valor = valor.replace(' ', '');
      valor = valor.split('.').join('');
      valor = valor.split(',').join('');

      for (var i=0; i<valor.length; i++) {
        if (valor.substr(i,1) != 0) {
          valor = valor.substr(i);
          break;
        }
      }

      if (valor.length < 3) {
        if (valor.length == 2 && valor != 00) {
          campo.value = '0,' + valor;
        }
        else {
          campo.value = '';
        }

        if (valor.length == 1) {
          campo.value = '0,0' + valor;
        }

        if (valor.length == 0) {
          campo.value = '';
        }
      }
      else {
        var centavos = valor.substr(valor.length - 2, 2);
        var resto    = valor.substr(0, valor.length - 2);

        valor = '';

        var count = 0;

        for (var i = resto.length; i > 0; i--) {
          count++;

          if (count % 3 == 1 && count >1) {
            valor = resto.substr(i - 1, 1) + '.' + valor;
          }
          else {
            valor = resto.substr(i - 1, 1) + valor;
          }
        }

        campo.value = valor + ',' + centavos;
      }
    }
  }
  else {
    if (e.which != 46 && e.which != 44 ) {
      var valor = campo.value;

      valor = valor.replace(',', '');
      valor = valor.replace(' ', '');
      valor = valor.split('.').join('');
      valor = valor.split(',').join('');

      for (var i = 0; i < valor.length; i++) {
        if (valor[i] != 0) {
          valor = valor.substr(i);
          break;
        }
      }

      if (valor.length < 3) {
        if (valor.length == 2 && valor != 00) {
          campo.value = '0,' + valor;
        }
        else {
          campo.value = '';
        }

        if (valor.length == 1) {
          campo.value = '0,0' + valor;
        }

        if (valor.length == 0) {
          campo.value = '';
        }
      }
      else {
        var centavos = valor.substr(valor.length - 2, 2);
        var resto    = valor.substr(0, valor.length - 2);

        valor = '';

        var count = 0;

        for (var i = resto.length; i > 0; i--) {
          count++;

          if (count % 3 == 1 && count >1) {
            valor = resto.substr(i - 1, 1) + '.' + valor;
          }
          else {
            valor = resto.substr(i - 1, 1) + valor;
          }
        }

        campo.value = valor + ',' + centavos;
      }
    }
  }
}

function formataCNPJ(campo, e)
{
  if (typeof window.event != 'undefined') {
    if (window.event.keyCode != 46) {
      if ((campo.value.length == 2) || (campo.value.length == 6)) {
        campo.value += '.';
      }
    }

    if (window.event.keyCode != 47) {
      if (campo.value.length == 10) {
        campo.value += '/';
      }
    }

    if (window.event.keyCode != 45) {
      if (campo.value.length == 15) {
        campo.value += '-';
      }
    }
  }
  else {
    if (e.which != 8) {
      if (e.which != 46) {
        if ((campo.value.length == 2) || (campo.value.length == 6)) {
          campo.value += '.';
        }
      }

      if (e.which != 47) {
        if (campo.value.length == 10) {
          campo.value += '/';
        }
      }

      if (e.which != 45) {
        if (campo.value.length == 15) {
          campo.value += '-';
        }
      }
    }
  }
}

/**
 * @TODO Remover função. Aparentemente nunca é chamada, a única página que chama
 *   clsCampos::campoTextoPesquisa() (intranet/funcionario_cad.php) não entra
 *   no trecho de código que gera o output HTML para esta função.
 */
function pesquisa_valores_f(caminho, campo, flag, pag_cadastro)
{
  jar = window.open(caminho + '?campo=' + campo + '&flag=' + flag + '&pag_cadastro=' +
    pag_cadastro, 'JANELAPESQUISA', 'width=800, height=300, scrollbars=yes');

  jar.focus();
}

function pesquisa_valores_popless(caminho, campo)
{
  new_id = DOM_divs.length;
  div    = 'div_dinamico_' + new_id;

  if (caminho.indexOf('?') == -1) {
    showExpansivel(500, 500, '<iframe src="' + caminho + '?campo=' + campo + '&div=' + div + '&popless=1" frameborder="0" height="100%" width="500" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', 'Pesquisa de valores');
  }
  else {
    showExpansivel(500, 500, '<iframe src="' + caminho + '&campo=' + campo + '&div=' + div + '&popless=1" frameborder="0" height="100%" width="500" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', 'Pesquisa de valores');
  }
}

function MenuCarregaDados(key, ordem, menu_pai, menu, submenu, titulo, ico_menu,
  alvo, suprime_menu)
{
  texto = titulo;

  if (submenu==0) {
    texto = 'Selecione';
  }

  submenuLen = document.getElementById('ref_cod_menu_submenu').options.length;

  document.getElementById('ref_cod_menu_submenu').options[submenuLen] =
    new Option (texto, submenu, true, true);

  document.getElementById('ord_menu').value             = ordem;
  document.getElementById('ref_cod_menu_pai').value     = menu_pai;
  document.getElementById('ref_cod_menu').value         = menu;
  document.getElementById('ref_cod_menu_submenu').value = submenu;
  document.getElementById('tt_menu').value              = titulo;
  document.getElementById('img_banco_').value           = ico_menu;
  document.getElementById('img_banco').value            = ico_menu;
  document.getElementById('alvo').value                 = alvo;
  document.getElementById('suprime_menu').value         = suprime_menu;
  document.getElementById('editar').value               = key;
  document.getElementById('editando').value             = '1';
  document.getElementById('id_deletar').value           = key;
}

function MenuExcluiDado()
{
  document.getElementById('editando').value = '2';
  document.getElementById('tipoacao').value = '';
  document.getElementById('formcadastro').submit();
}

// Exibe ou esconde um campo da tela
function setVisibility(f, visible)
{
  var field = typeof(f) == 'object' ? f : document.getElementById(f);

  var browser = navigator.appName;

  if (browser.indexOf("Netscape") == 0){
    // Netscape e Mozilla
    if (field) {
      field.style.visibility = (visible == true) ? 'visible' : 'collapse';
    }
    else {
      f.style.visibility = (visible == true) ? 'visible' : 'collapse';
    }
  }
  else {
    // IE
    if (field) {
      field.style.display = (visible == true) ? 'inline' : 'none';
    }
    else {
      f.style.display = (visible == true) ? 'inline': 'none';
    }
  }
}

// Retorna true se o campo estiver visivel
function getVisibility(f)
{
  var field   = document.getElementById(f);
  var browser = navigator.appName;

  field = field ? field : f;

  if (browser.indexOf('Netscape') == 0){
    // Netscape e Mozilla
    if (field.style.visibility == 'visible' || field.style.visibility == '') {
      return true;
    }
    else if (field.style.visibility == 'collapse') {
      return false;
    }
  }
  else{
    // IE
    if (field.style.display == 'inline' || field.style.display == 'block' || field.style.display == '') {
      return true;
    }
    else if (field.style.display == 'none') {
      return false;
    }
  }
}

function cv_set_campo(campo1, valor1, campo2, valor2, campo3, valor3, campo4,
  valor4, campo5, valor5, campo6, valor6, campo7, valor7, campo8, valor8, campo9,
  valor9, campo10, valor10, campo11, valor11, campo12, campo13, valor13,
  campo14, valor14)
{
  obj1          = parent.document.getElementById(campo1);

  if (obj1){
    obj1.value    = valor1;
  }

  obj2       = parent.document.getElementById(campo2);

  if (obj2)
    obj2.value = valor2;

  obj3       = parent.document.getElementById(campo3);

  if (obj3)
    obj3.value = valor3;

  obj4          = parent.document.getElementById(campo4);

  if (obj4){
    obj4.value    = valor4;
  }

  obj5       = parent.document.getElementById(campo5);

  if (obj5)
    obj5.value = valor5;


  obj6       = parent.document.getElementById(campo6);

  if (obj6)
    obj6.value = valor6;

  obj7          = parent.document.getElementById(campo7);

  if (obj7){
    obj7.value    = valor7;
  }

  obj8       = parent.document.getElementById(campo8);

  if (obj8)
    obj8.value = valor8;

  obj9 = parent.document.getElementById(campo9);

  if (obj9) {
    obj9.value = valor9;
  }

  obj10          = parent.document.getElementById(campo10);

  if (obj10){
    obj10.value    = valor10;
  }

  obj11          = parent.document.getElementById(campo11);

  if (obj11){
    obj11.value    = valor11;
  }

  obj12          = parent.document.getElementById(campo12);

  if (obj12){
    obj12.value    = valor8;
    obj12.disabled = true;
  }

  if (parent.document.getElementById(campo13)) {
    obj13       = parent.document.getElementById(campo13);
    obj13.value = valor13;
  }

  if (parent.document.getElementById(campo14)) {
    obj14          = parent.document.getElementById(campo14);
    obj14.value    = valor14;
    obj14.disabled = true;
  }

  window.parent.fechaExpansivel('div_dinamico_' + (parent.DOM_divs.length * 1 - 1));

  if(parent.afterSetSearchFields)
    parent.afterSetSearchFields(self);
}

function cv_libera_campos(campo1, campo2, campo3, campo4, campo5, campo6, campo7, campo8)
{
  window.parent.fechaExpansivel('div_dinamico_' + (parent.DOM_divs.length * 1 - 1));

  parent.document.getElementById(campo1).disabled = false;
  parent.document.getElementById(campo1).value    = '';
  parent.document.getElementById(campo2).disabled = false;
  parent.document.getElementById(campo2).value    = false;
  parent.document.getElementById(campo3).disabled = false;
  parent.document.getElementById(campo3).value    = '';
  parent.document.getElementById(campo4).disabled = false;
  parent.document.getElementById(campo4).value    = '';
  parent.document.getElementById(campo5).disabled = false;
  parent.document.getElementById(campo5).value    = '';
  parent.document.getElementById(campo6).disabled = false;
  parent.document.getElementById(campo6).value    = '';

  if (parent.document.getElementById(campo7)) {
    parent.document.getElementById(campo7).disabled = false;
    parent.document.getElementById(campo7).value    = '1';
  }

  if (parent.document.getElementById(campo8)) {
    parent.document.getElementById(campo8).disabled = false;
    parent.document.getElementById(campo8).value    = '1';
  }

  if(parent.afterUnsetSearchFields)
    parent.afterUnsetSearchFields(self);
}

// Função a ser executada antes de fechar a janela.
var exec = null;

function set_campo_pesquisa()
{
  var i = 0;
  var submit = false;

  while (i < arguments.length) {
    if (typeof arguments[i] != 'undefined' && arguments[i] != 'submit') {
      if (parent.document.getElementById(arguments[i])) {
        obj = parent.document.getElementById(arguments[i]);
      }
      else if (window.opener.document.getElementById(arguments[i])) {
        obj = window.opener.document.getElementById(arguments[i]);
      }

      if (obj.type == 'select-one') {
        novoIndice              = obj.options.length;
        obj.options[novoIndice] = new Option(arguments[i + 2]);
        opcao                   = obj.options[novoIndice];
        opcao.value             = arguments[i + 1];
        opcao.selected          = true;

        obj.onchange();

        i += 3;
      }
      else {
        obj.value =  arguments[i + 1];
        i         += 2;
      }
    }
    else if (arguments[i] == 'submit') {
      submit = true;
      i     += 1;
    }
  }

  if (submit) {
    if (window == top) {
      tmpObj = window.opener.document.getElementById('tipoacao')

      if (hasProperties(tmpObj)) {
        tmpObj.value = '';
      }

      tmpObj = window.opener.document.getElementById('formcadastro')

      if (hasProperties(tmpObj)) {
        tmpObj.submit();
      }
    }
    else {
      tmpObj = window.parent.document.getElementById('tipoacao')

      if (hasProperties(tmpObj)) {
        tmpObj.value = '';
      }

      tmpObj = window.parent.document.getElementById( 'formcadastro' )
      if (hasProperties(tmpObj)) {
        tmpObj.submit();
      }
    }
  }

  if (exec) {
    exec();
  }

  if (window == top) {
    window.close();
  }
  else {
    window.parent.fechaExpansivel('div_dinamico_' + (parent.DOM_divs.length * 1 - 1));
  }
}

// Retorna 0 caso não tenha propriedades
function hasProperties(obj)
{
  prop = '';
  if (typeof obj == 'string') {
    obj = document.getElementById(obj);
  }

  if (typeof obj == 'object') {
    for (i in obj) {
      prop += i;
    }
  }

  return prop.length;
}

function enviar()
{
  if (
    (typeof arguments[0] != 'undefined') &&
    (typeof arguments[1] != 'undefined')
  ) {
    window.opener.addVal(arguments[0], arguments[1]);
  }

  if (typeof arguments[2] != 'undefined') {
    window.opener.document.getElementById('formcadastro').submit();
  }

  window.close();
}

function getElementsByClassName(oElm, strTagName, strClassName)
{
  var arrElements = (strTagName == '*' && document.all) ?
    document.all : oElm.getElementsByTagName(strTagName);

  var arrReturnElements = new Array();
  strClassName = strClassName.replace(/\-/g, '\\-');

  var oRegExp = new RegExp('(^|\\s)' + strClassName + '(\\s|$)');
  var oElement;

  for (var i=0; i<arrElements.length; i++) {
    oElement = arrElements[i];

    if (oRegExp.test(oElement.className)) {
      arrReturnElements.push(oElement);
    }
  }

  return (arrReturnElements);
}

function mudaClassName(strClassTargetName, strNewClassName)
{
  tagName = (arguments[2]) ? arguments[2]: '*';
  arrObjs = getElementsByClassName( document, tagName, strClassTargetName );

  for (i in arrObjs) {
    arrObjs[i].className = strNewClassName;
  }
}

function addEvent_(evt, func, field)
{
  if (! field) {
    field = window;
  }
  else {
    field = document.getElementById(field);
  }

  if (field.addEventListener) {
    // Mozilla
    field.addEventListener(evt, func, false);
  }
  else if (field.attachEvent) {
    // IE
    field.attachEvent('on' + evt, func);
  }
}
