<?php

namespace Tests\Unit\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\SchoolManagerAccessCriteria;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;
use iEducar\Modules\Educacenso\Validator\SchoolManagers;
use Tests\TestCase;

class SchoolManagerTest extends TestCase
{
    public function testWithoutOrEmptyManagerShouldBeInvalid()
    {
        $validator = new SchoolManagers([], [1, 2], [1, 2], ['1', '2'], [1, 2], [true, false], 1);
        $this->assertFalse($validator->isValid());

        $validator = new SchoolManagers([1, null], [1, 2], [1, 2], ['1', '2'], [1, 2], [true, false], 1);
        $this->assertFalse($validator->isValid());
    }

    public function testWithoutOrEmptyRoleShouldBeInvalid()
    {
        $validator = new SchoolManagers([1, 2], [], [1, 2], ['1', '2'], [1, 2], [true, false], 1);
        $this->assertFalse($validator->isValid());

        $validator = new SchoolManagers([1, 2], [null, 1], [1, 2], ['1', '2'], [1, 2], [true, false], 1);
        $this->assertFalse($validator->isValid());
    }

    public function testRoleIsDirectorAndAccesCriteriaIsEmptyShouldBeInvalid()
    {
        $validator = new SchoolManagers([1], [SchoolManagerRole::DIRETOR], [], ['1'], [1], [true], 1);
        $this->assertFalse($validator->isValid());

        $validator = new SchoolManagers([1], [SchoolManagerRole::DIRETOR], [1], ['1'], [1], [true], 1);
        $this->assertTrue($validator->isValid());
    }

    public function testAccessCriteriaIsOtherAndDescriptionIsEmptyShouldBeInvalid()
    {
        $validator = new SchoolManagers([1], [1], [SchoolManagerAccessCriteria::OUTRO], [], [1], [true], 1);
        $this->assertFalse($validator->isValid());

        $validator = new SchoolManagers([1], [1], [SchoolManagerAccessCriteria::OUTRO], ['1'], [1], [true], 1);
        $this->assertTrue($validator->isValid());
    }

    public function testRoleIsDirectorAndAccessTypeIsEmptyShouldBeInvalid()
    {
        $validator = new SchoolManagers([1], [SchoolManagerRole::DIRETOR], [1], ['1'], [], [true], DependenciaAdministrativaEscola::FEDERAL);
        $this->assertFalse($validator->isValid());

        $validator = new SchoolManagers([1], [SchoolManagerRole::DIRETOR], [1], ['1'], [1], [true], DependenciaAdministrativaEscola::FEDERAL);
        $this->assertTrue($validator->isValid());

        $validator = new SchoolManagers([1], [SchoolManagerRole::DIRETOR], [1], ['1'], [], [true], DependenciaAdministrativaEscola::PRIVADA);
        $this->assertTrue($validator->isValid());
    }
}
