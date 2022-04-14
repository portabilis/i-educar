$j(function () {
    var etapasHandler = {
        env: 'ano',
        module: 0,
        selectors: {
            'ano': {
                'stepsRows': 'tr[id^="tr_modulos_ano_letivo["]',
                'year': '#ref_ano_'
            },
            'turma': {
                'stepsRows': 'tr[id^="tr_turma_modulo["]',
                'year': '#ano_letivo'
            }
        },
        init: function () {
            this.setupEnv();
            this.removeTableCellsAndRows();
            this.setCurrentModule();
            this.initModule();
            this.selectModule();
        },
        getSelector: function (key) {
            return this.selectors[this.env][key] || undefined;
        },
        setCurrentModule: function () {
            var val = $j('#ref_cod_modulo').val();

            if (val === '') {
                val = 0;
            }

            this.module = parseInt(val, 10);

            return this.module;
        },
        setupEnv: function () {
            if ($j('tr[id^="tr_turma_modulo["]').length > 0) {
                this.env = 'turma';
            }
        },
        submit: function () {
            var that = this;

            $j('#btn_enviar').click(function (e) {
                if (validationUtils.validatesFields(true)) {
                    if (parseInt($j('#padrao_ano_escolar').val(), 10) === 1) {
                        if (typeof window.valida !== "undefined") {
                            // reproduzindo função encontrada em modules/Cadastro/Assets/Javascripts/Turma.js:332
                            if (validationUtils.validatesFields(true)) {
                                window.valida();
                            }
                        } else {
                            window.acao();
                        }

                        return;
                    }

                    e.preventDefault();

                    that.resetErrors();

                    if (!that.validateDates()) {
                        alert('Ocorreram erros na validação dos campos. Verifique as mensagens e tente novamente.');

                        return false;
                    }

                    var validations = [
                        'validateStartDates',
                        'validateEndDates'
                    ];

                    var valid = true;

                    $j.each(validations, function (i, validation) {
                        if (!that[validation]()) {
                            valid = false;
                        }
                    });

                    if (valid) {
                        if (typeof window.valida !== "undefined") {
                            // reproduzindo função encontrada em modules/Cadastro/Assets/Javascripts/Turma.js:332
                            if (validationUtils.validatesFields(true)) {
                                window.valida();
                            }
                        } else {
                            window.acao();
                        }
                    } else {
                        alert('Ocorreram erros na validação dos campos. Verifique as mensagens e tente novamente.');
                    }
                }
                return false;
            });
        },
        addError: function (elm, msg) {
            messageUtils.error(msg, elm);
        },
        resetErrors: function () {
            $j('input.error').removeClass('error');
        },
        validateDates: function () {
            var fields = $j('[id^=data_inicio], [id^=data_fim]'),
                valid = true;

            fields.each(function (i, elm) {
                if (!validationUtils.validatesDateFieldAlt(elm)) {
                    valid = false;
                }
            });

            return valid;
        },
        validateEndDates: function () {
            var that = this,
                currentYear = this.getYear(),
                nextYear = currentYear + 1,
                fields = $j('[id^="data_fim["]'),
                valid = true;

            fields.each(function (i, elm) {
                var $elm = $j(elm),
                    val = $elm.val(),
                    dateParts = that.getDateParts(val),
                    ts = that.makeTimestamp(dateParts),
                    parentLine = $elm.closest('tr'),
                    nextLine = parentLine.next(that.getSelector('stepsRows')),
                    startDateElm = parentLine.find('[id^="data_inicio["]'),
                    startDateTs = that.makeTimestamp(that.getDateParts(startDateElm.val()));

                if (nextLine.length < 1) {
                    var validYears = [currentYear, nextYear];

                    if (validYears.indexOf(dateParts.year) === -1) {
                        valid = false;
                        that.addError(elm, 'O ano "' + dateParts.year + '" não é válido. Utilize o ano especificado ou próximo.');

                        return;
                    }
                }

                if (ts <= startDateTs) {
                    valid = false;
                    that.addError(elm, 'A data final precisa ser maior que a data inicial desta etapa.');

                    return;
                }
            });

            return valid;
        },
        validateStartDates: function () {
            var that = this,
                currentYear = this.getYear(),
                previousYear = currentYear - 1,
                fields = $j('[id^="data_inicio["]'),
                valid = true;

            fields.each(function (i, elm) {
                var $elm = $j(elm),
                    val = $elm.val(),
                    dateParts = that.getDateParts(val),
                    ts = that.makeTimestamp(dateParts),
                    parentLine = $elm.closest('tr'),
                    previousLine = parentLine.prev(that.getSelector('stepsRows'));

                if (previousLine.length < 1) {
                    var validYears = [currentYear, previousYear];

                    if (validYears.indexOf(dateParts.year) === -1) {
                        valid = false;
                        that.addError(elm, 'O ano "' + dateParts.year + '" não é válido. Utilize o ano especificado ou anterior.');

                        return;
                    }
                } else {

                    var previousDate = previousLine.find('[id^="data_fim["]'),
                        previousTs = that.makeTimestamp(that.getDateParts(previousDate.val()));

                    if (ts <= previousTs) {
                        valid = false;
                        that.addError(elm, 'A data inicial precisa ser maior que a data final da etapa anterior.');

                        return;
                    }
                }
            });

            return valid;
        },
        makeTimestamp: function (parts) {
            var date = new Date(parts.year, parts.month - 1, parts.day);

            return Math.floor(+date / 1000);
        },
        getDateParts: function (date) {
            var parts = date.split('/');

            return {
                day: parseInt(parts[0], 10),
                month: parseInt(parts[1], 10),
                year: parseInt(parts[2], 10)
            }
        },
        removeTableCellsAndRows: function () {
            var removeLinks = $j('[id^=link_remove'),
                addLink = $j('[id^=btn_add]'),
                sendBtn = $j('#btn_enviar');

            $j('td#td_acao').hide();
            $j('[id^=link_remove').parent().hide();
            $j('#adicionar_linha').hide();

            removeLinks.removeAttr('onclick');
            addLink.removeAttr('onclick');
            sendBtn.removeAttr('onclick');
            sendBtn.unbind('click');
            this.submit();
        },
        makeDialog: function (params) {
            var container = $j('#dialog-container');

            if (container.length < 1) {
                $j('body').append('<div id="dialog-container" style="width: 500px;"></div>');
                container = $j('#dialog-container');
            }

            if (container.hasClass('ui-dialog-content')) {
                container.dialog('destroy');
            }

            container.empty();
            container.html(params.content);

            delete params['content'];

            container.dialog(params);
        },
        initModule: function () {
            var $select = $j('#ref_cod_modulo'),
                val = $select.val(),
                availableModules = window.modulosDisponiveis || [],
                moduleInfo = availableModules[val] || {},
                etapas = moduleInfo.etapas || undefined,
                rows = this.countRows();

                if (etapas > rows) {
                    var diff = etapas - rows;
                    this.addRows(diff);
                }

                if (etapas < rows) {
                    var diff = rows - etapas;
                    this.removeRows(diff);
                }
        },
        setupModule: function () {
            var $select = $j('#ref_cod_modulo'),
                val = $select.val(),
                availableModules = window.modulosDisponiveis || [],
                oldModuleInfo = availableModules[this.module] || {},
                moduleInfo = availableModules[val] || {},
                etapas = moduleInfo.etapas || undefined,
                rows = this.countRows(),
                that = this,
                content = '';

            val = (val === '') ? 0 : parseInt(val, 10);
            oldModuleInfo.module = this.module;

            if (val && Boolean(etapas) === false) {
                alert("Este módulo não possui o número de etapas definido.\nRealize esta alteração no seguinte caminho:\nCadastros > Tipos > Escolas > Tipos de etapas");

                $select.val((this.module === 0) ? '' : this.module);

                return;
            }

            this.setCurrentModule();

            if (etapas > rows) {
                var diff = etapas - rows;

                content += (diff == 1)
                  ? '<strong>Você está adicionando uma etapa!</strong> '
                  : `<strong>Você está adicionando ${diff} etapas!</strong> `;

                content += '<br><br>Esta mudança irá afetar o tipo de etapa onde as notas e faltas são emitidas. Exemplo: ';
                content += `Notas lançadas no primeiro <strong>${oldModuleInfo.label}</strong> vão aparecer no primeiro <strong>${moduleInfo.label}</strong>. `;
                content += '<br><br>Tem certeza de que deseja prosseguir?';

                this.makeDialog({
                    content: content,
                    title: 'Atenção!',
                    maxWidth: 860,
                    width: 860,
                    close: function () {
                        $select.val(oldModuleInfo.module);
                        $j(this).dialog('destroy');
                    },
                    buttons: [{
                        text: 'Sim',
                        click: function () {
                            that.addRows(diff);
                            $j(this).dialog('destroy');
                        }
                    }, {
                        text: 'Não',
                        click: function () {
                            $select.val(oldModuleInfo.module);
                            $j(this).dialog('destroy');
                        }
                    }]
                });
            }

            if (etapas < rows) {
                var diff = rows - etapas;

                content += (diff == 1)
                  ? '<strong>Você está removendo uma etapa!</strong> '
                  : `<strong>Você está removendo ${diff} etapas!</strong> `;

                content += '<br><br>Esta mudança irá afetar o tipo de etapa onde as notas e faltas são emitidas. Exemplo: ';
                content += `Notas lançadas no primeiro <strong>${oldModuleInfo.label}</strong> vão aparecer no primeiro <strong>${moduleInfo.label}</strong>. `;
                content += 'Se houver notas/faltas enviadas na etapa sendo removida esta alteração será bloqueada.<br><br>Tem certeza de que deseja prosseguir?';

                this.makeDialog({
                    content: content,
                    title: 'Atenção!',
                    maxWidth: 860,
                    width: 860,
                    close: function () {
                        $select.val(oldModuleInfo.module);
                        $j(this).dialog('destroy');
                    },
                    buttons: [{
                        text: 'Sim',
                        click: function () {
                            that.removeRows(diff);
                            $j(this).dialog('destroy');
                        }
                    }, {
                        text: 'Não',
                        click: function () {
                            $select.val(oldModuleInfo.module);
                            $j(this).dialog('destroy');
                        }
                    }]
                });
            }
        },
        addRows: function (qtt) {
            for (var i = 0; i < qtt; i++) {
                tab_add_1.addRow();
                this.removeTableCellsAndRows();
            }
        },
        removeRows: function (qtt) {
            var rows = $j(this.getSelector('stepsRows')).get().reverse(),
                count = 0;

            rows.each(function (elm) {
                if (count < qtt) {
                    tab_add_1.removeRow(elm);
                    count++;
                }
            });
        },
        selectModule: function () {
            var that = this,
                $select = $j('#ref_cod_modulo');

            $select.focus(function () {
                that.setCurrentModule();
            }).change(function () {
                if ($j('#tipoacao').val() === 'Novo') {
                    that.initModule();
                } else {
                    that.setupModule();
                }
            })
        },
        countRows: function () {
            var rows = $j(this.getSelector('stepsRows'));

            return rows.length;
        },
        getYear: function () {
            return parseInt($j(this.getSelector('year')).val(), 10);
        }
    };

    etapasHandler.init();
});

