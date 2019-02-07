<select name="turno_id" id="turno_id" class="geral">
    <option value="">Selecione</option>
    @foreach($turnos as $id => $value)
        <option value="{{$id}}">{{$value}}</option>
    @endforeach
</select>
