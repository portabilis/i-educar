-- Cria tabela de relacao entre categoria e obra
-- @author Maurício Citadini Biléssimo <mauricio@portabilis.com.br>

create table pmieducar.relacao_categoria_acervo(
	ref_cod_acervo int not null,
	categoria_id int not null,
	foreign key (ref_cod_acervo) references pmieducar.acervo(cod_acervo),
	foreign key (categoria_id) references pmieducar.categoria_obra(id)
);