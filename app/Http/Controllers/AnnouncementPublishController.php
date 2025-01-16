<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnnouncementRequest;
use App\Models\Announcement;
use App\Process;
use iEducar\Support\Exceptions\Exception;
use Illuminate\Support\Facades\DB;

class AnnouncementPublishController extends Controller
{
    public function index()
    {
        $this->menu(Process::ANNOUNCEMENT);
        $this->breadcrumb('Publicação de avisos', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
        $announcements = Announcement::query()
            ->withTrashed()
            ->with([
                'userTypes',
            ])
            ->latest()
            ->paginate();

        return view('announcement.publish.index', [
            'announcements' => $announcements,
        ]);
    }

    public function update(AnnouncementRequest $request, $announcementId)
    {
        try {
            DB::beginTransaction();
            $announcement = Announcement::query()->withTrashed()->findOrFail($announcementId);
            $announcement->fill($request->all());
            $announcement->save();
            $announcement->userTypes()->sync($request->get('tipo_usuario'));
            $request->get('active') ? $announcement->restore() : $announcement->delete();
            DB::commit();
            session()->flash('success', 'Edição efetuada com sucesso.');
        } catch (Exception) {
            DB::rollBack();
            session()->flash('error', 'Edição não realizada.');
        }

        return redirect()->route('announcement.publish.edit', $announcementId);
    }

    public function store(AnnouncementRequest $request)
    {
        try {
            DB::beginTransaction();
            $announcement = Announcement::create($request->all());
            $announcement->userTypes()->sync($request->get('tipo_usuario'));
            $request->get('active') ? $announcement->restore() : $announcement->delete();
            DB::commit();
            session()->flash('success', 'Cadastro efetuado com sucesso.');
        } catch (Exception) {
            DB::rollBack();
            session()->flash('error', 'Cadastro não realizado.');
        }

        return redirect()->route('announcement.publish.index');
    }

    public function create()
    {
        $this->menu(Process::ANNOUNCEMENT);
        $this->breadcrumb('Publicação de avisos', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        return view('announcement.publish.create', [
            'announcement' => new Announcement,
            'userTypes' => null,
        ]);
    }

    public function edit($announcementId)
    {
        $this->menu(Process::ANNOUNCEMENT);
        $this->breadcrumb('Publicação de avisos', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
        $announcement = Announcement::query()->withTrashed()->findOrFail($announcementId);
        $userTypes = $announcement->userTypes->pluck('cod_tipo_usuario');

        return view('announcement.publish.create', [
            'announcement' => $announcement,
            'userTypes' => $userTypes,
        ]);
    }
}
