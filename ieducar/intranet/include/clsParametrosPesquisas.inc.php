<?php

/**
 * Classe de parametrização dos dados a serem informados para as listagens genéricas.
 *
 * @author Adriano Erik Weiguert Nagasava
 */
class clsParametrosPesquisas
{
    /**
     * Deve ser utilizado para informar se haverá submit (1) ou não na página (0).
     *
     * @var int
     */
    public $submit;

    /**
     * Deve ser usado para informar os nomes dos campos a serem utilizados.
     *
     * @var array
     */
    public $campo_nome;

    /**
     * Deve ser utilizado para informar o tipo do campo ("text" ou "select");
     *
     * @var array
     */
    public $campo_tipo;

    /**
     * Deve ser usado para informar os nomes dos campos a serem utilizados como indice.
     *
     * @var array
     */
    public $campo_indice;

    /**
     * Deve ser usado para informar os nomes dos campos a serem utilizados como valores.
     *
     * @var array
     */
    public $campo_valor;

    /**
     * Deve ser utilizado para informar se será uma pesquisa de pessoa física (F), pessoa jurídica (J) ou pessoa física e jurídica (FJ).
     *
     * @var char
     */
    public $pessoa;

    /**
     * Deve ser usado para indicar se deseja que apareça o botão de novo (S) ou não (N) na pesquisa de pessoa.
     *
     * @var char
     */
    public $pessoa_novo;

    /**
     * Deve ser usado para indicar se deseja que a tela seja aberta num iframe ("frame") ou para abrir na própria janela ("window").
     *
     * @var string
     */
    public $pessoa_tela;

    /**
     * Deve ser usado para informar o nome do campo para onde será retornado o valor "0", indicando que deve ser feito um novo cadastro de pessoa.
     *
     * @var string
     */
    public $pessoa_campo;

    /**
     * Deve ser usado para indicar se deseja que após o usuário selecionar uma pessoa, ela seja redirecionada pra uma tela de cadastro com as informações da pessoa selecionada (S) ou não (N).
     *
     * @var char
     */
    public $pessoa_editar;

    /**
     * Deve ser usado para indicar em qual sistema a pessoa física está/será cadastrada.
     *
     * @var int
     */
    public $ref_cod_sistema;

    /**
     * Deve ser usado para indicar se na inclusão o CPF da pessoa é obrigatório ("S") ou não ("N").
     *
     * @var int
     */
    public $pessoa_cpf;

    /**
     * Construtor da classe
     *
     * @return clsParametrosPesquisas
     */
    public function __construct()
    {
        $this->campo_nome   = [];
        $this->campo_tipo   = [];
        $this->campo_valor  = [];
        $this->campo_indice = [];
    }

    /**
     * Pega todos os atributos da classe e joga num array e retorna este array serializado e codificado para url.
     *
     * @return array
     */
    public function serializaCampos()
    {
        $parametros_serializados['submit']          = $this->submit;
        $parametros_serializados['campo_nome']      = $this->campo_nome;
        $parametros_serializados['campo_tipo']      = $this->campo_tipo;
        $parametros_serializados['campo_indice']    = $this->campo_indice;
        $parametros_serializados['campo_valor']     = $this->campo_valor;
        $parametros_serializados['pessoa']          = $this->pessoa;
        $parametros_serializados['pessoa_novo']     = $this->pessoa_novo;
        $parametros_serializados['pessoa_tela']     = $this->pessoa_tela;
        $parametros_serializados['pessoa_campo']    = $this->pessoa_campo;
        $parametros_serializados['pessoa_editar']   = $this->pessoa_editar;
        $parametros_serializados['ref_cod_sistema'] = $this->ref_cod_sistema;
        $parametros_serializados['pessoa_cpf']      = $this->pessoa_cpf;
        $parametros_serializados                    = serialize($parametros_serializados);
        $parametros_serializados                    = urlencode($parametros_serializados);

        return $parametros_serializados;
    }

