(function () {
  'use strict'

  const dataExport = (formId, resource) => {
    const form = document.getElementById(formId)
    const data = new FormData(form)
    const queryString = new URLSearchParams(data).toString()
    const url = `/exports/${resource}?${queryString}`

    window.location = url
  }

  window.dataExport = dataExport
  document.getElementById('export-btn').style.marginTop = 0;
})()
