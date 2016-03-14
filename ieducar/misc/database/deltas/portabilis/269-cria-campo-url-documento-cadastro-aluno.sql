--Cria campo para armazenar URL de documentos no cadastro do aluno.

--@author   Paula Bonot <paula@portabilis.com.br>

ALTER TABLE pmieducar.aluno ADD COLUMN url_documento character varying(255);