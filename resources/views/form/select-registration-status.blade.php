<span class="form">
    <select class="geral" name="situacao_matricula" id="situacao_matricula" style="width: 308px;">
        @foreach(\iEducar\Modules\Enrollments\Model\EnrollmentStatusFilter::getDescriptiveValues() as $value => $label)
            <option value="{{ $value }}" @if($value === 10) selected @endif>
                {{ $label }}
            </option>
        @endforeach
    </select>
</span>
