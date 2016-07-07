-- Atualizar instituições de ensino superior conforme "Tabela IES - 2016"
-- @author Caroline Salib <caroline@portabilis.com.br>

UPDATE modules.educacenso_ies
SET nome = 'UNIVERSIDADE DO CONTESTADO',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =42)
WHERE ies_id = 441;


UPDATE modules.educacenso_ies
SET nome = 'UNIVERSIDADE DO SUL DE SANTA CATARINA',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =42)
WHERE ies_id = 494;


UPDATE modules.educacenso_ies
SET nome = 'CENTRO UNIVERSITARIO DE MANDAGUARI UNIMAN',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =41)
WHERE ies_id = 535;


UPDATE modules.educacenso_ies
SET nome = 'FACULDADE DE FORMACAO DE PROFESSORES DE SERRA TALHADA',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =26)
WHERE ies_id = 657;


UPDATE modules.educacenso_ies
SET nome = 'UNIVERSIDADE DE TAUBATE',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =35)
WHERE ies_id = 665;


UPDATE modules.educacenso_ies
SET nome = 'UNIVERSIDADE DO PLANALTO CATARINENSE',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =42)
WHERE ies_id = 1189;


UPDATE modules.educacenso_ies
SET nome = 'FACULDADES ADAMANTINENSES INTEGRADAS',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =35)
WHERE ies_id = 1292;


UPDATE modules.educacenso_ies
SET nome = 'FACULDADES INTEGRADAS DE SANTA FE DO SUL',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =35)
WHERE ies_id = 1356;


UPDATE modules.educacenso_ies
SET nome = 'CENTRO UNIVERSITARIO FUNDACAO SANTO ANDRE',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =35)
WHERE ies_id = 2183;


UPDATE modules.educacenso_ies
SET nome = 'UNIVERSIDADE COMUNITARIA DA REGIAO DE CHAPECO',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =42)
WHERE ies_id = 3151;


UPDATE modules.educacenso_ies
SET nome = 'UNIVERSIDADE DE RIO VERDE',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =52)
WHERE ies_id = 3974;


UPDATE modules.educacenso_ies
SET nome = 'CENTRO UNIVERSITARIO MUNICIPAL DE SAO JOSE',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =42)
WHERE ies_id = 4756;


UPDATE modules.educacenso_ies
SET nome = 'UNIVERSIDADE ALTO VALE DO RIO DO PEIXE',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =42)
WHERE ies_id = 15032;


UPDATE modules.educacenso_ies
SET nome = 'FACULDADE DE CIENCIAS DA SAUDE DE SERRA TALHADA',
           dependencia_administrativa_id = 3,
           tipo_instituicao_id =1,
           uf =
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge =26)
WHERE ies_id = 17775;

INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 10251 , 'FACULDADE ORTODOXA', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 51), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 12899 , 'FACULDADE METROPOLITANA DO VALE DO AÇO', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 31), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 13728 , 'FACULDADE DOS CARAJÁS', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 15), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 13764 , 'FACULDADE DE TECNOLOGIA DE AMPÉRE', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 41), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 14158 , 'FACULDADE DE TECNOLOGIA DE NOVO CABRAIS', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 43), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 14718 , 'FACULDADE PARANÁ', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 41), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 15500 , 'Faculdade Lusocapixaba', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 32), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 15562 , 'Faculdade Batista do Cariri', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 23), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 16602 , 'FACULDADE DE EDUCAÇÃO ELIÂ', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 15), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 16782 , 'Faculdade Mário Quintana', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 43), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 16849 , 'Faculdade Modal', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 31), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 16918 , 'FACULDADE CATÓLICA DE FEIRA DE SANTANA', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 29), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 16948 , 'Faculdade 28 de Agosto de Ensino e Pesquisa', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17025 , 'Faculdade de Educação Superior de Paragominas', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 15), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17091 , 'Faculdade de Negócios do Recife', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 26), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17115 , 'FACULDADE DA UNIÃO DE ENSINO E PESQUISA INTEGRADA', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 25), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17118 , 'Faculdade do Norte de Mato Grosso', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 51), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17289 , 'Faculdade de Teologia de Caratinga Uriel de Almeida Leitão', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 31), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17348 , 'Faculdade de Tecnologia dos Inconfidentes', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 31), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17355 , 'Faculdade de Educação em Ciências da Saúde', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17382 , 'Faculdade Ietec', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 31), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17394 , 'Faculdade Alencarina de Sobral', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 23), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17400 , 'Faculdade Menino Deus', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 43), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17403 , 'FACULDADE ARI DE SÁ', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 23), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17420 , 'FACULDADE CESUMAR DE PONTA GROSSA', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 41), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17460 , 'FACULDADE Profissional', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 41), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17558 , 'Faculdade Santo André', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 11), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17563 , 'FACULDADE COESP', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 25), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17565 , 'FACULDADE DE CIÊNCIAS HUMANAS,EXATAS E DA SAÚDE DO PIAUÍ', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 22), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17590 , 'Faculdade ISAE BRASIL', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 41), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17593 , 'Faculdade de Botucatu', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17598 , 'FACULDADE PROF. WLADEMIR DOS SANTOS', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17608 , 'Faculdade de Educação Paulistana', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17622 , 'Faculdade Talles de Mileto - Sede Dragão do Mar', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 23), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17628 , 'Faculdade do Maciço do Baturité', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 23), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17662 , 'Faculdade Galileu', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17670 , 'Faculdade de Quixeramobim', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 23), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17672 , 'INSTITUTO DE DIREITO PÚBLICO DE SÃO PAULO', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17674 , 'Faculdade de Educação de São Mateus', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 21), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17701 , 'FAP-FACULDADE DE PINHEIROS', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 32), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17731 , 'FACULDADE SESI-SP DE EDUCAÇÃO', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17749 , 'Faculdade América', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 32), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17763 , 'Faculdade SENAI de João Pessoa', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 25), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17816 , 'FACULDADE MAURÍCIO DE NASSAU DE FEIRA DE SANTANA', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 29), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17828 , 'Faculdade do Centro Leste - Cariacica', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 32), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17831 , 'Faculdade de Tecnologia e Negócios de Catalão', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 52), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17850 , 'FACULDADE TECNOLÓGICA SANTANNA', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 17854 , 'FACULDADE CAPITAL FEDERAL', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 18010 , 'Faculdade Estácio de Cuiabá', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 51), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 18019 , 'Faculdade do Educador', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 18023 , 'FACULDADE MAURÍCIO DE NASSAU DE PETROLINA', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 26), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 18067 , 'CISNE - Faculdade Tecnológica de Quixadá', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 23), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 18075 , 'FACULDADE MAURÍCIO DE NASSAU DE JABOATÃO DOS GUARARAPES', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 26), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 18114 , 'Faculdade Fasipe Mato Grosso', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 51), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 18133 , 'Faculdade Unida de Campinas Goiânia - FACUNICAMPS GOIÂNIA', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 52), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 18165 , 'Fundação Universidade Virtual do Estado de São Paulo', 2 , 1 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 18257 , 'FACULDADE SÄO JOSÉ', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 42), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 18288 , 'Faculdade Latino-americana', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 19500 , 'Faculdade de Tecnologia de São Carlos', 2 , 1 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 19501 , 'FACULDADE DE TECNOLOGIA SEBRAE', 2 , 1 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 19512 , 'INSTITUTO MASTER DE ENSINO PRESIDENTE ANTÔNIO CARLOS', 4 , 2 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 31), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 19578 , 'Faculdade de Tecnologia de Cotia', 2 , 1 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 19588 , 'FACULDADE DE EDUCAÇÃO TECNOLÓGICA DO ESTADO DO RIO DE JANEIRO', 2 , 1 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 33), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 19739 , 'FACULDADE DE TECNOLOGIA DE CAMPINAS', 2 , 1 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 19862 , 'Faculdade de Tecnologia de Bebedouro', 2 , 1 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 20478 , 'Faculdade de Tecnologia de Santana de Parnaíba', 2 , 1 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 35), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 21095 , 'Academia Militar das Agulhas Negras', 3 , 1 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 33), 1, now());


INSERT INTO modules.educacenso_ies(ies_id, nome, dependencia_administrativa_id, tipo_instituicao_id, uf, user_id, created_at)
VALUES ( 21206 , 'Escola de Educação Física do Exército', 3 , 1 ,
  (SELECT sigla_uf
   FROM public.uf
   WHERE cod_ibge = 33), 1, now());