    /**
     * Recebe os atributos em um array serializado, "deserializa o array e preenche os atributos.
     *
     * @param array $parametros_serializados
     */
    public function deserializaCampos($parametros_serializados)
    {
        $parametros_serializados = str_replace('\\', null, $parametros_serializados);
        $parametros_serializados = unserialize($parametros_serializados);
        $this->submit            = $parametros_serializados['submit'];
        $this->campo_nome        = $parametros_serializados['campo_nome'];
        $this->campo_tipo        = $parametros_serializados['campo_tipo'];
        $this->campo_indice      = $parametros_serializados['campo_indice'];
        $this->campo_valor       = $parametros_serializados['campo_valor'];
        $this->pessoa            = $parametros_serializados['pessoa'];
        $this->pessoa_novo       = $parametros_serializados['pessoa_novo'];
        $this->pessoa_tela       = $parametros_serializados['pessoa_tela'];
        $this->pessoa_campo      = $parametros_serializados['pessoa_campo'];
        $this->pessoa_editar     = $parametros_serializados['pessoa_editar'];
        $this->ref_cod_sistema   = $parametros_serializados['ref_cod_sistema'];
        $this->pessoa_cpf        = $parametros_serializados['pessoa_cpf'];
    }

    /**
     * Gera um array com todos os atributos da classe.
     *
     * @return array
     */
    public function geraArrayComAtributos()
    {
        $parametros_serializados['submit']          = $this->submit;
        $parametros_serializados['campo_nome']      = $this->campo_nome;
        $parametros_serializados['campo_tipo']      = $this->campo_tipo;
        $parametros_serializados['campo_indice']    = $this->campo_indice;
        $parametros_serializados['campo_valor']     = $this->campo_valor;
        $parametros_serializados['pessoa']          = $this->pessoa;
        $parametros_serializados['pessoa_novo']     = $this->pessoa_novo;
        $parametros_serializados['pessoa_tela']     = $this->pessoa_tela;
        $parametros_serializados['pessoa_campo']    = $this->pessoa_campo;
        $parametros_serializados['pessoa_editar']   = $this->pessoa_editar;
        $parametros_serializados['ref_cod_sistema'] = $this->ref_cod_sistema;
        $parametros_serializados['pessoa_cpf']      = $this->pessoa_cpf;

        return $parametros_serializados;
    }

    /**
     * Preenche os atributos com os valores de um array.
     *
     * @param array $parametros_serializados
     */
    public function preencheAtributosComArray($parametros_serializados)
    {
        $this->submit          = $parametros_serializados['submit'];
        $this->campo_nome      = $parametros_serializados['campo_nome'];
        $this->campo_tipo      = $parametros_serializados['campo_tipo'];
        $this->campo_indice    = $parametros_serializados['campo_indice'];
        $this->campo_valor     = $parametros_serializados['campo_valor'];
        $this->pessoa          = $parametros_serializados['pessoa'];
        $this->pessoa_novo     = $parametros_serializados['pessoa_novo'];
        $this->pessoa_tela     = $parametros_serializados['pessoa_tela'];
        $this->pessoa_campo    = $parametros_serializados['pessoa_campo'];
        $this->pessoa_editar   = $parametros_serializados['pessoa_editar'];
        $this->ref_cod_sistema = $parametros_serializados['ref_cod_sistema'];
        $this->pessoa_cpf      = $parametros_serializados['pessoa_cpf'];
    }

    /**
     * Adiciona um novo campo do tipo texto a ser buscado na pesquisa e setado após ela.
     *
     * @param string $campo_nome
     * @param string $campo_valor
     */
    public function adicionaCampoTexto($campo_nome, $campo_valor)
    {
        $this->campo_nome[]   = $campo_nome;
        $this->campo_tipo[]   = 'text';
        $this->campo_indice[] = 0;
        $this->campo_valor[]  = $campo_valor;
    }

