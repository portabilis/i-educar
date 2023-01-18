<tr id="tr_file">
    <td class="formmdtd" valign="top"><span class="form">Arquivos</span></td>
    <td class="formmdtd" valign="top">
        <span class="form">
            <table class="table-detail">
                <tr>
                    <th>Arquivo</th>
                    <th>Data de adição</th>
                    <th>Link</th>
                </tr>
                @if(isset($files))
                    @foreach($files as $file)
                        <tr>
                            <td> {{$file->original_name}} </td>
                            <td> {{$file->created_at->format('d/m/Y')}} </td>
                            <td>
                                <a class="decorated" target="_blank" rel="noopener" href="{{$file->url}}">Visualizar</a>
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

</style>
