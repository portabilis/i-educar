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
 * @author    Eriksen Costa <eriksen.paixao_bs@cobra.com.br>
 * @license   @@license@@
 * @since     Arquivo disponível desde a versão 2.0.0
 * @version   $Id$
 */

/**
 * Closure com funções utilitárias para o manuseamento de formulários.
 */
var ied_forms = new function() {
  var checker = 0;

  /**
   * Seleciona/deseleciona campos checkbox de um formulário. Cada chamada ao
   * método executa uma ação de forma alternada: a primeira vez, altera a
   * propriedade dos checkboxes para "checked", na segunda, remove a
   * propriedade "checked" dos mesmos. Esse padrão segue nas chamadas
   * subsequentes.
   *
   * @param document docObj
   * @param string   formId
   * @param string   fieldsName
   */
  this.checkAll = function(docObj, formId, fieldsName) {
    if (checker === 0) {
      checker = 1;
    } else {
      checker = 0;
    }

    var regex = new RegExp(fieldsName);
    var form  = docObj.getElementById(formId);

    for (var i = 0; i < form.elements.length; i++) {
      var elementName = form.elements[i].name;
      if (null !== elementName.match(regex)) {
        form.elements[i].checked = checker == 1 ? true : false;
      }
    }
  };
};