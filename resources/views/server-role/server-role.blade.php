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
                                    <div class="pointer modal-btn" id="{{$key}}">Mostrar detalhe</div>
                                    <div class="name modal-{{$key}}">{{$function->nome}}</div>
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

    .modal-server-container.active {
        position: absolute;
        z-index: 2;
        height: 100vh;
        width: 100vw;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-server-container.inactive {
        display: none;
    }

    .modal-btn {
        color: #188ad1;
    }

    .modal-btn i {
        color: #188ad1;
    }

    .modal-server-header h3 {
        margin: 0;
        font-size: 1em;
        color: #47728f;
    }

    .modal-server-header {
        height: 60px;
        background-color: #CCDCE6;
        border-radius: 10px 10px 0px 0px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 3% 0 3%;
    }

    .modal-server-wrapper {
        min-height: 50vh;
        min-width: 60vw;
        width: 60vw;
        background-color: #FFFFFF;
        border-radius: 10px;

    }

    .modal-server-body {
        max-height: 50vh;
        padding: 3%;
        font-size: 1em;
        color: #47728f;
        overflow: auto;
    }
</style>

<script>

    const modal_element = '<div class="modal-server-container inactive">' +
        '<div class="modal-server-wrapper">' +
        '<div class="modal-server-header">' +
        '<h3>Componentes Curriculares</h3>' +
        '<i class="fa fa-close modal-server-close"></i>' +
        '</div>' +
        '<div class="modal-server-body"></div>' +
        '</div>' +
        '</div>';

    const buttons = document.querySelectorAll('.modal-btn');

    let modal_data = '';

    document.addEventListener("DOMContentLoaded", function (e) {
        document.body.insertAdjacentHTML('afterbegin', modal_element);
        modalListener()
    })

    function modalListener() {
        let modal = document.getElementsByClassName('modal-server-container');
        let close_actions = document.querySelectorAll('.modal-server-close, .modal-server-container');
        close_actions.forEach(function (i) {
            i.addEventListener('click', event => {
                modal[0].classList.remove("active");
                modal[0].classList.add("inactive");
            })
        })
    }

    buttons.forEach(button => {
        button.addEventListener('click', event => {
            let modal_target = event.target.id;
            let element = document.getElementsByClassName(`modal-${modal_target}`);
            let modal = document.getElementsByClassName('modal-server-container');
            let modal_body = document.querySelector('.modal-server-body');

            modal_body.innerHTML = '';
            modal_data = element[0].textContent;

            if (modal[0].classList.contains("active")) {
                modal[0].classList.remove("active");
                modal[0].classList.add("inactive");
            } else {
                modal[0].classList.remove("inactive");
                modal[0].classList.add("active");
            }

            if (modal_data.includes(';')) {
                let split_data = modal_data.split(';');
                split_data.each(i => {
                    modal_body.innerHTML += `<li>${i}</li>`;
                })
            } else {
                modal_body.innerHTML += `<li>${modal_data}</li>`;
            }
        })
    })

</script>
