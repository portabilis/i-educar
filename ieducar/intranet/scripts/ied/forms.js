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
   * @see ied_forms.getElementsByName
   */
  this.checkAll = function(docObj, formId, fieldsName) {
    if (checker === 0) {
      checker = 1;
    } else {
      checker = 0;
    }

    var elements = ied_forms.getElementsByName(docObj, formId, fieldsName);
    for (e in elements) {
      elements[e].checked = checker == 1 ? true : false;
    }
  };

  /**
   * Faz um bind de eventos para um elemento HTML. Baseia-se nos métodos de
   * eventos W3C e clássico. O método do Internet Explorer (attachEvent) é
   * ignorado pois passa os argumentos das funções anônimas com cópia e sim
   * por referência, fazendo com que as variáveis this referenciem o objeto
   * window global.
   *
   * Para registrar diversas funções como listener ao evento, crie uma função
   * anônima:
   *
   * <code>
   * window.load = function() {
   *   var events = function() {
   *     function1(params);
   *     function2(params);
   *     functionN(params);
   *   }
   *   new ied_forms.bind(document, 'formId', 'myRadios', 'click', events);
   * }
   * </code>
   *
   * @param document docObj
   * @param string   formId
   * @param string   fieldsName
   * @param string   eventType      O tipo de evento para registrar o evento
   *   (listener), sem a parte 'on' do nome. Exemplos: click, focus, mouseout.
   * @param string   eventFunction  Uma função listener para o evento. Para
   *   registrar várias funções, crie uma função anônima.
   * @see ied_forms.getElementsByName
   * @link http://www.quirksmode.org/js/events_advanced.html Advanced event registration models
   * @link http://www.quirksmode.org/js/events_tradmod.html Traditional event registration model
   * @link http://javascript.about.com/library/bldom21.htm Cross Browser Event Processing
   * @link http://www.w3schools.com/jsref/dom_obj_event.asp Event Handlers
   */
  this.bind = function(docObj, formId, fieldsName, eventType, eventFunction) {
    var elements = ied_forms.getElementsByName(docObj, formId, fieldsName);

    for (e in elements) {
      if (elements[e].addEventListener) {
        elements[e].addEventListener(eventType, eventFunction, false);
      }
      else {
        // Usa o modo tradicional de registro de eventos ao invés do método
        // nativo do Internet Explorer (attachEvent).
        elements[e]['on' + eventType] = eventFunction;
      }
    }
  };

  /**
   * Retorna objetos de um formulário ao qual o nome (atributo name) seja
   * equivalente ao argumento fieldsName. Esse argumento aceita expressões
   * regulares, o que o torna mais flexível para atribuir eventos ou atributos
   * a múltiplos elementos da árvore DOM.
   *
   * @param document docObj      Um objeto document, geralmente o objeto global document.
   * @param string   formId      O atributo "id" do formulário.
   * @param string   fieldsName  O nome do elemento de formulário ou uma string Regex.
   * @return Array   Um array com os elementos encontrados.
   */
  this.getElementsByName = function(docObj, formId, fieldsName) {
    var regex = new RegExp(fieldsName);
    var form = docObj.getElementById(formId);
    var matches = [];
    var matchId = 0;

    for (var i = 0; i < form.elements.length; i++) {
      var elementName = form.elements[i].name;
      if (null !== elementName.match(regex)) {
        matches[matchId++] = form.elements[i];
      }
    }

    return matches;
  };
};
