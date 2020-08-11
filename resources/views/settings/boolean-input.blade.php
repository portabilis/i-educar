<tr>
    <td><span class="form">{{$key}}</span></td>
    <td>
        <input type="hidden" name="{{$id}}" value="0">
        <input name="{{$id}}" type="checkbox" @if($value == 1) checked @endif value="1" />
    </td>
</tr>
