<div class="bwd_headermenu_area sticky-top">
    <div id="projektname">
        <a class="projektname_text">Bewerbungsdatenbank</a>
    </div>
    <div>
        <a class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
    </div>
    <div>
        <a class="<?php echo ($current_page == 'bewerbungen.php') ? 'active' : ''; ?>" href="bewerbungen.php">Bewerbungen</a>
    </div>
    <div>
        <a class="disabled <?php echo ($current_page == 'analysen.php') ? 'active' : ''; ?>" href="analysen.php">Analysen</a>
    </div>
    <div>
        <a class="<?php echo ($current_page == 'antworten.php') ? 'active' : ''; ?>" href="antworten.php">Antworten</a>
    </div>
    <div>
        <a class="disabled <?php echo ($current_page == 'erinnerungen.php') ? 'active' : ''; ?>" href="erinnerungen.php">Erinnerungen</a>
    </div>
    <div>
        <a class="disabled <?php echo ($current_page == 'termine.php') ? 'active' : ''; ?>" href="termine.php">Termine</a>
    </div>

    <div>
        <a class="disabled <?php echo ($current_page == 'statistiken.php') ? 'active' : ''; ?>" href="statistiken.php">Statistiken</a>
    </div>
    <div>
        <a class="<?php echo ($current_page == 'logout.php?logout') ? 'active' : ''; ?>" href="inc/logout.php?logout">Logout</a>
    </div>
</div>