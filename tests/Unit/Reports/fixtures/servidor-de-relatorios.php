<?php

// Servidor de renderização de relatórios usado nos testes: responde conforme o
// nome do relatório e devolve o que recebeu, para o teste conferir o envio.

$request = json_decode(file_get_contents('php://input'), true) ?? [];

header('Content-Type: application/json');

echo match ($request['report'] ?? null) {
    'sucesso' => json_encode(['success' => true, 'data' => ['file' => base64_encode(json_encode($request + ['authorization' => $_SERVER['HTTP_AUTHORIZATION'] ?? null]))]]),
    'falha' => json_encode(['success' => false, 'error' => 'Relatório indisponível']),
    'falha-sem-erro' => json_encode(['success' => false, 'message' => 'Sem detalhes']),
    default => 'isto não é json',
};
