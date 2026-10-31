<div id="pending-transfers" style="display:none"
     data-url="{{ route('transfers.pending.index') }}"
     data-channel="{{ \Illuminate\Support\Facades\DB::getDefaultConnection() . '-notification-' . md5(auth()->id()) }}"
     data-auto-open="{{ request()->is('web', 'web/*', 'intranet/educar_index.php', 'intranet/index.php') ? '1' : '0' }}">
    <p>Existem alunos aguardando recebimento por transferência interna. Confirme o recebimento para iniciar a matrícula e a enturmação.</p>
    <p>A transferência permanece pendente até a criação da matrícula de entrada.</p>
    <p id="pending-transfers-summary" role="status"></p>
    <div id="pending-transfers-list"></div>
    <nav aria-label="Páginas de transferências pendentes">
        <button type="button" id="pending-transfers-previous" hidden>Anterior</button>
        <button type="button" id="pending-transfers-next" hidden>Próxima</button>
    </nav>
    <p id="pending-transfers-error" role="alert"></p>
</div>
<style>
    #pending-transfers-list article { border-top: 1px solid #cddce6; padding: 12px 0; }
    #pending-transfers-list dl { margin: 8px 0; }
    #pending-transfers-list dt { font-weight: bold; }
    #pending-transfers-list dd { margin: 0 0 6px; }
    #pending-transfers-list .pending-transfer-confirm { display: inline-block; padding: 10px 16px; margin: 8px 0; border-radius: 3px; font: inherit; cursor: pointer; }
    #pending-transfers-list .pending-transfer-confirm:hover { filter: brightness(.92); }
    #pending-transfers-list .pending-transfer-confirm:focus { outline: 2px solid #47728f; outline-offset: 2px; }
    #pending-transfers-list .pending-transfer-confirm:disabled { cursor: wait; opacity: .65; }
    #pending-transfers-error { color: #a00; }
    .pending-transfer-count { background: #b91c1c; color: #fff; border: 0; border-radius: 12px; cursor: pointer; padding: 2px 7px; }
    .pending-transfer-pulse { animation: pending-transfer-pulse 1s ease-in-out infinite; }
    @keyframes pending-transfer-pulse { 50% { opacity: .35; } }
    @media (prefers-reduced-motion: reduce) { .pending-transfer-pulse { animation: none; } }
</style>
@php
    $pendingTransfersScript = Asset::get('/vendor/legacy/Cadastro/Assets/Javascripts/TransferenciasPendentes.js');
    $pendingTransfersScript .= (str_contains($pendingTransfersScript, '?') ? '&' : '?') . 'revision=' . filemtime(public_path('vendor/legacy/Cadastro/Assets/Javascripts/TransferenciasPendentes.js'));
@endphp
<script src="{{ $pendingTransfersScript }}"></script>
