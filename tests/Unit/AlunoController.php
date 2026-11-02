// Adicionar chamada da nova regra no método commonValidate()
protected function commonValidate(): bool
{
    return $this->validateDeficiencies() &&
        $this->validateNis() &&
        $this->validateInepExam() &&
        $this->validateTechnologicalResources() &&
        $this->validateCpfState() &&
        $this->validateCpfCode() &&
        $this->validateCpfUniqueness() &&
        $this->validateBirthCertificate() &&
        $this->validateInepCode() &&
        $this->validateInepRequiredWhenNoCpf(); // <--- Nova validação TDD
}

/**
 * Valida obrigatoriedade de ID INEP caso o aluno não possua CPF
 */
protected function validateInepRequiredWhenNoCpf(): bool
{
    $state = $this->cpfState();

    // Se o estado do CPF for falso (aluno não possui CPF)
    if ($state !== null && !$state['possui_cpf']) {
        $inepId = $this->getRequest()->aluno_inep_id;

        if (empty($inepId)) {
            $this->messenger->append('O Código ID INEP é obrigatório quando o aluno não possui CPF.');
            return false;
        }
    }

    return true;
}