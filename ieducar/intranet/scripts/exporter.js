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

  const dataExportResponsaveis = (formId1, resource1) => {
    const form1 = document.getElementById(formId1)
    const data1 = new FormData(form1)
    const queryString1 = new URLSearchParams(data1).toString()
    const url1 = `/exports/${resource1}?${queryString1}`

    window.location = url1
  }

  window.dataExport = dataExportResponsaveis
  document.getElementById('export-btn-responsaveis').style.marginTop = 0;
}
)()
