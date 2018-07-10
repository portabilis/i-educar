var url = 'https://script.google.com/macros/s/AKfycbwVoUbYo4cukltNBjLynsfHrX2DWc71xDsBCwcTyV7ek-PrEeQ5/exec'
window.onload = function() {
  var form = document.querySelector('#test-form');

document.querySelector('#submit-form').addEventListener('click', function(e) {
  e.preventDefault();
  var name = document.querySelector("#conversion_name").value;
  var lname = document.querySelector("#conversion_lname").value;
  var email = document.querySelector("#conversion_email").value;
  sendEmail(name, lname, email);
});
};

function sendEmail(name, lastName, email) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.querySelector("#conversionSuccess").className = "show";
      setTimeout (function() {
        document.querySelector("#conversionSuccess").className = "hide";
      }, 3000)
    }
  };
  var params = "?nome="+name+"&sobrenome="+lastName+"&email="+email;
  xhttp.open("GET", url+params, true);
  xhttp.send();
}
