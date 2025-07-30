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

    if ($anzahl_antworten > 0) {
        $erfolgsquote = ($anzahl_positiveAntworten / $anzahl_antworten) * 100;
    } else {
        $erfolgsquote = 0;
    }

?>