<span class="form">
    <select class="geral" name="ref_cod_instituicao" id="ref_cod_instituicao" style="width: 308px;">
        <option value="">Selecione uma instituição</option>
        @foreach(\App\Models\LegacyInstitution::active()->get() as $institution)
            <option value="{{$institution->cod_instituicao}}" @if(old('ref_cod_instituicao', Request::get('ref_cod_instituicao')) == $institution->cod_instituicao) selected @endif>
                {{$institution->name}}
            </option>
        @endforeach
    </select>
</span>
