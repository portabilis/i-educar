<tr>
    <td>
        <span class="form"> @if($description != '') {{$description}} @else {{$key}} @endif </span>
        <br>
        <sub style="vertical-align:top;">{{$hint}}</sub>
    </td>
    <td>
        <input name="{{$id}}" type="text" value="{{$value}}" size="40" @if(!$enabled) disabled @endif />
    </td>
</tr>
