<?php
/**
 * Footer Template
 *
 * This template is designed for the Sanctuary Watch website.
 * It includes three columns with links to various sections of the site,
 * social media links, and copyright information.
 */

defined( 'ABSPATH' ) || exit;
// $post_id = get_the_ID();
$instance_num =  get_post_meta(get_the_ID(), 'scene_location', true);
$instance_contact_person = get_post_meta($instance_num, 'instance_contact_person', true);
$instance_more_info = get_post_meta($instance_num, 'instance_more_info', true);

// echo $post_id;
?>
<script> 
let instance_contact_person =  <?php echo json_encode($instance_contact_person); ?>;
let instance_more_info =  <?php echo json_encode($instance_more_info); ?>;
let instance_num = <?php echo json_encode($instance_num); ?>;
console.log("ahahahahahahhahahaah");
console.log(instance_num);
console.log(instance_contact_person);
console.log(instance_more_info);

</script>

<footer class="site-footer" style="background-color: #03386c; color: white; padding: 32px 12px; font-size: 13px;">
    <div class="container" style="margin-left: 29%;">
        <div class="row" style="display: flex; flex-wrap: wrap; justify-content: space-between;">
            <div class="col-md-6" style="flex: 1; ">
                <h5 style="color: white; font-weight: bold; margin-bottom: 12px;">About the Sanctuary</h5>
                <a href="#" id="about-sanctuary-btn" style="
                    /* background-color: #008da8;  */
                    color: white; 
                    /* font-weight: bold;  */
                    padding: 6px 27x;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    margin-bottom: 12px;
                    font-size: 17px;
                    /* text-align: left; */
                    width: 26%;
                ">
                    Channel Islands National Marine Sanctuary
            </a>
            </div>
            <div class="col-md-6" style="flex: 1;">
                <h5 style="color: white; font-weight: bold; margin-bottom: 12px;">Contact</h5>
                <ul style="list-style: none; padding: 0;font-size: 17px;">
                <li class="contact-person">
                <a href="#" style="color: lightgrey; text-decoration: none;">
                    <strong><?php echo esc_html($instance_contact_person['instance_contact_name']); ?>, <?php echo esc_html($instance_contact_person['instance_contact_title']); ?></strong>
                    
                </a>
                </li>
                    <li><a href="mailto:<?php echo esc_attr($instance_contact_person['instance_contact_email']); ?>?subject=Inquiry%20from%20Website&body=Hello%2C%0A%0AI%20am%20contacting%20you%20regarding%20..." 
                    style="color: lightgrey; text-decoration: underline;">
                        <?php echo esc_html($instance_contact_person['instance_contact_email']); ?>
                    </a></li>
                    <!-- <li><a href="#" style="color: lightgrey; text-decoration: none;">916-817-4425</a></li> -->
                </ul>
            </div>
        </div>
        <!-- <div class="footer-base" style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 12px; border-top: 1px solid white;"> -->
            <!-- <div class="social-links">
                <a href="#" style="color: lightgrey; text-decoration: none; margin-right: 12px;">Blog</a>
                <a href="#" style="color: lightgrey; text-decoration: none; margin-right: 12px;">Facebook</a>
                <a href="#" style="color: lightgrey; text-decoration: none; margin-right: 12px;">Github</a>
                <a href="#" style="color: lightgrey; text-decoration: none; margin-right: 12px;">LinkedIn</a>
                <a href="#" style="color: lightgrey; text-decoration: none;">Instagram</a>
            </div> -->
            <!-- <div class="copyright" style="font-size: 11px;">
                © <?php echo date('Y'); ?> Sanctuary Watch
                <span style="display: block;">Protecting wildlife, preserving nature</span>
            </div> -->
        <!-- </div> -->
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>