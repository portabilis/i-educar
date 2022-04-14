@php

if (empty($records)) {
    $records = [10, 20, 30, 40, 50, 60];
}

@endphp

<select name="record">
    <option value="">Selecione um registro</option>
    @foreach($records as $record)
        <option value="{{ $record }}" @if(old('record', request('record')) == $record) selected @endif>
            Registro {{ $record }}
        </option>
    @endforeach
</select>
