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
          <td class="formlttd" valign="top">
            <span class="form">Ano</span>
          </td>
          <td class="formlttd" valign="top">
            <span class="form">
              @include('form.select-year')
            </span>
          </td>
        </tr>
        <tr>
          <td class="formmdtd" valign="top"><span class="form">Exportar dados de:</span></td>
          <td class="formmdtd" valign="top">
            <span class="form">
              <select class="geral" name="status" id="status" style="width: 308px;">
                @foreach($export->getAllowedExports() as $code => $exportItem)
                <option @if(request('type', 1) == $code) selected @endif value="{{ $code }}">{{ $exportItem->getLabel() }}</option>
                @endforeach
              </select>
            </span>
          </td>
        </tr>
        <tr>
          <td class="formlttd" valign="top">
            <span class="form">Instituição</span>
          </td>
          <td class="formlttd" valign="top">
            <span class="form">
              @include('form.select-institution')
            </span>
          </td>
        </tr>
        <tr>
          <td class="formmdtd" valign="top">
            <span class="form">Escola</span>
          </td>
          <td class="formmdtd" valign="top">
            <span class="form">
              @include('form.select-school')
            </span>
          </td>
        </tr>
        @if(in_array(request('type', 1), [1,3]))
        <tr>
          <td class="formlttd" valign="top">
            <span class="form">Situação da Matrícula</span>
          </td>
          <td class="formlttd" valign="top">
            <span class="form">
              @include('form.select-registration-status')
            </span>
          </td>
        </tr>
        @endif
        <tr>
          <td class="formlttd" valign="top" colspan="2">
            <div style="margin-top: 20px">Selecione os campos que deseja exportar</div>
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
            <div style="display: flex; flex-wrap: wrap; padding-right: 20px">
              @foreach($exportation->getExportedColumnsByGroup() as $group => $itens)
                <div style="min-width: 25%">
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
            <input type="hidden" name="model" value="{{ get_class($exportation) }}">
          </td>
        </tr>
      </tbody>
    </table>

    <div class="separator"></div>

    <div style="width: 500px; margin: 20px auto 10px;">
      <p class="text-muted">Leia e marque o checkbox abaixo:</p>
      <div style="display: flex;">
        <input id="agree" name="agree" value="agree" type="checkbox" />
        <label for="agree" style="font-size: 14px">
          Declaro entender que é minha a responsabilidade de cuidar da integridade das informações e de garantir a
          confidencialidade dos dados e a privacidade dos indivíduos que terão suas informações acessadas.
        </label>
      </div>
    </div>

    <div style="text-align: center; margin-bottom: 10px">
      <button id="export-button" class="btn-green" type="submit" disabled>Exportar</button>
    </div>

    <style>
    #export-button[disabled] {
      opacity: 0.7;
    }
    </style>

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
    jQuery('#status').change(function () {
      window.location.href = '{{ route('export.form') }}?type=' + jQuery('#status').val();
    });
    jQuery('#agree').change(function () {
      jQuery('#export-button').prop('disabled', !jQuery('#agree').prop('checked'));
    });

    if (jQuery('#status').val() == 3) {
      jQuery('.fields').prop('checked', true);
      jQuery('#situacao_matricula').val(3);
    }

      if (jQuery('#status').val() == 4) {
          jQuery('.fields').prop('checked', true);
      }
  });
  </script>

  <script type="text/javascript" src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
  <script type="text/javascript" src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
  <script type="text/javascript" src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Escola.js") }}"></script>
@endpush
