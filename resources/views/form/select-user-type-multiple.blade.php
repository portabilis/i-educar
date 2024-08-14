<span class="form">
    <select name="tipo_usuario[]" id="tipo_usuario" multiple="multiple" style="width: 308px;">
        @foreach(App_Model_IedFinder::getTiposUsuario() as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
    </select>
</span>

@push('scripts')
    <link type='text/css' rel='stylesheet'
          href='{{ Asset::get("/vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.css") }}'>
    <script type='text/javascript'
            src='{{ Asset::get('/vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js') }}'></script>
    <script type="text/javascript"
            src="{{ Asset::get("/vendor/legacy/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js") }}"></script>
    <script type='text/javascript'>
        (function ($) {
            $(document).ready(function () {
                multipleSearchOptions = {}
                multipleSearchOptions.placeholder = 'Selecione os tipos de usuário';
                multipleSearchHelper.setup('tipo_usuario', '', 'multiple', 'multiple', multipleSearchOptions);
                $j('#tipo_usuario').trigger('chosen:updated');
            });
        })(jQuery);
    </script>
@endpush
@if($userTypes = old('tipo_usuario', $userTypes))

    @php
        $userTypes = collect($userTypes);

        if ($userTypes->isNotEmpty() && is_array($userTypes[0])){
            $userTypes = $userTypes->map(function ($value) {
                return $value[0];
            });
        }
    @endphp

    @push('scripts')
        <script>
            (function ($) {
                $(document).ready(function () {
                    setTimeout(function () {
                        $('#tipo_usuario').val([{{$userTypes->implode(',')}}]);
                        $('#tipo_usuario').trigger('chosen:updated');
                    }, 1000);
                });
            })(jQuery);
        </script>
    @endpush
@endif