    /**
     * Adiciona um novo campo do tipo select a ser buscado na pesquisa e setado após ela.
     *
     * @param string $campo_nome
     * @param string $campo_indice
     * @param string $campo_valor
     */
    public function adicionaCampoSelect($campo_nome, $campo_indice, $campo_valor)
    {
        $this->campo_nome[]   = $campo_nome;
        $this->campo_tipo[]   = 'select';
        $this->campo_indice[] = $campo_indice;
        $this->campo_valor[]  = $campo_valor;
    }

    /**
     * Seta o nome do campo especificado na posição indicada.
     *
     * @param int    $posicao
     * @param string $valor
     */
    public function setCampoNome($posicao, $valor)
    {
        $this->campo_nome[$posicao] = $valor;
    }

    /**
     * Caso seja passada a posição do campo por parâmetro, retorna o nome do campo especificado, senão, retorna um array com os nomes de todos os campos.
     *
     * @param int $posicao
     *
     * @return string or array
     */
    public function getCampoNome($posicao = null)
    {
        if (is_numeric($posicao)) {
            return $this->campo_nome[$posicao];
        } else {
            return $this->campo_nome;
        }
    }

    /**
     * Seta o tipo do campo especificado na posição indicada como "text".
     *
     * @param int $posicao
     */
    public function setCampoTipoTexto($posicao)
    {
        $this->campo_tipo[$posicao] = 'text';
    }

    /**
     * Seta o tipo do campo especificado na posição indicada como "select".
     *
     * @param int $posicao
     */
    public function setCampoTipoSelect($posicao)
    {
        $this->campo_tipo[$posicao] = 'select';
    }

    /**
     * Caso seja passada a posição do campo por parâmetro, retorna o tipo do campo especificado, senão, retorna um array com os tipos de todos os campos.
     *
     * @param int $posicao
     *
     * @return string or array
     */
    public function getCampoTipo($posicao = null)
    {
        if (is_numeric($posicao)) {
            return $this->campo_tipo[$posicao];
        } else {
            return $this->campo_tipo;
        }
    }

    /**
     * Seta o indice do campo especificado na posição indicada.
     *
     * @param int    $posicao
     * @param string $valor
     */
    public function setCampoIndice($posicao, $valor)
    {
        $this->campo_indice[$posicao] = $valor;
    }

    /**
     * Caso seja passada a posição do campo por parâmetro, retorna o indice do campo especificado, senão, retorna um array com os indices de todos os campos.
     *
     * @param int $posicao
     *
     * @return int or array
     */
    public function getCampoIndice($posicao = null)
    {
        if (is_numeric($posicao)) {
            return $this->campo_indice[$posicao];
        } else {
            return $this->campo_indice;
        }
    }

    /**
     * Seta o nome do campo que será buscado na tabela na posição indicada.
     *
     * @param int    $posicao
     * @param string $valor
     */
    public function setCampoValor($posicao, $valor)
    {
        $this->campo_valor[$posicao] = $valor;
    }

    /**
     * Caso seja passada a posição do campo por parâmetro, retorna o nome do campo que será buscado na tabela, senão, retorna um array com todos os nomes dos campos que irão ser buscados na tabela.
     *
     * @param int $posicao
     *
     * @return string or array
     */
    public function getCampoValor($posicao = null)
    {
        if (is_numeric($posicao)) {
            return $this->campo_valor[$posicao];
        } else {
            return $this->campo_valor;
        }
    }

    /**
     * Deve ser passado o valor 1 caso a página tenha "auto-submit" ou o valor 0 caso não tenha.
     *
     * @param int $submit
     */
    public function setSubmit($submit)
    {
        $this->submit = $submit;
    }

    /**
     * Retorna 1 caso a página tenha "auto-submit" ou o 0 caso não tenha.
     *
     * @return int
     */
    public function getSubmit()
    {
        return $this->submit;
    }

    /**
     * Deve ser passado 'F' se for pesquisar uma pessoa física, 'J' se for pesquisar uma pessoa jurídica e 'FJ' se não importar o tipo da pessoa que irá ser pesquisada.
     * opcoes: ('F' || 'J' || "FJ" || "FUNC")
     *
     * @param string $pessoa
     */
    public function setPessoa($pessoa)
    {
        $this->pessoa = $pessoa;
    }

