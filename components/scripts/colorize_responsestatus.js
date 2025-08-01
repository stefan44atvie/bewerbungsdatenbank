const artStatus = document.getElementsByClassName("response_status");

const bgColorMap = {
  "Positiv (Ja/Interessiert": "green",
  "Negativ (Nein/Abgelehnt)": "red",
  "Neutral (Bestätigung per Mail)": "grey",
  "Ausstehend (Warten auf Entscheidung)": "orange",
};

for (let i = 0; i < artStatus.length; i++) {
  const text = artStatus[i].textContent.trim();
  const el = artStatus[i];
  el.style.backgroundColor = bgColorMap[text] || "gray";
}