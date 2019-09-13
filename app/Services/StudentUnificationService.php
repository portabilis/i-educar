<?php

namespace App\Services;

use App\Exceptions\Unification\InactiveMainUnification;
use App\Exceptions\Unification\UndoInactiveUnification;
use App\Exceptions\Unification\WithoutPermission;
use App\Models\LegacyUserType;
use App\Models\LogUnification;
use App\User;

class StudentUnificationService
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param LogUnification $unification
     * @throws \Exception
     */
    public function undo(LogUnification $unification)
    {
        $this->canUndo($unification);

        $adapter = $unification->getAdapter();

        $adapter->undo($unification);

        $unification->active = false;
        $unification->updated_by = $this->user->cod_usuario;
        $unification->save();
    }

    /**
     * @param LogUnification $unification
     */
    public function canUndo(LogUnification $unification)
    {
        if (!$unification->active) {
            throw new UndoInactiveUnification();
        }

        if (empty($unification->main)) {
            throw new InactiveMainUnification($unification);
        }

        $this->checkPermission($unification);
    }

    /**
     * @param LogUnification $unification
     */
    private function checkPermission($unification)
    {
        $user = $this->user;
        $unificationOwner = $unification->createdBy->user;

        if ($user->type->level > LegacyUserType::LEVEL_INSTITUTIONAL) {
            $this->checkPermissionSchoolingLevel($user, $unificationOwner);
        }

        if ($user->isInstitutional()) {
            $this->checkPermissionInstitutionalLevel($user, $unificationOwner);
        }
    }

    /**
     * Veririca se o usuário do nível Escola tem permissão para desfazer a unificação
     *
     * Usuários de nível escolar podem desfazer unificações que foram efetuadas por
     * usuários da mesma unidade que ele
     *
     * @param User $user
     * @param User $unificationOwner
     */
    private function checkPermissionSchoolingLevel(User $user, User $unificationOwner)
    {
        $schoolsUser = $user->schools->pluck('cod_escola')->all();
        $schoolsUnificationOwner = $unificationOwner->schools->pluck('cod_escola')->all();

        if (count(array_intersect($schoolsUser, $schoolsUnificationOwner)) < 1) {
            throw new WithoutPermission();
        }
    }

    /**
     * Veririca se o usuário do nível Institucional tem permissão para desfazer a unificação
     *
     * Usuários institucionais somente podem desfazer unificações e usuários do mesmo
     * nível ou nível escolar
     *
     * @param User $user
     * @param $unificationOwner
     */
    private function checkPermissionInstitutionalLevel(User $user, $unificationOwner)
    {
        if ($user->type->level == $unificationOwner->type->level) {
            return;
        }

        if ($unificationOwner->type->level == LegacyUserType::LEVEL_SCHOOLING) {
            return;
        }

        throw new WithoutPermission();
    }

}
