<span class="form">
    <select name="ref_cod_serie[]" id="ref_cod_serie" multiple="multiple" style="width: 308px;">
        @foreach(App_Model_IedFinder::getSeries(null, Request::get('ref_cod_escola'), Request::get('ref_cod_curso')) as $id => $name)
            <option value="{{$id}}">{{$name}}</option>
        @endforeach
    </select>
</span>

@push('scripts')
    <link type='text/css' rel='stylesheet' href='{{ Asset::get("/modules/Portabilis/Assets/Plugins/Chosen/chosen.css") }}'>
    <script type='text/javascript' src='{{ Asset::get('/modules/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js') }}'></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/MultipleSearchSerie.js") }}"></script>
    <script type='text/javascript'>
        (function ($) {
            $(document).ready(function () {
                multipleSearchSerieOptions = {}
                multipleSearchSerieOptions.placeholder = 'Selecione as séries';
                multipleSearchHelper.setup('ref_cod_serie', '', 'multiple', 'multiple', multipleSearchSerieOptions);
                $j('#ref_cod_serie').trigger('chosen:updated');
            });

            $(document).ready(function(){

                // serie search expect an id for escola
                var $instituicaoField = getElementFor('instituicao');

                var $escolaField = getElementFor('ref_cod_escola');

                var $cursoField = getElementFor('ref_cod_curso');
                var $serieField = getElementFor('ref_cod_serie');
                var $ano = getElementFor('ano');

                var handleGetSeries = function(resources) {
                    var selectOptions = jsonResourcesToSelectOptions(resources['options']);
                    updateSelect($serieField, selectOptions, "Selecione uma série");
                    $j('#ref_cod_serie').trigger('chosen:updated');
                }

                var updateSeries = function(){
                    updateSelect($serieField, [], "Selecione uma série");
                    $j('#ref_cod_serie').trigger('chosen:updated');

                    if ($instituicaoField.val() && $cursoField.val() && $cursoField.is(':enabled')) {
                        $serieField.children().first().html('Aguarde carregando...');

                        var urlForGetSeries = getResourceUrlBuilder.buildUrl('/module/DynamicInput/serie', 'series', {
                            instituicao_id: $instituicaoField.val(),
                            escola_id: $escolaField.val(),
                            curso_id: $cursoField.val(),
                            ano: $ano.val()
                        });

                        var options = {
                            url: urlForGetSeries,
                            dataType: 'json',
                            success: handleGetSeries
                        };

                        getResources(options);
                    }

                    $serieField.change();
                };

                // bind onchange event
                $cursoField.change(updateSeries);
                $ano.change(updateSeries);

            }); // ready
        })(jQuery);
    </script>
@endpush
@if(old('ref_cod_serie', Request::get('ref_cod_serie')))
    @php
        $grades = collect(old('ref_cod_serie', Request::get('ref_cod_serie')));

        if (is_array($grades[0])){
            $grades = $grades->map(function ($value) {
                return $value[0];
            });
        }
    @endphp

    @push('scripts')
        <script>
            (function($){
                $(document).ready(function() {
                    setTimeout(function() {
                        $('#ref_cod_serie').val([{{$grades->implode(',')}}]);
                        $('#ref_cod_serie').trigger('chosen:updated');
                    }, 1000);
                });
            })(jQuery);
        </script>
    @endpush
@endif

<style>
    .chosen-container-multi {
        width: 325px !important;
    }
</style>
