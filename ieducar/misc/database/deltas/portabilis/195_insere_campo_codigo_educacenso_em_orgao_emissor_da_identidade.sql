--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE cadastro.orgao_emissor_rg ADD COLUMN codigo_educacenso INTEGER;
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '10' WHERE descricao = 'SSP';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '40' WHERE descricao = 'Ministérios Militares';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '41' WHERE descricao = 'Ministério da Aeronáutica';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '42' WHERE descricao = 'Ministério do Exército';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '43' WHERE descricao = 'Ministério da Marinha';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '44' WHERE descricao = 'Polícia Federal';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '60' WHERE descricao = 'Carteira de Identidade Classista';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '61' WHERE descricao = 'Conselho Regional de Administração';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '63' WHERE descricao = 'Conselho Regional de Biblioteconomia';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '64' WHERE descricao = 'Conselho Regional de Contabilidade';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '65' WHERE descricao = 'Conselho Regional de Corretores Imóveis';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '67' WHERE descricao = 'Conselho Regional de Engenharia, Arquitetura e Agronomia';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '71' WHERE descricao = 'Conselho Regional de Medicina';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '72' WHERE descricao = 'Conselho Regional de Medicina Veterinária';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '73' WHERE descricao = 'Ordem dos Músicos do Brasil';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '75' WHERE descricao = 'Conselho Regional de Odontologia';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '77' WHERE descricao = 'Conselho Regional de Psicologia';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '78' WHERE descricao = 'Conselho Regional de Química';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '80' WHERE descricao = 'Ordem dos Advogados do Brasil';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '81' WHERE descricao = 'Outros Emissores';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '82' WHERE descricao = 'Documento Estrangeiro';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '62' WHERE descricao = 'Conselho Regional de Assist. Social';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '66' WHERE descricao = 'Conselho Regional de Enfermagem';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '68' WHERE descricao = 'Conselho Regional de Estatística';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '69' WHERE descricao = 'Conselho Regional de Farmácia';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '70' WHERE descricao = 'Conselho Regional de Fisioterapia e Terapia Ocupacional';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '74' WHERE descricao = 'Conselho Regional de Nutrição';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '76' WHERE descricao = 'Conselho Regional de Profissionais de Relações Públicas';
UPDATE cadastro.orgao_emissor_rg SET codigo_educacenso = '79' WHERE descricao = 'Conselho Regional de Representantes Comerciais';


-- UNDO

ALTER TABLE cadastro.orgao_emissor_rg DROP COLUMN codigo_educacenso;