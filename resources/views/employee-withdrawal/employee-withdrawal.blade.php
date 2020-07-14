<tr id="tr_historico_afastamento">
    <td class="formmdtd" valign="top"><span class="form">Histórico de afastamentos</span></td>
    <td class="formmdtd" valign="top">
        <span class="form">
            <table class="table-detail">
                <tr>
                    <th>Motivo</th>
                    <th>Data de saída</th>
                    <th>Data de retorno</th>
                    <th>Documentos</th>
                </tr>
                @if(isset($withdrawals))
                    @foreach($withdrawals as $withdrawal)
                        <tr>
                            <td> {{$withdrawal->reason->nm_motivo}} </td>
                            <td> {{$withdrawal->data_saida->format('d/m/Y')}} </td>
                            <td> @if($withdrawal->data_retorno) {{$withdrawal->data_retorno->format('d/m/Y')}} @endif </td>
                            <td>
                            @foreach($withdrawal->files as $file)
                                <ul>
                                    <li><a href="{{$file->url}}" target="_new">{{$file->original_name}}</a></li>
                                </ul>
                            @endforeach
                            </td>
                        </div>
                        </tr>
                    @endforeach
                @endif
            </table>
        </span>
    </td>
</tr>

<style>

.table-detail {
  border-collapse: collapse;
  font-size: 14px;
}

.table-detail tr th {
  font-weight: bold;
  font-size: 14px;
  text-align: left;
  line-height: normal;
  padding: 3px 8px;
  background: #ccdce6 !important;
}

.table-detail tr td {
  padding: 3px 8px;
}

.table-detail tr td ul{
  padding: 0;
  margin: 0;
}

.table-detail tr td ul li{
  padding: 0;
  margin: 0;
}

.table-detail tbody tr:nth-child(odd) {
  background-color: #f5f9fd;
}

.table-detail tbody tr:nth-child(even) {
  background-color: #ffffff;
}

</style>