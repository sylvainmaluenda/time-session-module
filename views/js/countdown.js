document.addEventListener("DOMContentLoaded", function () {
  const endDate = new Date(end_date).getTime();

  var bar = document.createElement("div");
  bar.id = "bo-countdown";
  bar.style.position = "fixed";
  bar.style.bottom = "0";
  bar.style.left = "0";
  bar.style.width = "100%";
  bar.style.background = "#e74c3c";
  bar.style.color = "#fff";
  bar.style.textAlign = "center";
  bar.style.padding = "10px";
  bar.style.zIndex = "9999";

  document.body.appendChild(bar);

  var interval = setInterval(function () {
    var now = new Date().getTime();
    var distance = endDate - now;

    if (distance < 0) {
      clearInterval(interval);
      location.reload(); // PHP time management
      return;
    }

    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
    var minutes = Math.floor((distance / (1000 * 60)) % 60);
    var seconds = Math.floor((distance / 1000) % 60);

    bar.innerHTML =
      "⏳ Session : " +
      days + "d " + hours + "h " + minutes + "m " + seconds + "s";
  }, 1000);
});