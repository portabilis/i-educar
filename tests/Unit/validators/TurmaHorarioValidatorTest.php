<?php

namespace Tests\Unit\Validators;

use App\Validators\TurmaHorarioValidator;
use Tests\TestCase;

class TurmaHorarioValidatorTest extends TestCase
{
    private TurmaHorarioValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new TurmaHorarioValidator;
    }

    public function test_aceita_horarios_validos_sem_intervalo(): void
    {
        $isValid = $this->validator->isValid(horaInicial: '07:30', horaFinal: '11:45');

        $this->assertTrue($isValid);
        $this->assertEmpty($this->validator->getMessages());
    }

    public function test_aceita_horarios_validos_com_intervalo(): void
    {
        $isValid = $this->validator->isValid(
            horaInicial: '07:30',
            horaFinal: '11:45',
            horaInicioIntervalo: '09:30',
            horaFimIntervalo: '09:50'
        );

        $this->assertTrue($isValid);
        $this->assertEmpty($this->validator->getMessages());
    }

    public function test_aceita_valores_nulos_para_horarios_opcionais(): void
    {
        $isValid = $this->validator->isValid(
            horaInicial: null,
            horaFinal: null,
            horaInicioIntervalo: null,
            horaFimIntervalo: null
        );

        $this->assertTrue($isValid);
        $this->assertEmpty($this->validator->getMessages());
    }

    public function test_rejeita_formato_invalido_de_horario(): void
    {
        $isValid = $this->validator->isValid(horaInicial: '25:00', horaFinal: '12:00');

        $this->assertFalse($isValid);
        $this->assertContains(
            'O formato de horário informado é inválido. Utilize HH:MM.',
            $this->validator->getMessages()
        );
    }

    public function test_rejeita_hora_inicial_maior_que_hora_final(): void
    {
        $isValid = $this->validator->isValid(horaInicial: '13:00', horaFinal: '08:00');

        $this->assertFalse($isValid);
        $this->assertContains(
            'A hora inicial deve ser anterior à hora final da turma.',
            $this->validator->getMessages()
        );
    }

    public function test_rejeita_hora_inicial_igual_a_hora_final(): void
    {
        $isValid = $this->validator->isValid(horaInicial: '08:00', horaFinal: '08:00');

        $this->assertFalse($isValid);
        $this->assertContains(
            'A hora inicial deve ser anterior à hora final da turma.',
            $this->validator->getMessages()
        );
    }

    public function test_rejeita_intervalo_com_apenas_um_dos_horarios_preenchido(): void
    {
        $isValid = $this->validator->isValid(
            horaInicial: '07:30',
            horaFinal: '11:45',
            horaInicioIntervalo: '09:30',
            horaFimIntervalo: null
        );

        $this->assertFalse($isValid);
        $this->assertContains(
            'Ambos os horários de início e término do intervalo devem ser informados.',
            $this->validator->getMessages()
        );
    }

    public function test_rejeita_intervalo_com_inicio_maior_ou_igual_ao_fim(): void
    {
        $isValid = $this->validator->isValid(
            horaInicial: '07:30',
            horaFinal: '11:45',
            horaInicioIntervalo: '10:00',
            horaFimIntervalo: '09:30'
        );

        $this->assertFalse($isValid);
        $this->assertContains(
            'A hora de início do intervalo deve ser anterior à hora de término do intervalo.',
            $this->validator->getMessages()
        );
    }

    public function test_rejeita_intervalo_iniciando_antes_da_aula(): void
    {
        $isValid = $this->validator->isValid(
            horaInicial: '07:30',
            horaFinal: '11:45',
            horaInicioIntervalo: '07:00',
            horaFimIntervalo: '07:20'
        );

        $this->assertFalse($isValid);
        $this->assertContains(
            'O intervalo deve estar compreendido entre a hora inicial e a hora final da turma.',
            $this->validator->getMessages()
        );
    }

    public function test_rejeita_intervalo_terminando_apos_o_fim_da_aula(): void
    {
        $isValid = $this->validator->isValid(
            horaInicial: '07:30',
            horaFinal: '11:45',
            horaInicioIntervalo: '11:30',
            horaFimIntervalo: '12:00'
        );

        $this->assertFalse($isValid);
        $this->assertContains(
            'O intervalo deve estar compreendido entre a hora inicial e a hora final da turma.',
            $this->validator->getMessages()
        );
    }

    public function test_rejeita_apenas_um_horario_de_aula_informado(): void
    {
        $isValid = $this->validator->isValid(horaInicial: '07:30', horaFinal: null);

        $this->assertFalse($isValid);
        $this->assertContains(
            'A hora inicial e a hora final da turma devem ser informadas juntas.',
            $this->validator->getMessages()
        );
    }
}
