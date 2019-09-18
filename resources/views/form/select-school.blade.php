<span class="form">
    <select class="geral" name="ref_cod_escola" id="ref_cod_escola" style="width: 308px;">
        <option value="">Selecione uma escola</option>
        @if(Request::get('ref_cod_instituicao'))
            @foreach(App_Model_IedFinder::getEscolasByUser(Request::get('ref_cod_instituicao')) as $id => $name)
                <option value="{{$id}}">{{$name}}</option>
            @endforeach
        @endif
    </select>
</span>

@if(Request::get('ref_cod_escola'))
    @push('scripts')
        <script>
            (function($){
                $(document).ready(function() {
                    $j('#ref_cod_escola').val({{Request::get('ref_cod_escola')}})
                });
            })(jQuery);
        </script>
    @endpush
@endif
