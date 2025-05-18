<?php
defined('ABSPATH') || exit;
$instance_num =  get_post_meta(get_the_ID(), 'scene_location', true);
$instance_footer_about = get_post_meta($instance_num, 'instance_footer', true);

?>
<script> 
let instance_footer_about = <?php echo json_encode($instance_footer_about); ?>;

</script>

<footer class="site-footer" style="background-color: #03386c; color: white; padding: 32px 12px; font-size: 13px;">
    <div class="container" style="margin: 0 auto; max-width: 1200px; padding: 0 15px;">
        <div class="row">
            <div class="col-12 col-sm-4 mb-4 mb-sm-0">
                <h5 class="text-white mb-3">About</h5>
                <div class="footer_component"> 
                    <?php echo $instance_footer_about['instance_footer_about']; ?>
                </div>
            </div>
            
            <div class="col-12 col-sm-4 mb-4 mb-sm-0">
                <h5 class="text-white mb-3">Contact</h5>
                <div class="footer_component"> 
                    <?php echo $instance_footer_about['instance_footer_contact']; ?>
                </div>
            </div>

            <div class="col-12 col-sm-4 mb-4 mb-sm-0">
                <h5 class="text-white mb-3">Reports</h5>
                <div class="footer_component" > 
                    <?php echo $instance_footer_about['instance_footer_reports']; ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>