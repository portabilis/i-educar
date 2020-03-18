@extends('layout.default')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}"/>
@endpush

@section('content')
    <form id="formcadastro" action="" method="get">
        <table class="tablecadastro" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tbody>
            <tr>
                <td class="formdktd" colspan="2" height="24"><b>Notificações</b></td>
            </tr>
            <tr id="tr_nm_instituicao">
                <td class="formmdtd" valign="top"><span class="form">Notificações:</span></td>
                <td class="formmdtd" valign="top">
                    <span class="form">
                        <select class="geral" name="status" id="status" style="width: 308px;">
                            <option value="0" @if(Request::get('status') == null) selected @endif>Todas</option>
                            <option value="1" @if(Request::get('status') == iEducar\Modules\Notifications\Status::READ) selected @endif>Lidas</option>
                            <option value="2" @if(Request::get('status') == iEducar\Modules\Notifications\Status::UNREAD) selected @endif>Não lidas</option>
                        </select>
                    </span>
                </td>
            </tr>
            <tr id="tr_nm_instituicao">
                <td class="formlttd" valign="top"><span class="form">Tipo:</span></td>
                <td class="formlttd" valign="top">
                    <span class="form">
                        <select class="geral" name="type" id="type" style="width: 308px;">
                            <option value="">Selecione</option>
                            @foreach(\App\Models\NotificationType::all() as $type)
                                <option value="{{$type->id}}" @if(Request::get('type') == $type->id) selected @endif>{{$type->name}}</option>
                            @endforeach
                        </select>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="formmdtd" valign="top"><span>Texto:</span></td>
                <td class="formmdtd" valign="top">
                <input type="text" name="text" id="text" style="width: 400px;" value="{{Request::get('text')}}" />
            </tr>
            </tbody>
        </table>

        <div class="separator"></div>

        <div style="text-align: center">
            <button class="btn-green" type="submit">Buscar</button>
        </div>

    </form>

    <table class="table-default">
        <thead>
        <tr>
            <th>Texto</th>
            <th>Status</th>
            <th>Data</th>
        </tr>
        </thead>
        <tbody>
        @forelse($notifications as $notification)
            <tr @if(!$notification->read_at) class="unread" @endif>
                <td><a onclick="markAsRead(this, true)" href="{{$notification->link}}" data-id="{{$notification->id}}" target="_blank">{{$notification->text}}</a></td>
                <td><a onclick="markAsRead(this, true)" href="{{$notification->link}}" data-id="{{$notification->id}}" target="_blank" class="text-status"> @if($notification->read_at) Lida @else Não lida @endif </a></td>
                <td>{{$notification->created_at->format('d/m/Y H:i')}}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3">Não existe nenhuma notificação</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="separator"></div>

    <div style="text-align: center">
        {{ $notifications->links() }}
    </div>
@endsection

@prepend('scripts')
    <script>
        if ($j('#nova_situacao').val() != '4') {
            $j('.field-transfer').hide();
        }

        $j('#nova_situacao').on('change', function () {
            if ($j(this).val() == '4') {
                $j('.field-transfer').show();
            } else {
                $j('.field-transfer').hide();
            }
        })
    </script>

    <script type="text/javascript"
            src="{{ Asset::get("/modules/Portabilis/Assets/Javascripts/ClientApi.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/DynamicInput.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Escola.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Curso.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Serie.js") }}"></script>
    <script type="text/javascript"
            src="{{ Asset::get("/modules/DynamicInput/Assets/Javascripts/Turma.js") }}"></script>
@endprepend
