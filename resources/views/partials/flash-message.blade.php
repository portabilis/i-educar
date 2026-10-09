@php
    $messages = [];

    foreach (['success', 'error', 'notice', 'info', 'legacy'] as $msgType) {
        foreach ((array) Session::get($msgType) as $message) {
            if ($message) {
                $messages[] = [$msgType, $message];
            }
        }
    }

    if (Session::has('errors')) {
        foreach (Session::get('errors')->all() as $message) {
            $messages[] = ['error', $message];
        }
    }
@endphp
<div class="flashMessages">
    <div class="flashMessages__container">
        @foreach ($messages as [$msgType, $message])
            {{-- O erro interrompe o leitor de tela; os demais avisos esperam ele terminar de falar. --}}
            <div class="flashMessages__message -{{ $msgType }}"><button type="button" class="close-msg" aria-label="Fechar aviso">×</button><time aria-hidden="true">{{ date('H:i:s', time()) }}</time><div class="flashMessages__text" role="{{ $msgType === 'error' ? 'alert' : 'status' }}">{!! $message !!}</div></div>
        @endforeach
    </div>
    <ul class="flashMessages__controls">
        <li><a href="#" data-action="showAll">mostrar todos (+<span>0</span>)</a></li><li><a href="#" data-action="closeAll">fechar todos</a></li>
    </ul>
</div>
