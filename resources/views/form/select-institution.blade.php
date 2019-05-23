<span class="form">
    <select class="geral" name="ref_cod_instituicao" id="ref_cod_instituicao" style="width: 308px;">
        <option value="">Selecione uma instituição</option>
        @foreach(\App\Models\LegacyInstitution::active()->get() as $institution)
            <option value="{{$institution->id}}">{{$institution->name}}</option>
        @endforeach
    </select>
</span>