<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>

<div id="body" class="body">
    <?php 
        for ($x = 0; $x < 100; $x++){
            echo "<br>";
        }
    ?>
</div>

<?php
get_footer();
?>