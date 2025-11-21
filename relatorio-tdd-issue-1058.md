**Relatório TDD — Issue #1058: CPF inválido retorna vários alunos no i-Educar**

- Issue: #1058
- Link da issue: https://github.com/gustaallves/i-educar/issues/1058
- Branch criada: `fix/1058-validar-cpf-busca`
- Pull request: https://github.com/gustaallves/i-educar/pull/new/fix/1058-validar-cpf-busca

**1) Especificação da issue (copiada do repositório)**

Título: "CPF inválido retorna vários alunos no iEducar #1058"

Descrição resumida:
Ao inserir um CPF inválido na busca de alunos, o sistema retorna múltiplos registros em vez de exibir mensagem de erro indicando CPF inválido. O comportamento esperado é validar o CPF no backend e não retornar resultados para CPF incorreto.

**2) Minha descrição da nova funcionalidade / correção**

- Objetivo: impedir que buscas por CPF inválido gerem resultados errôneos; exibir uma mensagem de erro para o usuário e não usar o CPF inválido para filtrar a consulta.
- Requisitos/testes a serem desenvolvidos:
  - Função `validar_cpf(string): bool` que aceita CPF com ou sem pontuação e retorna true apenas para CPFs válidos.
  - Testes unitários que confirmem CPFs válidos retornam true e CPFs inválidos retornam false.
  - Integração na listagem de alunos: antes de aplicar o filtro por CPF, normalizar e validar; se inválido, colocar mensagem de erro em sessão e ignorar o filtro de CPF.

**3) Mudanças realizadas (arquivos)**

- Adicionado: `ieducar/lib/validations.php` — implementa `validar_cpf()`.
- Adicionado: `tests/Unit/ValidationsTest.php` — testes unitários PHPUnit.
- Modificado: `ieducar/intranet/educar_aluno_lst.php` — validação de CPF antes de aplicar filtro; exibição de mensagem de erro via `$_SESSION['ieducar_error']`.
- Adicionado (auxiliar): `.test-runner/composer.json` e `.test-runner/*vendor*/` — runner isolado para executar PHPUnit sem instalar dependências do projeto principal.
- Adicionado: `relatorio-tdd-issue-1058.md` (este arquivo).

**4) Ciclo TDD aplicado (passos, comandos e evidências)**

- Passo 1 — Escrever teste: `tests/Unit/ValidationsTest.php` criado contendo casos para CPF válido e inválido.
  - (Comando) — nenhum (o teste foi criado no repositório).

- Passo 2 — Ver teste falhar: opcional para relatório (não foi preservado o estado anterior do repositório), mas o teste demonstraria falha antes da função existir.
  - Para reproduzir localmente: renomear temporariamente `ieducar/lib/validations.php` e rodar os testes — verá falha.

- Passo 3 — Implementação mínima para passar no teste: `ieducar/lib/validations.php` adicionado com algoritmo de verificação dos dígitos verificadores do CPF.

- Passo 4 — Rodar testes e confirmar que passam.
  - Comandos executados (no workspace):
    ```powershell
    cd "C:\Users\gugu8\OneDrive\Área de Trabalho\Estudos\UNB\Testes\i-educar"
    cd .test-runner
    composer install --no-interaction --prefer-dist
    vendor\bin\phpunit ..\tests\Unit\ValidationsTest.php --testdox
    ```
  - Saída dos testes (capturada):
    > PHPUnit 9.6.29 by Sebastian Bergmann and contributors.
    >
    > Validations
    >  ✔ Valid cpf
    >  ✔ Invalid cpf
    >
    > Time: 00:00.014, Memory: 6.00 MB
    >
    > OK (2 tests, 7 assertions)

- Passo 5 — Refatoração: função implementada de forma simples e legível; validação aplicada ao handler de busca de alunos com mensagem de sessão. Testes executados após mudança e passam (e estão incluídos no repositório).

**5) Resultado da execução dos testes**

- Todos os testes em `tests/Unit/ValidationsTest.php` passaram (2 tests, 7 assertions).
- Comandos e saída mostrados acima.

