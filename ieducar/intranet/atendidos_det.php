<?php

use App\Models\Employee;
use App\Models\LegacyIndividual;
use App\Models\LegacyIndividualPicture;
use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use App\Models\LegacyRace;
use App\Models\LegacyStudent;
use App\Models\PersonHasPlace;
use App\Services\FileService;
use App\Services\UrlPresigner;

return new class extends clsDetalhe
{
    public function Gerar()
    {
        $this->titulo = 'Detalhe da Pessoa';

        $cod_pessoa = (int) $this->getQueryString(name: 'cod_pessoa');

        $pessoa = LegacyPerson::with('individual')->find($cod_pessoa);

        if (!$pessoa) {
            $this->addDetalhe(detalhe: ['Erro', 'Pessoa não encontrada']);

            return;
        }

        $fisica = $pessoa->individual;
        $endereco = PersonHasPlace::with('place.city.state')->where('person_id', $cod_pessoa)->first()?->place;
        $telefones = LegacyPhone::query()->where('idpes', $cod_pessoa)->get()->keyBy('tipo');

        $caminhoFoto = LegacyIndividualPicture::whereKey($cod_pessoa)->value('caminho');
        if ($caminhoFoto) {
            $this->addDetalhe(detalhe: ['Nome', $pessoa->nome.'
                <p><img height="117" src="' . (new UrlPresigner)->getPresignedUrl(url: $caminhoFoto) . '"/></p>']);
        } else {
            $this->addDetalhe(detalhe: ['Nome', $pessoa->nome]);
        }

        if ($fisica?->social_name) {
            $this->addDetalhe(detalhe: ['Nome social e/ou afetivo', $fisica->social_name]);
        }

        $this->addDetalhe(detalhe: ['CPF', $fisica?->cpf]);

        if ($fisica?->data_nasc) {
            $this->addDetalhe(detalhe: ['Data de Nascimento', dataFromPgToBr(data_original: $fisica->data_nasc)]);
        }

        // Cor/Raça.
        $nameRace = LegacyRace::query()->whereHas('individual', fn ($q) => $q->whereKey($cod_pessoa))->value('nm_raca');
        if ($nameRace) {
            $this->addDetalhe(detalhe: ['Raça', $nameRace]);
        }

        if ($endereco?->address) {
            $end = $endereco->number ? ' nº ' . $endereco->number : '';
            $this->addDetalhe(detalhe: ['Endereço', $endereco->address . $end]);
        }

        if ($endereco?->complement) {
            $this->addDetalhe(detalhe: ['Complemento', $endereco->complement]);
        }

        if ($endereco?->city?->name) {
            $this->addDetalhe(detalhe: ['Cidade', $endereco->city->name]);
        }

        if ($endereco?->city?->state?->abbreviation) {
            $this->addDetalhe(detalhe: ['Estado', $endereco->city->state->abbreviation]);
        }

        if ($endereco?->postal_code) {
            $this->addDetalhe(detalhe: ['CEP', int2cep(int: $endereco->postal_code)]);
        }

        $telefoneFixo = $telefones[LegacyPhone::TYPE_LANDLINE] ?? null;
        if ($telefoneFixo?->fone) {
            $this->addDetalhe(detalhe: ['Telefone 1', sprintf('(%s) %s', $telefoneFixo->ddd, $telefoneFixo->fone)]);
        }

        $telefoneSecundario = $telefones[LegacyPhone::TYPE_MOBILE] ?? null;
        if ($telefoneSecundario?->fone) {
            $this->addDetalhe(detalhe: ['Telefone 2', sprintf('(%s) %s', $telefoneSecundario->ddd, $telefoneSecundario->fone)]);
        }

        $celular = $telefones[LegacyPhone::TYPE_MOBILE_ALT] ?? null;
        if ($celular?->fone) {
            $this->addDetalhe(detalhe: ['Celular', sprintf('(%s) %s', $celular->ddd, $celular->fone)]);
        }

        $fax = $telefones[LegacyPhone::TYPE_FAX] ?? null;
        if ($fax?->fone) {
            $this->addDetalhe(detalhe: ['Fax', sprintf('(%s) %s', $fax->ddd, $fax->fone)]);
        }

        if ($pessoa->url) {
            $this->addDetalhe(detalhe: ['Site', $pessoa->url]);
        }

        if ($pessoa->email) {
            $this->addDetalhe(detalhe: ['E-mail', $pessoa->email]);
        }

        if ($fisica?->sexo) {
            $this->addDetalhe(detalhe: ['Sexo', $fisica->sexo == 'M' ? 'Masculino' : 'Feminino']);
        }

        $vinculos = collect();
        if ($aluno = LegacyStudent::active()->where('ref_idpes', $cod_pessoa)->first(['cod_aluno'])) {
            $vinculos->push(sprintf(
                '<a target="_blank" href="/intranet/educar_aluno_det.php?cod_aluno=%s">Aluno</a>',
                $aluno->getKey()
            ));
        }

        if ($servidor = Employee::active()->find($cod_pessoa, ['cod_servidor', 'ref_cod_instituicao'])) {
            $vinculos->push(sprintf(
                '<a target="_blank" href="/intranet/educar_servidor_det.php?cod_servidor=%s&ref_cod_instituicao=%s">Servidor</a>',
                $servidor->getKey(),
                $servidor->ref_cod_instituicao
            ));
        }

        if ($mother = LegacyIndividual::query()->where('idpes_mae', $cod_pessoa)->first()) {
            $vinculos->push(sprintf(
                '<a target="_blank" href="/intranet/atendidos_det.php?cod_pessoa=%s">Mãe da Pessoa Física</a>',
                $mother->getKey(),
            ));
        }

        if ($father = LegacyIndividual::query()->where('idpes_pai', $cod_pessoa)->first()) {
            $vinculos->push(sprintf(
                '<a target="_blank" href="/intranet/atendidos_det.php?cod_pessoa=%s">Pai da Pessoa Física</a>',
                $father->getKey(),
            ));
        }

        if ($responsible = LegacyIndividual::query()->where('idpes_responsavel', $cod_pessoa)->first()) {
            $vinculos->push(sprintf(
                '<a target="_blank" href="/intranet/atendidos_det.php?cod_pessoa=%s">Responsável da Pessoa Física</a>',
                $responsible->getKey(),
            ));
        }

        if ($vinculos->isEmpty()) {
            $vinculos->push('Pessoa física não possui vínculos');
        }
        $this->addHtml('<tr><td class="formlttd" width="20%">Vínculos:</td><td class="formlttd">' . $vinculos->implode('<br>') . '</td></tr>');

        $fileService = new FileService(urlPresigner: new UrlPresigner);
        $files = $fileService->getFiles(relation: LegacyIndividual::find($cod_pessoa));

        if (is_array(value: $files) && count(value: $files) > 0) {
            $this->addHtml(html: view(view: 'uploads.upload-details', data: ['files' => $files])->render());
        }

        $obj_permissao = new clsPermissoes;

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 43, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, super_usuario: true)) {
            $this->url_novo = 'atendidos_cad.php';
            $this->url_editar = 'atendidos_cad.php?cod_pessoa_fj=' . $cod_pessoa;
        }

        $this->url_cancelar = 'atendidos_lst.php';

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Pessoa física', breadcrumbs: ['educar_pessoas_index.php' => 'Pessoas']);
    }

    public function Formular()
    {
        $this->title = 'Pessoa';
        $this->processoAp = 43;
    }
};
