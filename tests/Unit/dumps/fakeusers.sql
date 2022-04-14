DELETE
FROM pmieducar.usuario;
INSERT INTO pmieducar.usuario (cod_usuario, ref_cod_instituicao, ref_funcionario_cad, data_cadastro, ativo)
VALUES (1, 1, 1, NOW(), 1),
       (2, 1, 1, NOW(), 1);
