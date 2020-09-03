<tr id="tr-calendar">
    <td class="formmdtd" valign="top"><span class="form">Calendários letivos</span></td>
    <td class="formmdtd" valign="top">
        <span class="form">
            <select class="geral" name="stage_type" id="stage_type" style="width: 308px;">
                <option value="">Selecione um tipo</option>
                @foreach($calendars as $calendar)
                    <option value="{{$calendar->start_date}}-{{$calendar->end_date}}">
                        {{(new DateTime($calendar->start_date))->format('d/m/Y')}} - {{(new DateTime($calendar->end_date))->format('d/m/Y')}}
                    </option>
                @endforeach
            </select>
        </span>
    </td>
</tr>

