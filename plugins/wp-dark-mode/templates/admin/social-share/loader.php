<?php
/**
 * WP Dark Mode - Social Share Loader
 *
 * @package WP_DARK_MODE
 * @since 2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit();
?>
<div id="wpdm-social-share-loader" class="absolute left-0 top-0 w-full h-full z-50 bg-white text-slate-600 p-4">
	<div class="flex flex-col gap-2 ">
		<?php for ( $i = 0; $i < 10; $i++ ) { ?>
			<div class="flex items-center gap-2">
				<?php
				$rand = wp_rand( 2, 4 );
				for ( $j = 0; $j < $rand; $j++ ) {
					?>
					<div class="h-8 w-full bg-slate-200 rounded-md"></div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</div>