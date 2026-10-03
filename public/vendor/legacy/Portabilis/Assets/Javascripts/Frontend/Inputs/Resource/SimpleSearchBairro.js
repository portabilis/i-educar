var simpleSearchNeighborhoodOptions = {
  canSearch: function () {
    return true;
  },
  autocompleteOptions: {
    source: function (request, response) {
      simpleSearch.search(this.element, request, function (results) {
        var term = $j.trim(request.term);
        var normalizedTerm = normalizeNeighborhood(term);

        var hasExactMatch = $j(results).toArray().some(function (item) {
          return normalizeNeighborhood(item.value) === normalizedTerm;
        });
                
        results.sort(function(a, b) {
          return a.label.localeCompare(b.label, 'pt-BR');
        });

        if (term && !hasExactMatch) {
          results.push({
            value: term,
            label: 'Criar Bairro: ' + term,
            createNeighborhood: true,
          });
        }

        response(results);
      });
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

      function extractNeighborhoodName(str) {
        if (typeof str !== 'string') return str;
        var separatorIndex = str.lastIndexOf(' / ');
        if (separatorIndex !== -1) {
          return str.substring(0, separatorIndex).trim();
        }
        return str;
      }

      ui.item.value = extractNeighborhoodName(ui.item.value);
      ui.item.id = extractNeighborhoodName(ui.item.id);
      ui.item.label = extractNeighborhoodName(ui.item.label);

      return simpleSearch.handleSelect(event, ui);
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