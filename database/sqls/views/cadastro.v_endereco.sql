CREATE OR REPLACE VIEW cadastro.v_endereco AS
SELECT
    e.idpes,
    e.cep,
    e.idlog,
    e.numero,
    e.letra,
    e.complemento,
    e.idbai,
    e.bloco,
    e.andar,
    e.apartamento,
    l.nome AS logradouro,
    l.idtlog,
    b.nome AS bairro,
    m.nome AS cidade,
    m.sigla_uf,
    b.zona_localizacao
FROM cadastro.endereco_pessoa e,
    public.logradouro l,
    public.bairro b,
    public.municipio m
WHERE e.idlog = l.idlog AND e.idbai = b.idbai AND b.idmun = m.idmun AND e.tipo = 1::numeric
UNION
SELECT e.idpes,
    e.cep,
    NULL::numeric AS idlog,
    e.numero,
    e.letra,
    e.complemento,
    NULL::numeric AS idbai,
    e.bloco,
    e.andar,
    e.apartamento,
    e.logradouro,
    e.idtlog,
    e.bairro,
    e.cidade,
    e.sigla_uf,
    e.zona_localizacao
FROM cadastro.endereco_externo e
WHERE e.tipo = 1::numeric;
