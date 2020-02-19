<?php

namespace App\Services;

use App\Menu;
use App\Models\LegacyUser;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class NotificationsService
{
    public function createByPermission($permissionId, $text, $link, $typeNotification)
    {
        $menu = Menu::where('process', $permissionId)->first();
        $types = DB::table('pmieducar.menu_tipo_usuario')->select()->where('menu_id', $menu->getKey())->get();

        $users = [];
        foreach ($types as $type) {
            $users = LegacyUser::where('ref_cod_tipo_usuario', $type->ref_cod_tipo_usuario)
                ->where('ativo', 1)
                ->get()
                ->all();
        }

        foreach($users as $user) {
            $this->createByUser($user, $text, $link, $typeNotification);
        }
    }

    private function createByUser($user, $text, $link, $type)
    {
        Notification::create([
            'text' => $text,
            'link' => $link,
            'type_id' => $type,
            'user_id' => $user->getKey(),
        ]);
    }
}
