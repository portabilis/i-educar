<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AtualizaNomenclaturaDeConfiguracoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            UPDATE settings
            SET description = (
                CASE key
                    WHEN \'legacy.code\' THEN \'Habilitar código legado?\'
                    WHEN \'legacy.display_errors\' THEN \'Exibir erros código legado?\'
                    WHEN \'legacy.apis.access_key\' THEN \'Chave de acesso ao i-Educar\'
                    WHEN \'legacy.apis.secret_key\' THEN \'Chave secreta do i-Educar\'
                    WHEN \'legacy.apis.educacao_token_header\' THEN \'Cabeçalho do Token da Api do i-Diário\'
                    WHEN \'legacy.apis.educacao_token_key\' THEN \'Chave do Token da Api do i-Diário\'
                    WHEN \'legacy.app.administrative_pending.exist\' THEN \'Possui pendência administrativa?\'
                    WHEN \'legacy.app.administrative_pending.msg\' THEN \'Texto de pendência administrativa\'
                    WHEN \'legacy.app.administrative_tools_url\' THEN \'Url do administrativo\'
                    WHEN \'legacy.app.alunos.mostrar_codigo_sistema\' THEN \'Exibir campo "Código sistema" no cadastro do aluno\'
                    WHEN \'legacy.app.alunos.codigo_sistema\' THEN \'Label do campo "Código sistema" no cadastro do aluno\'
                    WHEN \'legacy.app.alunos.laudo_medico_obrigatorio\' THEN \'Obrigar laudo médico para alunos com deficiência\'
                    WHEN \'legacy.app.alunos.nao_apresentar_campo_alfabetizado\' THEN \'Não apresentar check "Alfabetizado" no cadastro do aluno\'
                    WHEN \'legacy.app.alunos.obrigar_recursos_tecnologicos\' THEN \'Obrigar informar recursos tecnológicos do aluno\'
                    WHEN \'legacy.app.auditoria.notas\' THEN \'Auditar lançamento de notas\'
                    WHEN \'legacy.app.aws.bucketname\' THEN \'AWS Bucket S3 para armazenar uploads\'
                    WHEN \'legacy.app.aws.awsacesskey\' THEN \'AWS Bucket S3 Acess Key\'
                    WHEN \'legacy.app.aws.awssecretkey\' THEN \'AWS Bucket S3 Secret Key\'
                    WHEN \'legacy.app.database.dbname\' THEN \'Nome do banco de dados\'
                    WHEN \'legacy.app.database.hostname\' THEN \'Host\'
                    WHEN \'legacy.app.database.password\' THEN \'Senha\'
                    WHEN \'legacy.app.database.port\' THEN \'Porta\'
                    WHEN \'legacy.app.database.username\' THEN \'Usuário\'
                    WHEN \'legacy.app.diario.nomenclatura_exame\' THEN \'Apresentar nota de exame como nota de conselho\'
                    WHEN \'legacy.app.entity.name\' THEN \'Nome da instituição\'
                    WHEN \'legacy.app.faltas_notas.mostrar_botao_replicar\' THEN \'Permitir replicar notas conceituais por área de conhecimento\'
                    WHEN \'legacy.app.filaunica.criterios\' THEN \'Apresentar critérios no fila única\'
                    WHEN \'legacy.app.filaunica.current_year\' THEN \'Ano atual para consultar protocolos do fila única\'
                    WHEN \'legacy.app.filaunica.ordenacao\' THEN \'Ordenação dos protocolos do fila única\'
                    WHEN \'legacy.app.filaunica.trabalho_obrigatorio\' THEN \'Obriga informar trabalho dos responsáveis do candidato do fila única\'
                    WHEN \'legacy.app.fisica.exigir_cartao_sus\' THEN \'Obrigar campo "Número da carteira do SUS" no cadastro do aluno\'
                    WHEN \'legacy.app.gtm.id\' THEN \'Código do tag manager\'
                    WHEN \'legacy.app.locale.country\' THEN \'País do cliente (código do i-Educar)\'
                    WHEN \'legacy.app.locale.province\' THEN \'Sigla UF do cliente\'
                    WHEN \'legacy.app.locale.timezone\' THEN \'Configuração de timezone\'
                    WHEN \'legacy.app.matricula.dependencia\' THEN \'Permitir matrículas de dependência?\'
                    WHEN \'legacy.app.matricula.multiplas_matriculas\' THEN \'Permitir múltiplas matrículas?\'
                    WHEN \'legacy.app.mostrar_aplicacao\' THEN \'Mostrar condições específicas do sistema para o cliente\'
                    WHEN \'legacy.app.name\' THEN \'Nome da entidade no controle de versão\'
                    WHEN \'legacy.app.processar_historicos_conceituais\' THEN \'Processar históricos de notas conceituais\'
                    WHEN \'legacy.app.projetos.ignorar_turno_igual_matricula\' THEN \'Permitir vincular alunos em projetos do mesmo período da turma em andamento\'
                    WHEN \'legacy.app.rdstation.private_token\' THEN \'Token do RDStation\'
                    WHEN \'legacy.app.rdstation.token\' THEN \'Token privado do RDStation\'
                    WHEN \'legacy.app.recaptcha_v3.minimum_score\' THEN \'Pontuação mínima (V3)\'
                    WHEN \'legacy.app.recaptcha_v3.private_key\' THEN \'Chave privada (V3)\'
                    WHEN \'legacy.app.recaptcha_v3.public_key\' THEN \'Chave pública (V3)\'
                    WHEN \'legacy.app.recaptcha.options.lang\' THEN \'Linguagem\'
                    WHEN \'legacy.app.recaptcha.options.secure\' THEN \'Nível de segurança\'
                    WHEN \'legacy.app.recaptcha.options.theme\' THEN \'Tema\'
                    WHEN \'legacy.app.recaptcha.private_key\' THEN \'Chave privada\'
                    WHEN \'legacy.app.recaptcha.public_key\' THEN \'Chave pública\'
                    WHEN \'legacy.app.remove_obrigatorios_cadastro_pessoa\' THEN \'Remove obrigatoriedade dos campos de pessoa física\'
                    WHEN \'legacy.app.reserva_vaga.permite_indeferir_candidatura\' THEN \'Permitir indeferir candidatura da reserva de vaga\'
                    WHEN \'legacy.app.rg_pessoa_fisica_pais_opcional\' THEN \'Tornar opcional informar RG para os pais dos alunos\'
                    WHEN \'legacy.app.template.pdf.logo\' THEN \'Caminho logo apresentada na impressão da agenda\'
                    WHEN \'legacy.app.template.vars.instituicao\' THEN \'Nome da instituição no template\'
                    WHEN \'legacy.app.template.pdf.titulo\' THEN \'Título Relatório PDF\'
                    WHEN \'legacy.app.template.layout\' THEN \'Layout do Template\'
                    WHEN \'legacy.app.user_accounts.default_password_expiration_period\' THEN \'Dias para expiração de senha\'
                    WHEN \'legacy.config.active_on_ieducar\' THEN \'Suspender cliente\'
                    WHEN \'legacy.educacenso.enable_export\' THEN \'Habilitar exportação do arquivo do Educacenso\'
                    WHEN \'legacy.filaunica.obriga_certidao_nascimento\' THEN \'Obrigar campo de certidão de nascimento no fila única\'
                    WHEN \'legacy.modules.error.email_recipient\' THEN \'Email destinatário de erros\'
                    WHEN \'legacy.report.atestado_vaga_alternativo\' THEN \'Emitir atestado de vaga alternativo\'
                    WHEN \'legacy.report.caminho_fundo_carteira_transporte\' THEN \'Caminho da imagem de fundo da carteira de transporte\'
                    WHEN \'legacy.report.caminho_fundo_certificado\' THEN \'Caminho da imagem de fundo do certificado de conclusão\'
                    WHEN \'legacy.report.carteira_estudante.codigo\' THEN \'Código apresentado na carteira do estudante\'
                    WHEN \'legacy.report.emitir_tabela_conversao\' THEN \'Apresentar tabela de conversão em documentos\'
                    WHEN \'legacy.report.header.alternativo\' THEN \'Emitir cabeçalho alternativo em documentos\'
                    WHEN \'legacy.report.header.show_data_emissao\' THEN \'Apresentar data de emissão no cabeçalho dos documentos\'
                    WHEN \'legacy.report.historico_escolar.modelo_sp\' THEN \'Apresentar apenas a opção "Modelo SP" na emissão de histórico escolar\'
                    WHEN \'legacy.report.lei_conclusao_ensino_medio\' THEN \'Lei de conclusão do ensino médio\'
                    WHEN \'legacy.report.lei_estudante\' THEN \'Lei do estudante\'
                    WHEN \'legacy.report.logo_file_name\' THEN \'Nome do arquivo referente à logo dos relatórios\'
                    WHEN \'legacy.report.modelo_atestado_transferencia_botucatu\' THEN \'Emitir modelo de Botucatu no atestado de transferência\'
                    WHEN \'legacy.report.modelo_atestado_transferencia_parauapebas\' THEN \'Emitir modelo de Parauapebas no atestado de transferência\'
                    WHEN \'legacy.report.modelo_ficha_individual\' THEN \'Modelo de ficha individual\'
                    WHEN \'legacy.report.mostrar_relatorios\' THEN \'Mostrar relatórios específicos para o cliente\'
                    WHEN \'legacy.report.portaria_aprovacao_pontos\' THEN \'Portaria aprovação de pontos\'
                    WHEN \'legacy.report.print_back_conclusion_certificate\' THEN \'Emitir verso do certificado de conclusão do ensino fundamental\'
                    WHEN \'legacy.report.default_factory\' THEN \'Factory principal\'
                    WHEN \'legacy.report.remote_factory.logo_name\' THEN \'Logo dos relatórios\'
                    WHEN \'legacy.report.remote_factory.password\' THEN \'Senha\'
                    WHEN \'legacy.report.remote_factory.this_app_name\' THEN \'Nome da aplicação nos relatórios\'
                    WHEN \'legacy.report.remote_factory.token\' THEN \'Token de segurança\'
                    WHEN \'legacy.report.remote_factory.url\' THEN \'Url dos relatórios\'
                    WHEN \'legacy.report.remote_factory.username\' THEN \'Usuário\'
                    WHEN \'legacy.report.show_error_details\' THEN \'Exibir detalhes dos erros de relatórios\'
                    WHEN \'legacy.report.source_path\' THEN \'Caminhos dos relatórios\'
                    WHEN \'legacy.modules.error.link_to_support\' THEN \'Link para obter suporte\'
                    WHEN \'legacy.modules.error.send_notification_email\' THEN \'Enviar e-mail de notificação de erro?\'
                    WHEN \'legacy.modules.error.send_notification_email\' THEN \'Exibir detalhes de erro?\'
                    WHEN \'legacy.modules.error.track\' THEN \'Habilitar track de erros\'
                    WHEN \'legacy.modules.error.tracker_name\' THEN \'Classe usada para registrar erros\'
                    WHEN \'legacy.report.reservas_de_vagas_integrais_por_escola.renda_per_capita_order\' THEN \'Ordenar lista de espera da reserva de vaga pela renda\'
                    WHEN \'legacy.report.diario_classe.dias_temporarios\' THEN \'Dias temporários do Diário de Classe\'
                    WHEN \'legacy.app.mailer.smtp.from_name\' THEN \'Nome de Exibição\'
                    WHEN \'legacy.app.mailer.smtp.from_email\' THEN \'Endereço e-mail de saída\'
                    WHEN \'legacy.app.mailer.smtp.host\' THEN \'SMTP Host\'
                    WHEN \'legacy.app.mailer.smtp.port\' THEN \'SMTP Porta\'
                    WHEN \'legacy.app.mailer.smtp.auth\' THEN \'Autenticação (SSL/TLS)\'
                    WHEN \'legacy.app.mailer.smtp.username\' THEN \'SMTP Usuário\'
                    WHEN \'legacy.app.mailer.smtp.password\' THEN \'SMTP Senha\'
                    WHEN \'legacy.app.mailer.smtp.encryption\' THEN \'Criptografia (SSL/TLS)\'
                    WHEN \'legacy.app.mailer.debug\' THEN \'SMTP Debug\'
                    WHEN \'preregistration.active\' THEN \'Habilitar cadastros no inscrições online\'
                    WHEN \'preregistration.city\' THEN \'Nome do município\'
                    WHEN \'preregistration.enabled\' THEN \'Habilitar inscrições online\'
                    WHEN \'preregistration.endpoint\' THEN \'Uri do sistema\'
                    WHEN \'preregistration.entity\' THEN \'Nome do cliente\'
                    WHEN \'preregistration.google_api_key\' THEN \'Chave da api do google\'
                    WHEN \'preregistration.grades\' THEN \'Códigos das séries permitidas\'
                    WHEN \'preregistration.ibge_code\' THEN \'Inep do município\'
                    WHEN \'preregistration.lat\' THEN \'Latitude inicial\'
                    WHEN \'preregistration.lng\' THEN \'Longitude inicial\'
                    WHEN \'preregistration.logo.horizontal\' THEN \'Url da logo horizontal\'
                    WHEN \'preregistration.logo.vertical\' THEN \'Url da logo vertical\'
                    WHEN \'preregistration.messages.initial_message\' THEN \'Mensagem inicial para preenchimento do formulário\'
                    WHEN \'preregistration.messages.review_message\' THEN \'Mensagem de revisão de preenchimento do formulário\'
                    WHEN \'preregistration.messages.subtitle\' THEN \'Mensagem de subtítulo\'
                    WHEN \'preregistration.messages.success_info\' THEN \'Mensagem informativa de sucesso\'
                    WHEN \'preregistration.messages.success_message\' THEN \'Mensagem conclusiva de sucesso\'
                    WHEN \'preregistration.messages.title\' THEN \'Título do formulário\'
                    WHEN \'preregistration.radius\' THEN \'Raio (em metros) das escolas que serão exibidas a partir do endereço\'
                    WHEN \'preregistration.state_abbreviation\' THEN \'Estado do município\'
                    WHEN \'preregistration.title\' THEN \'Título da página inicial\'
                    WHEN \'preregistration.token\' THEN \'Token de segurança\'
                    WHEN \'preregistration.year\' THEN \'Ano vigente\'
                    ELSE settings.description
                END
            );
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')
            ->update(['description' => '']);
    }
}
