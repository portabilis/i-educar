# Callback — Exclusão em lote de componentes

## Endpoint (API legada do i-Educar)

```
POST {ieducar_url}/module/Api/Diario
```

Mesma API que o iDiário já usa para lançar notas, faltas, etc. Autenticação padrão (access key + assinatura).

## Fluxo

1. O i-Educar chama `POST /api/v2/discipline_records/destroy_batch` passando um campo extra `operation_id`
2. O iDiário deve retornar imediatamente `{ "queued": true }` (status 200)
3. O iDiário processa a exclusão na fila
4. Ao terminar, o iDiário faz `POST` no endpoint do i-Educar com o resultado

## Payload enviado pelo i-Educar (destroy_batch)

```json
{
  "year": 2025,
  "unities": [1, 2],
  "courses": [],
  "grades": [10, 20],
  "disciplines": [100, 200],
  "user": 1,
  "operation_id": 42
}
```

Se `operation_id` estiver presente, o iDiário deve processar na fila e chamar o endpoint ao final.
Se `operation_id` não estiver presente (ou for null), manter o comportamento atual (síncrono).

## Resposta imediata esperada do iDiário

```json
{
  "queued": true
}
```

## Callback do iDiário para o i-Educar

Quando a fila terminar de processar, o iDiário deve montar a URL usando o domínio do i-Educar que já possui configurado:

```
POST {ieducar_url}/module/Api/Diario?oper=post&resource=component-batch-callback&operation_id={operation_id}

Parâmetros (no body, junto com access key/assinatura):
  success: true
  deleted: 150
```

Em caso de erro:

```
  success: false
  error: "Descrição do erro"
```

### Campos do body

| Campo     | Tipo    | Descrição                                      |
|-----------|---------|-------------------------------------------------|
| `success` | boolean | `true` se a exclusão foi concluída com sucesso  |
| `deleted` | integer | Quantidade de registros excluídos (quando sucesso) |
| `error`   | string  | Mensagem de erro (quando falha)                 |

### Parâmetros da query string

| Campo          | Tipo    | Descrição                                      |
|----------------|---------|-------------------------------------------------|
| `oper`         | string  | Sempre `post`                                  |
| `resource`     | string  | Sempre `component-batch-callback`              |
| `operation_id` | integer | ID da operação recebido no payload original    |

## Observações

- O iDiário já possui o domínio do i-Educar configurado — basta montar a URL com o `operation_id` recebido
- A autenticação é a mesma que o iDiário já usa nas outras chamadas ao i-Educar (access key + assinatura)
- O i-Educar ignora callbacks para operações que já foram concluídas ou falharam
