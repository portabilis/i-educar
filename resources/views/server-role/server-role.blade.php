@inject('service', 'App\Services\UrlPresigner')
<tr id="tr_funcao_servidor">
    <td class="formmdtd" valign="top"><span class="form">Função:</span></td>
    <td class="formmdtd" valign="top">
        <span class="form">
            <table class="table-detail">
                <tr>
                    <th>Função</th>
                    <th>Matrícula</th>
                    <th>Cursos ministrados</th>
                    <th>Componentes curriculares</th>
                </tr>
                @if(isset($serverrole))
                    @foreach($serverrole as $role)
                        <tr>
                            <td>{{$role->nm_funcao}}</td>
                            <td>{{$role->matricula}}</td>
                            <td>{{$role->nm_curso}}</td>
                            <td>{{$role->nome}}</td>
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
