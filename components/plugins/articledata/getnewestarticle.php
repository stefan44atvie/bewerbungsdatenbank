<?php 
   function getNewestArticle(mysqli $connect): ?array {
    $sql = "SELECT title, publish_date, publish_time, affiliate, short_intro, create_date 
            FROM articles 
            WHERE fk_astatus = 1 
            ORDER BY publish_date DESC 
            LIMIT 1";

    $res = mysqli_query($connect, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);

        // Kombinierter Zeitstempel
        $publish_datetime = $row['publish_date'] . ', ' . $row['publish_time'];

        return [
            'title' => $row['title'],
            'publish_date' => $row['publish_date'],
            'publish_time' => $row['publish_time'],
            'affiliate' => $row['affiliate'],
            'short_intro' => $row['short_intro'],
            'create_date' => $row['create_date'],
            'publish_datetime' => $publish_datetime
        ];
    }

    return null;
}
?>