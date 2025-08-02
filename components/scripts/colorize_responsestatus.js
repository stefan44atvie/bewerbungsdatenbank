(function () {
  const responseStatus = document.getElementsByClassName("response_status");

  const bgColorMap = {
    "Positiv (Ja/Interessiert)": "green",
    "Negativ (Nein/Abgelehnt)": "red",
    "Neutral (Bestätigung per Mail)": "grey",
    "Ausstehend (Warten auf Entscheidung)": "orange"
  };

  for (let i = 0; i < responseStatus.length; i++) {
    const text = responseStatus[i].textContent.trim();
    console.log(`Text [${i}]:`, JSON.stringify(text));
    const el = responseStatus[i];
    el.style.backgroundColor = bgColorMap[text] || "gray";
  }
})();