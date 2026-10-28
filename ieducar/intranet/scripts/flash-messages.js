((w) => {
  'use strict'

  const flashMessages = {
    containerElm: $j('.flashMessages__container'),
    messagesElmSelector: '.flashMessages__message',
    closeMessageElmSelector: '.close-msg',
    controlsElm: $j('.flashMessages__controls'),
    closeAllElm: $j('.flashMessages__controls a[data-action="closeAll"]'),
    showAllElm: $j('.flashMessages__controls a[data-action="showAll"]'),
    visibleCount: 2,
    overflowDisabled: false,

    init: () => {
      flashMessages.closeAllElm.hide()
      flashMessages.showAllElm.hide()
      flashMessages.closeEvent()
      flashMessages.closeAllEvent()
      flashMessages.showAllEvent()
      flashMessages.showCloseAll()
      flashMessages.hideOverflow()
    },

    closeEvent: () => {
      flashMessages.containerElm.on('click', flashMessages.closeMessageElmSelector, e => {
        e.preventDefault()

        const $elm = $j(e.currentTarget)
        const $msg = $elm.closest('.flashMessages__message')

        $msg.fadeOut(250, () => {
          $msg.remove()

          flashMessages.hideOverflow()
          flashMessages.showCloseAll()
        })

        return false
      })
    },

    closeAllEvent: () => {
      flashMessages.closeAllElm.on('click', e => {
        e.preventDefault()

        const $msgs = $j(flashMessages.messagesElmSelector)

        $msgs.fadeOut(250, () => {
          $msgs.remove()

          flashMessages.closeAllElm.hide()
          flashMessages.showAllElm.hide()
        })

        return false
      })
    },

    showAllEvent: () => {
      flashMessages.showAllElm.on('click', e => {
        e.preventDefault()

        const $msgs = $j(flashMessages.messagesElmSelector)

        flashMessages.overflowDisabled = true

        $msgs.fadeIn(250, () => {
          flashMessages.showAllElm.hide()
        })

        return false
      })
    },

    hideOverflow: () => {
      let $msgs = $j(flashMessages.messagesElmSelector)

      if (flashMessages.overflowDisabled) {
        return
      }

      if ($msgs.length > flashMessages.visibleCount) {
        $($msgs.get()).each((elm, i) => {
          if (i >= flashMessages.visibleCount) {
            $j(elm).hide()
          } else {
            $j(elm).show()
          }
        })

        flashMessages.showAllElm
          .show()
          .find('span')
          .html($j(flashMessages.messagesElmSelector + ':hidden').length)
      } else {
        flashMessages.showAllElm.hide()
      }
    },

    showCloseAll: () => {
      const $msgs = $j(flashMessages.messagesElmSelector)

      if ($msgs.length > 1) {
        flashMessages.closeAllElm.show()
      } else {
        flashMessages.closeAllElm.hide()
      }
    },

    add: (type = 'error', msg = '') => {
      const date = new Date()
      const hourString = `${date.getHours()}:${date.getMinutes()}:${date.getSeconds()}`
      // O erro interrompe o leitor de tela; os demais avisos esperam ele terminar de falar.
      const role = type === 'error' ? 'alert' : 'status'
      const msgHtml = `<div class="flashMessages__message -${type}"><button type="button" class="close-msg" aria-label="Fechar aviso">×</button><time aria-hidden="true">${hourString}</time><div class="flashMessages__text" role="${role}">${msg}</div></div>`

      // Entra transparente em vez de oculto: o leitor de tela ignora o que
      // está com display none e pode não anunciar o aviso quando ele aparece.
      const msgElm = $j(msgHtml).css('opacity', 0)

      flashMessages.overflowDisabled = false

      msgElm.prependTo(flashMessages.containerElm).animate({ opacity: 1 }, 250)

      flashMessages.hideOverflow()
      flashMessages.showCloseAll()
    },

    error: (msg) => {
      flashMessages.add('error', msg)
    },

    notice: (msg) => {
      flashMessages.add('notice', msg)
    },

    success: (msg) => {
      flashMessages.add('success', msg)
    },

    info: (msg) => {
      flashMessages.add('info', msg)
    }
  }

  flashMessages.init()

  w.flashMessages = {
    add: flashMessages.add,
    error: flashMessages.error,
    notice: flashMessages.notice,
    success: flashMessages.success,
    info:  flashMessages.info
  }
})(window)
