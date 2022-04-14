<span class="form">
    <select class="geral" name="stage_type" id="stage_type" style="width: 308px;">
        <option value="">Selecione um tipo</option>
            @foreach(\App\Models\LegacyStageType::active()->get() as $stageType)
            <option value="{{$stageType->getKey()}}">{{$stageType->name}}</option>
            @endforeach
    </select>
</span>

@if(old('stage_type', Request::get('stage_type')))
    @push('scripts')
        <script>
            (function ($) {
                $(document).ready(function () {
                    $j('#stage_type').val({{old('stage_type', Request::get('stage_type'))}})
                    $('#stage_type').trigger('change');
                });
            })(jQuery);
        </script>
    @endpush
@endif
