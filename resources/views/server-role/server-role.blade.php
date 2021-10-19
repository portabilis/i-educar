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
                    @foreach($serverfunction as $key => $function)
                        <tr>
                            <td>{{$function->nm_funcao}}</td>
                            <td>{{$function->matricula}}</td>
                            @if($function->professor === 1)
                                <td>{{$function->nm_curso}}</td>
                                <td>
                                    <i class="pointer collapse-btn fa fa-eye" id="{{$key}}"></i>
                                    <div class="name collapse-{{$key}}">{{$function->nome}}</div>
                                </td>
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

    .table-detail tr td ul {
        padding: 0;
        margin: 0;
    }

    .table-detail tr td ul li {
        padding: 0;
        margin: 0;
    }

    .table-detail tbody tr:nth-child(odd) {
        background-color: #f5f9fd;
    }

    .table-detail tbody tr:nth-child(even) {
        background-color: #ffffff;
    }

    .name {
        display: none;
    }

    .pointer {
        cursor: pointer;
    }

</style>

<script>

    const buttons = document.querySelectorAll('.collapse-btn');

    buttons.forEach(button => {
        button.addEventListener('click', event => {

            let collapse_target = event.target.id
            let btn = document.getElementById(event.target.id)
            let element = document.getElementsByClassName(`collapse-${collapse_target}`);

            if (element[0].classList.contains("name")) {
                element[0].classList.remove("name");
                btn.classList.remove("fa-eye");
                btn.classList.add("fa-eye-slash");
            } else {
                element[0].classList.add("name");
                btn.classList.add("fa-eye");
                btn.classList.remove("fa-eye-slash");
            }

        })
    })

</script>
