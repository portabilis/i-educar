<span class="form">
    <select class="geral" name="ref_cod_curso" id="ref_cod_curso" style="width: 308px;">
        <option value="">Selecione um curso</option>
        @if(Request::get('ref_cod_escola'))
            @foreach(App_Model_IedFinder::getCursos(Request::get('ref_cod_escola')) as $id => $name)
                <option value="{{$id}}">{{$name}}</option>
            @endforeach
        @endif
    </select>
</span>

@if(Request::get('ref_cod_curso'))
    @push('scripts')
        <script>
            (function($){
                $(document).ready(function() {
                    $j('#ref_cod_curso').val({{Request::get('ref_cod_curso')}})
                });
            })(jQuery);
        </script>
    @endpush
@endif
