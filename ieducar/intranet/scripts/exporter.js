(function () {
  'use strict'
  
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