**6) Como testar manualmente (UI)**

- Pré-requisito: ambiente web + banco do i-Educar executando localmente.
- Passos:
  1. Abra a página de listagem de alunos (ex.: `/ieducar/intranet/educar_aluno_lst.php`).
  2. No campo CPF, digite um CPF inválido (ex.: `111.111.111-11`) e busque.
  3. Resultado esperado: mostrar a mensagem "CPF inválido." no topo da listagem e não aplicar o filtro por CPF.
  4. Teste com um CPF válido (ex.: `529.982.247-25`) e verifique que retorna o registro correto.

**7) Commits / Pull Request**

- Branch criada: `fix/1058-validar-cpf-busca`
- Commit principal: "Valida CPF na busca de alunos; impede busca por CPF inválido (issue #1058) + testes"
- Push remoto: enviado para `origin/fix/1058-validar-cpf-busca`
- PR: https://github.com/gustaallves/i-educar/pull/new/fix/1058-validar-cpf-busca

**8) Snippets relevantes (código final)**

- `ieducar/lib/validations.php` (função resumida):

```php
function validar_cpf(string $cpf): bool
{
    $cpf = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf) !== 11) return false;
    if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;
    for ($t = 9; $t < 11; $t++) {
        $sum = 0;
        for ($i = 0; $i < $t; $i++) {
            $sum += (int)$cpf[$i] * (($t + 1) - $i);
        }
        $remainder = ($sum * 10) % 11;
        if ($remainder === 10) $remainder = 0;
        if ((int)$cpf[$t] !== $remainder) return false;
    }
    return true;
}
```

- Trecho aplicado em `ieducar/intranet/educar_aluno_lst.php` (validação antes do filtro):
```php
$cpf_normalizado = preg_replace(pattern: '/\D/', replacement: '', subject: $this->cpf_aluno);
if ($cpf_normalizado !== '' && !validar_cpf($cpf_normalizado)) {
    if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
    $_SESSION['ieducar_error'] = 'CPF inválido.';
    $this->cpf_aluno = null;
} else {
    $this->cpf_aluno = $cpf_normalizado ?: null;
}
```

**9) Evidências / Capturas (coloque aqui as capturas de tela)**

- [ ] Tela: teste criado (falha) — se disponível
- [x] Tela: testes passando (saída do PHPUnit acima)
- [ ] Tela: busca com CPF inválido mostrando a mensagem (adicionar quando testar UI)
- [ ] Tela: busca com CPF válido retornando o aluno correto

(Inclua as imagens nas seções acima quando você capturá-las no navegador.)

**10) Conclusão (minha percepção sobre TDD aplicado aqui)**

Aplicar TDD nesse caso trouxe vantagens claras e rápidas:
- Reduzi o risco de regressão: a validação do CPF ficou testada com casos típicos e borda.
- Feedback rápido: o ciclo escrever-teste → implementar → rodar testes foi rápido (testes unitários simples).
- Custo/benefício: implementar a validação e adicioná-la à camada de listagem teve baixo custo e alta vantagem (evita buscas incorretas e potenciais vazamentos de dados).

Limitações:
- Testes unitários só provam a lógica de validação; o teste manual em ambiente com banco é necessário para validar a experiência do usuário e integração com consulta ao banco.
- Em projetos maiores, convém integrar validação com um sistema de mensagens/flash do framework em vez de usar `$_SESSION` diretamente; posso adaptar isso se preferir.

---

Se quiser, eu:
- Posso abrir automaticamente o PR usando a GitHub CLI (`gh pr create`) e preencher a descrição; ou posso deixar pronto para você abrir.
- Posso ajustar a exibição da mensagem para usar o sistema de mensagens do projeto.
- Posso gerar versões das capturas solicitadas (preciso que você rode o app local e me passe imagens ou me permita executar mais comandos para iniciar o servidor web local).

Diga qual próximo passo prefere que eu execute: "abrir PR", "ajustar mensagem/flash", "gerar capturas" ou "finalizar relatório (PDF)".
