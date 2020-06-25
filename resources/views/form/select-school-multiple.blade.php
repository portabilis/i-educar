<span class="form">
    <select name="escola[]" id="escola" multiple="multiple" style="width: 308px;">
        <option value="">Selecione uma escola</option>
        @if(old('ref_cod_instituicao', Request::get('ref_cod_instituicao')))
            @foreach(App_Model_IedFinder::getEscolasByUser(old('ref_cod_instituicao', Request::get('ref_cod_instituicao'))) as $id => $name)
                <option value="{{$id}}">{{$name}}</option>
            @endforeach
        @endif
    </select>
</span>

@push('scripts')
    <script type='text/javascript'>
        (function ($) {
            $(document).ready(function () {
                multipleSearchEscolaOptions = {}
                multipleSearchEscolaOptions.placeholder = 'Selecione as escolas';
                multipleSearchHelper.setup('escola', '', 'multiple', 'multiple', multipleSearchEscolaOptions);
            });
        })(jQuery);
    </script>
    <link type='text/css' rel='stylesheet' href='{{ Asset::get("/modules/Portabilis/Assets/Plugins/Chosen/chosen.css") }}'>
    <script type='text/javascript' src='{{ Asset::get('/modules/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js') }}'></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/MultipleSearchEscola.js") }}"></script>
@endpush
@if(old('escola', Request::get('escola')))
    @push('scripts')
        <script>
            (function($){
                $(document).ready(function() {
                    $j('#escola').val({{old('escola', Request::get('escola'))}})
                });
            })(jQuery);
        </script>
    @endpush
@endif
