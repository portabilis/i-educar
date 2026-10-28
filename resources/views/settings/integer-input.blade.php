<tr>
    <td>
        <span class="form"><label for="{{$id}}">@if($description != '') {{$description}} @else {{$key}} @endif</label></span>
        <br>
        <sub style="vertical-align:top;">{{$hint}}</sub>
    </td>
    <td>
        <input name="{{$id}}" id="{{$id}}" type="number" value="{{$value}}" @if(!$enabled) disabled @endif />
    </td>
</tr>
