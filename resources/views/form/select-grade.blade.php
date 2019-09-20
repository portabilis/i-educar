<span class="form">
    <select class="geral" name="ref_cod_serie" id="ref_cod_serie" style="width: 308px;">
        <option value="">Selecione um serie</option>
        @if(Request::get('ref_cod_escola') && Request::get('ref_cod_curso'))
            @foreach(App_Model_IedFinder::getSeries(null, Request::get('ref_cod_escola'), Request::get('ref_cod_curso')) as $id => $name)
                <option value="{{$id}}">{{$name}}</option>
            @endforeach
        @endif
    </select>
</span>

@if(Request::get('ref_cod_serie'))
    @push('scripts')
        <script>
            (function($){
                $(document).ready(function() {
                    $j('#ref_cod_serie').val({{Request::get('ref_cod_serie')}})
                });
            })(jQuery);
        </script>
    @endpush
@endif
