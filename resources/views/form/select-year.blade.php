<span class="form">
    <input type="text" class="geral {{ isset($obrigatorio) && $obrigatorio ? 'obrigatorio' : '' }}" name="ano" id="ano" maxlength="4" value="{{old('ano', Request::get('ano', date('Y')))}}" size="4">
</span>
