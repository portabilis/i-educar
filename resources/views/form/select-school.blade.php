<span class="form">
    <select class="geral" name="ref_cod_escola" id="ref_cod_escola" style="width: 308px;">
        <option value="">Selecione uma escola</option>
        @if(old('ref_cod_instituicao', Request::get('ref_cod_instituicao')))
            @foreach(App_Model_IedFinder::getEscolasByUser(old('ref_cod_instituicao', Request::get('ref_cod_instituicao'))) as $id => $name)
                <option value="{{$id}}">{{ Str::upper($name) }}</option>
            @endforeach
        @endif
    </select>
</span>

@if(old('ref_cod_escola', Request::get('ref_cod_escola')))
    @push('scripts')
        <script>
            (function($){
                $(document).ready(function() {
                    $j('#ref_cod_escola').val({{old('ref_cod_escola', Request::get('ref_cod_escola'))}})
                });
            })(jQuery);
        </script>
    @endpush
@endif
