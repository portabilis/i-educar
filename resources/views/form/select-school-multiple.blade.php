<span class="form">
    <select name="escola[]" id="escola" multiple="multiple" style="width: 308px;">
        @if(old('ref_cod_instituicao', Request::get('ref_cod_instituicao')))
            @foreach(App_Model_IedFinder::getEscolasByUser(old('ref_cod_instituicao', Request::get('ref_cod_instituicao'))) as $id => $name)
                <option value="{{$id}}">{{$name}}</option>
            @endforeach
        @endif
    </select>
</span>

@push('scripts')
    <link type='text/css' rel='stylesheet' href='{{ Asset::get("/modules/Portabilis/Assets/Plugins/Chosen/chosen.css") }}'>
    <script type='text/javascript' src='{{ Asset::get('/modules/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js') }}'></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/MultipleSearchEscola.js") }}"></script>
    <script type='text/javascript'>
        (function ($) {
            $(document).ready(function () {
                multipleSearchEscolaOptions = {}
                multipleSearchEscolaOptions.placeholder = 'Selecione as escolas';
                multipleSearchHelper.setup('escola', '', 'multiple', 'multiple', multipleSearchEscolaOptions);
                $j('#escola').trigger('chosen:updated');
            });
        })(jQuery);
    </script>
@endpush
@if(old('escola', Request::get('escola')))
    @php
        $schools = collect(old('escola', Request::get('escola')));

        if (is_array($schools[0])){
            $schools = $schools->map(function ($value) {
                return $value[0];
            });
        }
    @endphp

    @push('scripts')
        <script>
            (function($){
                $(document).ready(function() {
                    setTimeout(function() {
                        $('#escola').val([{{$schools->implode(',')}}]);
                        $('#escola').trigger('chosen:updated');
                    }, 1000);
                });
            })(jQuery);
        </script>
    @endpush
@endif
