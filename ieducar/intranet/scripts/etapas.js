$j(function () {
    var etapasHandler = {
        module: 0,
        init: function () {
            this.removeTableCellsAndRows();
            this.setupModule();
            this.selectModule();
            this.submit();
        },
        submit: function () {
            var that = this;

            $j('#btn_enviar').click(function (e) {
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
                    acao();
                } else {
                    alert('Ocorreram erros na validação dos campos. Verifique as mensagens e tente novamente.');
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
            var that = this,
                fields = $j('[id^=data_inicio], [id^=data_fim]'),
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
                    nextLine = parentLine.next('[id^="tr_modulos_ano_letivo["]'),
                    startDateElm = parentLine.find('[id^="data_inicio["]'),
                    startDateTs = that.makeTimestamp(that.getDateParts(startDateElm.val()));

                if (nextLine.length < 1) {
                    var validYears = [currentYear, nextYear];

                    if (validYears.indexOf(dateParts.year) === -1) {
                        valid = false;
                        that.addError(elm, 'O ano "' + dateParts.year + '" não é válido. Utilize o ano especificado ou próximo.');

                        return;
                    }
                } else {
                    if (dateParts.year !== currentYear) {
                        valid = false;
                        that.addError(elm, 'O ano "' + dateParts.year + '" não é válido. Utilize o ano especificado.');

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
                    previousLine = parentLine.prev('[id^="tr_modulos_ano_letivo["]');

                if (previousLine.length < 1) {
                    var validYears = [currentYear, previousYear];

                    if (validYears.indexOf(dateParts.year) === -1) {
                        valid = false;
                        that.addError(elm, 'O ano "' + dateParts.year + '" não é válido. Utilize o ano especificado ou anterior.');

                        return;
                    }
                } else {
                    if (dateParts.year !== currentYear) {
                        valid = false;
                        that.addError(elm, 'O ano "' + dateParts.year + '" não é válido. Utilize o ano especificado.');

                        return;
                    }

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
                addLink = $j('[id^=btn_add]');

            $j('td#td_acao').hide();
            $j('[id^=link_remove').parent().hide();
            $j('#adicionar_linha').hide();

            removeLinks.removeAttr('onclick');
            addLink.removeAttr('onclick');
            $j('#btn_enviar').removeAttr('onclick');
        },
        setupModule: function () {
            var $select = $j('#ref_cod_modulo'),
                val = parseInt($select.val(), 10),
                availableModules = window.modulosDisponiveis,
                moduleInfo = availableModules[val],
                etapas = moduleInfo.etapas,
                rows = this.countRows();

            if (Boolean(etapas) === false) {
                alert("Este módulo não possui o número de etapas definido.\nRealize esta alteração no seguinte caminho:\nCadastros > Tipos > Escolas > Tipos de etapas");
                history.back();
            }

            if (etapas > rows) {
                var diff = etapas - rows;

                this.addRows(diff);
            }

            if (etapas < rows) {
                var diff = rows - etapas;

                this.removeRows(diff);
            }
        },
        addRows: function (qtt) {
            for (var i = 0; i < qtt; i++) {
                tab_add_1.addRow();
                this.removeTableCellsAndRows();
            }
        },
        removeRows: function (qtt) {
            var rows = $j('tr[id^="tr_modulos_ano_letivo["]').get().reverse(),
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

            $select.change(function () {
                that.setupModule();
            })
        },
        countRows: function () {
            var rows = $j('tr[id^="tr_modulos_ano_letivo["]');

            return rows.length;
        },
        getYear: function () {
            return parseInt($j('#ref_ano_').val(), 10);
        }
    };

    etapasHandler.init();
});