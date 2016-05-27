-- Cria tabela categoria_obra
-- @author Maurício Citadini Biléssimo <mauricio@portabilis.com.br>

create table pmieducar.categoria_obra(
	id serial primary key not null,
	descricao varchar(100) not null,
	observacoes varchar(300)
);