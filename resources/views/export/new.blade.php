@extends('layout.default')

@push('styles')
  <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@section('content')
  <form id="formcadastro" action="{{ route('export.export') }}" method="post">
    <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
      <tbody>
        <tr>
          <td class="formdktd" colspan="2" height="24"><b>Exportações</b></td>
        </tr>
        <tr>
          <td class="formmdtd" valign="top"><span class="form">Exportar dados de:</span></td>
          <td class="formmdtd" valign="top">
            <span class="form">
              <select class="geral" name="status" id="status" style="width: 308px;">
                <option value="1">Alunos</option>
              </select>
            </span>
          </td>
        </tr>
        <tr>
          <td class="formlttd" valign="top" colspan="2">
            <span class="form">Selecione os campos que deseja exportar</span>
          </td>
        </tr>
        <tr>
          <td class="formlttd" valign="top" colspan="2">
            <div style="padding-top: 10px">
              <input id="select-all" type="checkbox" />
              <label for="select-all">Selecionar todos</label>
            </div>
          </td>
        </tr>
        <tr>
          <td class="formlttd" valign="top" colspan="2">
            <div style="display: flex; justify-content: space-between; padding-right: 20px">
              @foreach($export->getExportedColumnsByGroup() as $group => $itens)
                <div>
                  <h4>{{ $group }}</h4>
                  @foreach($itens as $key => $label)
                    <div>
                      <input class="fields" type="checkbox" name="fields[]" id="checkbox-{{ $key }}" value="{{ $key }}" />
                      <label for="checkbox-{{ $key }}">{{ $label }}</label>
                    </div>
                  @endforeach
                </div>
              @endforeach
            </div>
            <input type="hidden" name="model" value="{{ get_class($export) }}">
          </td>
        </tr>
      </tbody>
    </table>

    <div class="separator"></div>

    <div style="text-align: center">
      <button class="btn-green" type="submit">Exportar</button>
    </div>

  </form>
@endsection

@push('scripts')
  <script>
  jQuery(document).ready(function () {
    jQuery('#select-all').click(function () {
      jQuery('.fields').prop('checked', this.checked);
    });
    jQuery('.fields').click(function () {
      jQuery('#select-all').prop('checked', false);
    });
  });
  </script>
@endpush
