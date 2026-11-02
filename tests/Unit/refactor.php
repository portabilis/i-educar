protected function validateNationalIdentifiers(): bool
{
    $state = $this->cpfState();
    $inepId = trim((string) $this->getRequest()->aluno_inep_id);
    $possuiCpf = $state !== null && $state['possui_cpf'];

    // Regra 1: Pelo menos um identificador único federal deve estar presente
    if (!$possuiCpf && empty($inepId)) {
        $this->messenger->append('É necessário informar o Código ID INEP caso o aluno não possua CPF.');
        return false;
    }

    return $this->validateCpfState() 
        && $this->validateCpfCode() 
        && $this->validateCpfUniqueness() 
        && $this->validateInepCode() 
        && $this->validatesUniquenessOfAlunoInepId();
}