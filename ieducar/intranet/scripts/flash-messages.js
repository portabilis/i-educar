((w) => {
  'use strict'

  const flashMessages = {
    containerElm: $j('.flashMessages__container'),
    messagesElmSelector: '.flashMessages__message',
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
    },

    closeEvent: () => {
      flashMessages.containerElm.on('click', flashMessages.messagesElmSelector, e => {
        const $elm = $j(e.currentTarget)

        $elm.fadeOut(250, () => {
          $elm.remove()

          flashMessages.hideOverflow()
          flashMessages.showCloseAll()
        })
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
      const msgHtml = `<div title="Clique para fechar" class="flashMessages__message -${type}"><time>${hourString}</time>${msg}</div>`
      const msgElm = $j(msgHtml).hide()

      flashMessages.overflowDisabled = false

      msgElm.prependTo(flashMessages.containerElm).fadeIn(250)

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