    /**
     * Retorna 'F' se for pesquisar uma pessoa física, 'J' se for pesquisar uma pessoa jurídica e 'FJ' se não importar o tipo da pessoa que irá ser pesquisada.
     *
     * @return string
     */
    public function getPessoa()
    {
        return  $this->pessoa;
    }

    /**
     * Deve ser passado 'S' se deseja que apareça o botão de novo na pesquisa de pessoa ou 'N' caso não deseje.
     *
     * @param char $pessoa_novo
     */
    public function setPessoaNovo($pessoa_novo)
    {
        $this->pessoa_novo = $pessoa_novo;
    }

    /**
     * Retorna 'S' se deseja que apareça o botão de novo na pesquisa de pessoa ou 'N' caso não deseje.
     *
     * @return char
     */
    public function getPessoaNovo()
    {
        return $this->pessoa_novo;
    }

    /**
     * Deve ser passado "frame" para indicar se deseja que a tela seja aberta num iframe ou "window" para abrir na própria janela.
     *
     * @param string $pessoa_tela
     */
    public function setPessoaTela($pessoa_tela)
    {
        $this->pessoa_tela = $pessoa_tela;
    }

    /**
     * Retorna "frame" para indicar se deseja que a tela seja aberta num iframe ou "window" para abrir na própria janela.
     *
     * @return string
     */
    public function getPessoaTela()
    {
        return $this->pessoa_tela;
    }

    /**
     * Deve ser passado o nome do campo para onde será retornado o valor "0", indicando que deve ser feito um novo cadastro de pessoa.
     *
     * @param string $pessoa_campo
     */
    public function setPessoaCampo($pessoa_campo)
    {
        $this->pessoa_campo = $pessoa_campo;
    }

    /**
     * Retorna o nome do campo para onde será retornado o valor "0", indicando que deve ser feito um novo cadastro de pessoa.
     *
     * @return string
     */
    public function getPessoaCampo()
    {
        return $this->pessoa_campo;
    }

    /**
     * Deve ser passado 'S' para indicar se deseja que após o usuário selecionar uma pessoa, ela seja redirecionada pra uma tela de cadastro com as informações da pessoa selecionada ou 'N' caso não deseje.
     *
     * @param char $pessoa_editar
     */
    public function setPessoaEditar($pessoa_editar)
    {
        $this->pessoa_editar = $pessoa_editar;
    }

    /**
     * Retorna 'S' para indicar se deseja que após o usuário selecionar uma pessoa, ela seja redirecionada pra uma tela de cadastro com as informações da pessoa selecionada ou 'N' caso não deseje.
     *
     * @return char
     */
    public function getPessoaEditar()
    {
        return $this->pessoa_editar;
    }

    /**
     * Deve ser usado para passar o código do sistema em que a pessoa física está/será cadastrada.
     *
     * @param int $ref_cod_sistema
     */
    public function setCodSistema($ref_cod_sistema)
    {
        $this->ref_cod_sistema = $ref_cod_sistema;
    }

    /**
     * Retorna o código do sistema em que a pessoa física está/será cadastrada.
     *
     * @return int
     */
    public function getCodSistema()
    {
        return $this->ref_cod_sistema;
    }

    /**
     * Deve ser usado para passar o "S" caso o CPF seja obrigatório na inclusão de uma pessoa ou "N"
     * caso contrário.
     *
     * @param int $pessoa_cpf
     */
    public function setPessoaCPF($pessoa_cpf)
    {
        $this->pessoa_cpf = $pessoa_cpf;
    }

    /**
     * Retorna o "S" se o CPF for obrigatório na inclusão de uma pessoa ou "N" caso não seja.
     *
     * @return char
     */
    public function getPessoaCPF()
    {
        return $this->pessoa_cpf;
    }
}
