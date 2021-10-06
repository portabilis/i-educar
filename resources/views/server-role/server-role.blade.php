<tr id="tr_funcao_servidor">
    <td class="formmdtd" valign="top"><span class="form">Função:</span></td>
    <td class="formmdtd" valign="top">
        <span class="form">
            <table class="table-detail">
                <tr>
                    <th style="width: 200px">Função</th>
                    <th>Matrícula</th>
                    <th style="width: 300px">Cursos ministrados</th>
                    <th style="width: 300px">Componentes curriculares</th>
                </tr>
                @if(isset($serverfunction))
                    @foreach($serverfunction as $function)
                        <tr>
                            <td>{{$function->nm_funcao}}</td>
                            <td>{{$function->matricula}}</td>
                            @if($function->professor === 1)
                                <td>{{$function->nm_curso}}</td>
                                <td>{{$function->nome}}</td>
                            @else
                                <td colspan="2"></td>
                            @endif
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
