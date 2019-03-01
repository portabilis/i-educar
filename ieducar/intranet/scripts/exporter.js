(function () {
  'use strict'

  const dataExport = (formId, resource) => {
    const form = document.getElementById(formId)
    const data = new FormData(form)
    const queryString = new URLSearchParams(data).toString()
    const url = `/exports/${resource}?${queryString}`

    window.location = `/exports/${resource}?${queryString}`
  }

  window.dataExport = dataExport
})()
