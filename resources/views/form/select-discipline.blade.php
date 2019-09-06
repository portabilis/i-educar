<span class="form">
    <select class="geral" name="ref_cod_componente_curricular" id="ref_cod_componente_curricular" style="width: 308px;">
        <option value="">Selecione um componente curricular</option>
        @if(Request::get('ref_cod_escola') && Request::get('ref_cod_serie'))
            {{-- todo: mover para service --}}
            @foreach(\App\Models\LegacySchoolGradeDiscipline::where('ref_ref_cod_escola', Request::get('ref_cod_escola'))
            ->where('ref_ref_cod_serie', Request::get('ref_cod_serie'))
            ->get() as $discipline)
                <option value="{{$discipline->discipline->id}}">{{$discipline->discipline->nome}}</option>
            @endforeach
        @endif
    </select>
</span>

@if(Request::get('ref_cod_componente_curricular'))
    @push('scripts')
        <script>
            (function($){
                $(document).ready(function() {
                    $j('#ref_cod_componente_curricular').val({{Request::get('ref_cod_componente_curricular')}})
                });
            })(jQuery);
        </script>
    @endpush
@endif
