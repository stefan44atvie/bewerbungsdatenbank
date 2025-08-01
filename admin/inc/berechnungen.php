<?php 

    $sql_countBewerbungen = "select count(id) as Anzahl from bewerbungen";
    $resCB = mysqli_query($connect,$sql_countBewerbungen);
    $rowCB = mysqli_fetch_assoc($resCB);
    $anzahl_bewerbungen = $rowCB['Anzahl'];

    $sql_countAntworten = "select count(id) as Anzahl from firmen_antworten";
    $resFA = mysqli_query($connect,$sql_countAntworten);
    $rowFA = mysqli_fetch_assoc($resFA);
    $anzahl_antworten = $rowFA['Anzahl'];

    $sql_countPositAntworten = "select count(id) as Anzahl from firmen_antworten where antwort_ergebnis = 'Positiv (Ja/Interessiert)'";
    $resCPA = mysqli_query($connect,$sql_countPositAntworten);
    $rowCPA = mysqli_fetch_assoc($resCPA);
    $anzahl_positiveAntworten = $rowCPA['Anzahl'];

    $sql_countNegAntworten = "select count(id) as Anzahl from firmen_antworten where antwort_ergebnis = 'Negativ (Nein/Abgelehnt)'";
    $resCNA = mysqli_query($connect,$sql_countNegAntworten);
    $rowCNA = mysqli_fetch_assoc($resCNA);
    $anzahl_negativeAntworten = $rowCNA['Anzahl'];

    $sql_countAusstehendAntworten = "select count(id) as Anzahl from firmen_antworten where antwort_ergebnis = 'Ausstehend (Warten auf Entscheidung)'";
    $resCAA = mysqli_query($connect,$sql_countAusstehendAntworten);
    $rowCAA = mysqli_fetch_assoc($resCAA);
    $anzahl_ausstehendeAntworten = $rowCAA['Anzahl'];

    $sql_countNeutralAntworten = "select count(id) as Anzahl from firmen_antworten where antwort_ergebnis = 'Neutral (Bestätigung per Mail)'";
    $resCNtA = mysqli_query($connect,$sql_countNeutralAntworten);
    $rowCNtA = mysqli_fetch_assoc($resCNtA);
    $anzahl_neutraleAntworten = $rowCNtA['Anzahl'];

    $antworten_gesamt = $anzahl_negativeAntworten + $anzahl_positiveAntworten;
    if ($antworten_gesamt > 0) {
        $erfolgsquote = ($anzahl_positiveAntworten / $anzahl_antworten) * 100;
        $erfolgsquote = round($erfolgsquote, 2); // auf 2 Nachkommastellen runden
    } else {
        $erfolgsquote = 0;
    }

    /* ---- Berechnung der aktuellen woche ---- */ 
    /* ======================================== */
    $heute = new DateTime(); // aktuelles Datum

    // Wochentag (1 = Montag, 7 = Sonntag)
    $wochentag = $heute->format('N');

    // Montag dieser Woche berechnen
    $montag = clone $heute;
    $montag->modify('-' . ($wochentag - 1) . ' days');

    // Sonntag dieser Woche berechnen
    $sonntag = clone $heute;
    $sonntag->modify('+' . (7 - $wochentag) . ' days');

    // Ausgabe
    // echo "Aktuelle Woche: " . $montag->format('d.m.Y') . " – " . $sonntag->format('d.m.Y');
    /* ---- Berechnung der aktuellen woche ---- */ 

?>