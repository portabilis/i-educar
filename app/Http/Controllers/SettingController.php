<?php

namespace App\Http\Controllers;

use App\Process;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $this->breadcrumb('Configurações de sistema', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->menu(Process::SETTINGS);

        if(!$request->user()->isAdmin()) {
            return redirect('/intranet/educar_configuracoes_index.php');
        }

        $fields = $this->getSettingsFields();
        return view('settings.index', ['fields' => $fields]);
    }

    private function getSettingsFields()
    {
        return Setting::query()
        ->OrderBy('key')
        ->get();
    }

    public function saveInputs(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            Setting::where('id', $key)->update(['value' => $value]);
        }

        return redirect()->route('settings.index')->with('success', 'Configurações de sistema salvas com sucesso.');
    }
}
