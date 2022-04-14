/* jshint esversion: 6 */

function get(url) {
  return new Promise(function (resolve, reject) {
      const xhr = new XMLHttpRequest();

      xhr.open('GET', url);

      xhr.onload = function () {
          resolve(xhr.responseText);
      };

      xhr.onerror = function () {
          reject(xhr.statusText);
      };

      xhr.send();
  });
}

const $ = document;
const installButton = $.getElementById('install');
const updateButton = $.getElementById('update');
const passwordInput = $.getElementById('password');
const taskDesc = $.getElementById('taskDesc');
const progressBar = $.getElementById('installProgress');

if (updateButton) {
    updateButton.addEventListener('click', function (e) {
        e.preventDefault();

        const parent = updateButton.parentNode;
        const loading = parent.previousElementSibling;
        const timestamp = + new Date();

        loading.style.display = 'block';
        parent.parentNode.removeChild(parent);

        const steps = [
            {
                command: 'link',
                description: 'Gerando symlinks'
            }, {
                command: 'migrate',
                description: 'Executando migrações'
            }
        ];

        let base = new Promise(function (resolve) {
            return resolve(true);
        });

        for (let i = 0; i < steps.length; i++) {
            const step = steps[i];

            base = base.then(function () {
                let url = '/install.php?command=exec&param=' + step.command + '&id=' + timestamp;

                if (step.extra) {
                    url += '&extra=' + step.extra;
                }

                return get(url);
            }).then(function (result) {
                return new Promise(function (resolve, reject) {
                    const interval = setInterval(function () {
                        get('/install.php?command=consult&pid=' + result + '&id=' + timestamp)
                            .then(function (result) {
                                result = parseInt(result, 10);

                                if (result === 0) {
                                    resolve(result);
                                    clearInterval(interval);
                                } else if (result > 0) {
                                    reject(step);
                                    clearInterval(interval);
                                }
                            });
                    }, 1000);
                });
            });
        }

        base.then(function () {
            alert('Atualização realizada com sucesso!');
            $.location.reload(true);
        });

        base.catch(function (error) {
            alert('Ocorreu um erro ao atualizar sua instalação' + "\n" + 'Verifique o log em storage/logs para identificar o problema e tente novamente.');
            $.location.reload(true);
        });

        return false;
    });
}

if (installButton) {
    installButton.addEventListener('click', function (e) {
        e.preventDefault();

        const password = passwordInput.value;
        const timestamp = + new Date();

        if (password == '') {
            alert('É necessário definir uma senha antes de prosseguir com a instalação.');
            return;
        }

        $.querySelector('.install').style.display = 'none';
        $.querySelector('.installing').style.display = 'block';

        const steps = [
            {
                command: 'key',
                description: 'Gerando chave da aplicação'
            }, {
              command: 'link',
              description: 'Gerando symlinks'
            }, {
              command: 'migrate',
              description: 'Executando migrações'
            }, {
              command: 'reports-link',
              description: 'Gerando symlinks dos relatórios'
            }, {
              command: 'password',
              description: 'Definindo senha do admin',
              extra: password
            }, {
                command: 'reports',
                description: 'Instalando relatórios'
            }
        ];

        progressBar.setAttribute('max', steps.length);

        let base = new Promise(function (resolve) {
            return resolve(true);
        });

        for (let i = 0; i < steps.length; i++) {
            const step = steps[i];

            base = base.then(function () {
                let url = '/install.php?command=exec&param=' + step.command + '&id=' + timestamp;

                if (step.extra) {
                    url += '&extra=' + step.extra;
                }

                progressBar.value = i;
                taskDesc.innerHTML = step.description;

                return get(url);
            }).then(function (result) {
                return new Promise(function (resolve, reject) {
                    const interval = setInterval(function() {
                        get('/install.php?command=consult&pid=' + result + '&id=' + timestamp)
                            .then(function (result) {
                                result = parseInt(result, 10);

                                if (result === 0) {
                                    resolve(result);
                                    clearInterval(interval);
                                } else if (result > 0) {
                                    reject(step);
                                    clearInterval(interval);
                                }
                            });
                    }, 1000);
                });
            });
        }

        base.then(function () {
            progressBar.value = progressBar.value + 1;
            alert('Instalação concluída!');
            $.location.reload(true);
        });

        base.catch(function (error) {
            alert('Ocorreu um erro no passo "' + error.description + '"' + "\n" + 'Verifique o log em storage/logs para identificar o problema e tente novamente.');
            $.location.reload(true);
        });

        return false;
    });
}
