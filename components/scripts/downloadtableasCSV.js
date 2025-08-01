function downloadTableAsCSV(tableId, filename) {
  const table = document.getElementById(tableId);
  if (!table) {
    alert('Tabelle nicht gefunden: ' + tableId);
    return;
  }

  let csv = [];
  for (let row of table.rows) {
    let cells = Array.from(row.cells).map(cell =>
      '"' + cell.textContent.trim().replace(/"/g, '""') + '"'
    );
    csv.push(cells.join(','));
  }

  const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);

  const link = document.createElement("a");
  link.href = url;
  link.download = filename;
  link.style.display = "none";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}

// Alles erst nach DOM laden
document.addEventListener('DOMContentLoaded', function () {
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('button[data-table-id]');
    if (btn) {
      const tableId = btn.getAttribute('data-table-id');
      const filename = btn.getAttribute('data-filename') || 'tabelle.csv';
      downloadTableAsCSV(tableId, filename);
    }
  });
});