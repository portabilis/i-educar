<tr>
    <td><span class="form"> @if($description != '') {{$description}} @else {{$key}} @endif </span></td>
    <td>
        <input name="{{$id}}" type="text" value="{{$value}}" size="40"/>
    </td>
</tr>
