const $ = document;

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

let installButton = $.getElementById('install');
let passwordInput = $.getElementById('password');
let taskDesc = $.getElementById('taskDesc');
let progressBar = $.getElementById('installProgress');

installButton.addEventListener('click', function (e) {
  e.preventDefault();

  let password = passwordInput.value;
  let timestamp = + new Date();

  if (password == '') {
    alert('É necessário definir uma senha antes de prosseguir com a instalação.');
    return;
  }

  let base = new Promise(function (resolve) {
    return resolve(true);
  });

  $.querySelector('.install').style.display = '';
  $.querySelector('.installing').style.display = 'block';

  let steps = [
    {
      command: 'key:generate',
      description: 'Gerando chave da aplicação'
    }, {
      command: 'legacy:database',
      description: 'Inicializando banco de dados'
    }, {
      command: 'legacy:link',
      description: 'Gerando symlinks'
    }, {
      command: 'migrate',
      description: 'Rodando migrações'
    }, {
      command: 'admin:password ' + password,
      description: 'Definindo senha do admin'
    }
  ];

  for (let i = 0; i < steps.length; i++) {
    base = base.then(function () {
      let step = steps[i];

      progressBar.value = i;
      taskDesc.innerHTML = step.description;

      return get('/install.php?command=exec&param=' + step.command + '&time=' + timestamp);
    }).then(function (result) {
      let step = steps[i];

      return new Promise(function (resolve, reject) {
        let interval = setInterval(function() {
          get('/install.php?command=consult&pid=' + result + '&time=' + timestamp)
            .then(function (result) {
              result = parseInt(result, 10);

              if (result === 0) {
                resolve(result);
                clearInterval(interval);
              } else if (result > 0) {
                reject(step);
                clearInterval(interval);
              }
            })
        }, 1000);
      });
    });
  }

  base.then(function () {
    alert('Instalação concluída!');
    window.location = '/intranet/index.php';
  });

  base.catch(function (error) {
    alert('Ocorreu um erro no passo "' + error.description + '"' + "\n" + 'Verifique o log em storage/logs para identificar o problema e tente novamente.');
    $.location.reload(true);
  });

  return false;
});
