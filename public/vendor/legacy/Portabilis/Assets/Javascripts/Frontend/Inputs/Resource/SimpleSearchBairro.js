var simpleSearchNeighborhoodOptions = {
  canSearch: function () {
    return true;
  },
  autocompleteOptions: {
    autoFocus: false,

    source: function (request, response) {
      var $element = $j(this.element);
      var term = $j.trim(request.term);

      if (!term) {
        response([]);
        return;
      }

      var normalizedTerm = normalizeNeighborhood(term);

      simpleSearch.search(this.element, { term: term }, function (results) {
        results = results.filter(function (item) {
          return normalizeNeighborhood(extractNeighborhoodName(item.value)).indexOf(normalizedTerm) !== -1;
        });

        var hasExactMatch = results.some(function (item) {
          return normalizeNeighborhood(extractNeighborhoodName(item.value)) === normalizedTerm;
        });

        results.sort(function(a, b) {
          return a.label.localeCompare(b.label, 'pt-BR');
        });

        $element.data('neighborhood-last-results', results.slice());

        if (!hasExactMatch) {
          results.push({
            value: term,
            label: 'Criar Bairro: ' + term,
            createNeighborhood: true,
          });
        }

        response(results);
      });
    },

    focus: function (event, ui) {
      var original = event.originalEvent;
 
      if (original && /^key/.test(original.type)) {
        $j(event.target).val(
          ui.item.createNeighborhood ? ui.item.value : extractNeighborhoodName(ui.item.value)
        );
      }
 
      return false;
    },

    select: function (event, ui) {
      var $element = $j(event.target);
      var $hiddenInput = $element.data('hidden-input-id');
            
      if (ui.item.createNeighborhood) {
        $element.val(ui.item.value);
        if (typeof $hiddenInput.val === 'function') {
          $hiddenInput.val(ui.item.value);
          $hiddenInput.trigger('change');
        }
        return false;
      }

      ui.item.value = extractNeighborhoodName(ui.item.value);
      ui.item.id = extractNeighborhoodName(ui.item.id);
      ui.item.label = extractNeighborhoodName(ui.item.label);

      return simpleSearch.handleSelect(event, ui);
    },
    
    change: function (event) {
      var $element = $j(event.target);
      var $hiddenInput = $element.data('hidden-input-id');
      var typed = $j.trim($element.val());
      var lastResults = $element.data('neighborhood-last-results') || [];
      var normalizedTyped = normalizeNeighborhood(typed);
 
      for (var i = 0; i < lastResults.length; i++) {
        var officialName = extractNeighborhoodName(lastResults[i].value);
 
        if (normalizeNeighborhood(officialName) === normalizedTyped) {
          typed = officialName;
          break;
        }
      }
 
      $element.val(typed);
 
      if (typeof $hiddenInput.val === 'function' && $hiddenInput.val() !== typed) {
        $hiddenInput.val(typed);
        $hiddenInput.trigger('change');
      }
    },
  },
};

function normalizeNeighborhood(value) {
    return value
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();
}

function extractNeighborhoodName(str) {
  if (typeof str !== 'string') return str;
  var separatorIndex = str.lastIndexOf(' / ');
  if (separatorIndex !== -1) {
    return str.substring(0, separatorIndex).trim();
  }
  return str;
}
