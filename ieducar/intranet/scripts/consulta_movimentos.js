$j(function () {
  let consulta = {
    links: $j('.mostra-consulta'),
    bind: function () {
      let that = this;

      this.links.on('click', function (e) {
        e.preventDefault();

        let $this = $j(this);
        let params = {
          oper: 'get',
          resource: 'alunos',
          escola: $this.data('escola'),
          tipo: $this.data('tipo'),
          data_inicial: $this.data('inicial'),
          data_final: $this.data('final'),
          curso: $this.data('curso'),
          ano: $this.data('ano')
        };

        console.log(params);

        that.request(params);

        return false;
      });
    },
    request: function (params) {
      let url  = '/module/Api/ConsultaMovimentoGeral';

      $j.getJSON(url, params, function (d) {
        console.log(d);
      });
    },
    init: function () {
      this.bind();
    }
  };

  consulta.init();
});
