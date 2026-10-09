<tr>
    <td>
        <span class="form"><label for="{{$id}}">@if($description != '') {{$description}} @else {{$key}} @endif</label></span>
        <br>
        <sub style="vertical-align:top;">{{$hint}}</sub>
    </td>
    <td>
        <input type="hidden" name="{{$id}}" value="0">
        <input name="{{$id}}" id="{{$id}}" type="checkbox" @if($value == 1) checked @endif value="1" @if(!$enabled) disabled @endif />
    </td>
</tr>
