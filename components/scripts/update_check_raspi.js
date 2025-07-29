document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById("update-btn");
    const modalBody = document.getElementById('modal-body');

    if (!btn || !modalBody) {
        console.error("Ein oder mehrere benötigte HTML-Elemente wurden nicht gefunden!");
        return;
    }

    const updateModalElement = document.getElementById('updateModal');
    const updateModal = new bootstrap.Modal(updateModalElement);

    btn.addEventListener("click", function () {
        const projektname = btn.getAttribute("data-projektname");
        const aktuelleVersion = btn.getAttribute("data-version");

        modalBody.innerHTML = "🔄 Überprüfe Update... Bitte warten.";
        updateModal.show();

        fetch(`/bewerbungsdatenbank/components/api/proxy_check_update.php?projekt=${encodeURIComponent(projektname)}&current_version=${encodeURIComponent(aktuelleVersion)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Fehler beim Abrufen der Updates: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Antwort von API:", data);

                if (data.success && data.data?.version && data.data.version !== aktuelleVersion) {
                    const neueVersion = data.data.version;
                    const downloadUrl = data.data.download_url;

                    modalBody.innerHTML = `⬇️ Update auf Version ${neueVersion} wird gestartet...`;

                    const installerUrl = `/bewerbungsdatenbank/admin/inc/update_installer.php?projektname=${encodeURIComponent(projektname)}&version=${encodeURIComponent(neueVersion)}&download_url=${encodeURIComponent(downloadUrl)}&install_dir=${encodeURIComponent(location.origin + '/digitaleseele2025')}`;
                    fetch(installerUrl)
                        .then(r => r.text())
                        .then(output => {
                            modalBody.innerHTML = `<pre>${output}</pre>`;

                            if (output.includes("Update erfolgreich")) {
                                const reloadBtn = document.getElementById('reload-btn');
                                if (reloadBtn) {
                                    reloadBtn.style.display = 'inline-block';
                                    reloadBtn.addEventListener('click', () => {
                                        location.reload();
                                    });
                                }
                            }
                        })
                        .catch(err => {
                            modalBody.innerHTML = `❌ Fehler beim Ausführen des Updates: ${err}`;
                        });
                } else if (data.success && !data.data) {
                    modalBody.innerHTML = "✅ Deine Version ist bereits aktuell. Kein Update nötig.";
                } else {
                    modalBody.innerHTML = "⚠️ Keine Update-Informationen gefunden oder Serverantwort unklar.";
                }
            })
            .catch(error => {
                modalBody.innerHTML = "❌ Fehler bei der Updateprüfung: " + error;
            });
    });
});