function atualizarEtapas () {
    var ano = $j("#ano").val();
    var ref_cod_escola = $j("#ref_cod_escola").val();
    
    var urlForPegarEtapas = postResourceUrlBuilder.buildUrl('/module/Api/AnoLetivoModulo', 'pegar-etapas', {});

    var options = {
        type     : 'POST',
        url      : urlForPegarEtapas,
        dataType : 'json',
        data     : {
            ano                 : ano,
            ref_cod_escola      : ref_cod_escola,
        },
        success     : handleAtualizarEtapas,
    };

    postResource(options);
}

function handleAtualizarEtapas (response) {
    var etapas = response.etapas;

    if (etapas === null) {
        alert("Ocorreu algum erro na obtenção das informações necessárias.")
    } else {
        var etapas_trs = document.getElementsByName("tr_turma_modulo[]");

        if (etapas.length === etapas_trs.length) {
            for (let index = 0; index < etapas_trs.length; index++) {
                const etapa_tr = etapas_trs[index];
                const etapa = etapas[index];

                etapa_tr.children[0].children[0].value = dataToBrasil(etapa[4]);
                etapa_tr.children[1].children[0].value = dataToBrasil(etapa[5]);
                etapa_tr.children[2].children[0].value = etapa[6];
            }

            alert("Etapas atualizadas com sucesso. Não esqueça de salvar.");
        } else {
            alert("Número de etapas da turma não coincide com o da escola. Operação interrompida.");
        }
    }
}

function dataToBrasil (dataFromBanco) {
    var data = "";
    var data_fragmentos = dataFromBanco.split('-');

    for (let index = data_fragmentos.length - 1; index >= 0; index--) {
        const data_fragmento = data_fragmentos[index];

        if (index !== 0) {
            data += data_fragmento + '/';
        } else {
            data += data_fragmento;
        }
    }
  
    return data
}