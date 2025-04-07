<span class="form">
    <select class="geral" name="matricula" id="matricula" style="width: 308px;">
        <option value="">Selecione uma matrícula</option>
        @if(old('ref_cod_turma', Request::get('ref_cod_turma')))
            @foreach(App_Model_IedFinder::getMatriculas(old('ref_cod_turma', Request::get('ref_cod_turma'))) as $id => $name)
                <option value="{{$id}}">{{$name}}</option>
            @endforeach
        @endif
    </select>
</span>

@if(old('matricula', Request::get('matricula')))
    @push('scripts')
        <script>
            (function($){
                $(document).ready(function() {
                    $j('#matricula').val({{old('matricula', Request::get('matricula'))}})
                });
            })(jQuery);
        </script>
    @endpush
@endif
