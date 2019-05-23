<span class="form">
    <select class="geral" name="ref_cod_instituicao" id="ref_cod_instituicao" style="width: 308px;">
        <option value="">Selecione uma instituição</option>
        @foreach(\App\Models\LegacyInstitution::active()->get() as $institution)
            <option value="{{$institution->cod_instituicao}}">{{$institution->name}}</option>
        @endforeach
    </select>
</span>

@if(Request::get('ref_cod_instituicao'))
    @push('scripts')
        <script>
            (function($){
                $(document).ready(function() {
                    $j('#ref_cod_instituicao').val({{Request::get('ref_cod_instituicao')}});
                });
            })(jQuery);
        </script>
    @endpush
@endif