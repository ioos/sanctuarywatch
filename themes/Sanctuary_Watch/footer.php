<?php
defined('ABSPATH') || exit;
$instance_num =  get_post_meta(get_the_ID(), 'scene_location', true);
$instance_footer_about = get_post_meta($instance_num, 'instance_footer', true);

?>
<script> 
let instance_footer_about = <?php echo json_encode($instance_footer_about); ?>;
console.log("footer stuff");
console.log(instance_footer_about);

</script>

<footer class="site-footer" style="background-color: #03386c; color: white; padding: 32px 12px; font-size: 13px;">
    <div class="container" style="margin: 0 auto; max-width: 1200px; padding: 0 15px;">
        <div class="row" style="display: flex; flex-wrap: wrap; margin: 0 -15px;">
            <div class="col-md-4" style="flex: 0 0 33.333%; padding: 0 15px;">
                <h5 style="color: white; font-weight: bold; margin-bottom: 12px;">About</h5>
                <div id="about-sanctuary-btn" style=" 
                    color: white; 
                    padding: 6px 0;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    margin-bottom: 12px;
                    font-size: 17px;
                    display: inline-block;"> 
                    <?php echo $instance_footer_about['instance_footer_about']; ?>
                </div>
            </div>
            
            <div class="col-md-4" style="flex: 0 0 33.333%; padding: 0 15px;">
                <h5 style="color: white; font-weight: bold; margin-bottom: 12px;">Contact</h5>
                <div id="about-sanctuary-btn" style=" 
                    color: white; 
                    padding: 6px 0;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    margin-bottom: 12px;
                    font-size: 17px;
                    display: inline-block;"> 
                    <?php echo $instance_footer_about['instance_footer_contact']; ?>
                </div>
            </div>

            <div class="col-md-4" style="flex: 0 0 33.333%; padding: 0 15px;">
                <h5 style="color: white; font-weight: bold; margin-bottom: 12px;">Reports</h5>
                <div id="about-sanctuary-btn" style=" 
                    color: white; 
                    padding: 6px 0;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    margin-bottom: 12px;
                    font-size: 17px;
                    display: inline-block;"> 
                    <?php echo $instance_footer_about['instance_footer_reports']; ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>