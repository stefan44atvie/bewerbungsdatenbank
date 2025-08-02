(function () {
  const artStatus = document.getElementsByClassName("applic_status");

  const bgColorMap = {
    "Beworben": "orange",
    "in Prüfung": "lightseagreen",
    "Vorstellungsgespräch geplant": "lightblue",
    "Vorstellungsgespräch abgeschlossen": "lightgreen",
    "Abgelehnt": "red",
    "Angenommen": "green",
    "Zurückgezogen": "salmon",
  };

  for (let i = 0; i < artStatus.length; i++) {
    const text = artStatus[i].textContent.trim();
    const el = artStatus[i];
    el.style.backgroundColor = bgColorMap[text] || "gray";
  }
})();