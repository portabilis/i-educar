<select name="school">
    <option value="">Selecione uma escola</option>
    @foreach($schools as $school)
        <option value="{{ $school->getKey() }}" @if(old('school', request('school')) == $school->getKey()) selected @endif>
            {{ $school->name }}
        </option>
    @endforeach
</select>
