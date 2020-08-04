<tr>
    <td><span class="form">@if($description != '') {{$description}} @else {{$key}} @endif</span></td>
    <td>
        <input name="{{$id}}" type="number" step="0.01" value="{{$value}}" @if(!$enabled) disabled @endif/>
    </td>
</tr>
