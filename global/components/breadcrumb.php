<?php
require_once('../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $getRewrittenUrl = $_POST['getRewrittenUrl'];
    if (isset($_GET['type'])) {
        $type = ucwords($_GET['type']);
    } else {
        $type = '';
    }
?>
    <nav class="breadcrumb">
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#"><i class="fas fa-folder"></i> <?= $getRewrittenUrl ?></a></li>
            <?php if (isset($_GET['type'])) { ?>
                <li><a href="#"><i class="fas fa-folder-open"></i> <?= $type . $getRewrittenUrl ?></a></li>
            <?php } ?>
        </ul>
    </nav>
<?php } ?>