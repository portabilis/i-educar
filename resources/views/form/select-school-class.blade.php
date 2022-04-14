<span class="form">
    <select class="geral" name="ref_cod_turma" id="ref_cod_turma" style="width: 308px;">
        <option value="">Selecione uma turma</option>
        @if(old('ref_cod_escola', Request::get('ref_cod_escola')) && old('ref_cod_serie', Request::get('ref_cod_serie')) && old('ano', Request::get('ano')))
            @foreach(App_Model_IedFinder::getTurmas(old('ref_cod_escola', Request::get('ref_cod_escola')), old('ref_cod_serie', Request::get('ref_cod_serie')), old('ano', Request::get('ano')), true) as $id => $name)
                <option value="{{$id}}">{{$name}}</option>
            @endforeach
        @endif
    </select>
</span>

@if(old('ref_cod_turma', Request::get('ref_cod_turma')))
    @push('scripts')
        <script>
            (function($){
                $(document).ready(function() {
                    $j('#ref_cod_turma').val({{old('ref_cod_turma', Request::get('ref_cod_turma'))}})
                });
            })(jQuery);
        </script>
    @endpush
@endif
