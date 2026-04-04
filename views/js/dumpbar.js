document.addEventListener("DOMContentLoaded", function () {
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

  bar.innerHTML = "⏳ Session expired : only SuperAdmin can access this shop.";
});