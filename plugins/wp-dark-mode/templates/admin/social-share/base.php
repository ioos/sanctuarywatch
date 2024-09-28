<?php
/**
 * WP Dark Mode - Social Share Base
 * Load all social share templates
 *
 * @package WP_DARK_MODE
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit();
?>

<div class="wrap wpdarkmode-section wpdarkmode-social-share" id="wp-dark-mode-social-share" x-data="SocialShare" tabindex="0">

	<!-- header  -->
	<h3 class="text-xl font-medium mb-4"><?php esc_html_e( 'Social Share (Inline Button)', 'wp-dark-mode' ); ?></h3>

	<!-- body  -->
	<div class="flex flex-col gap-2 border border-gray-300 bg-white relative">

		<!-- Save toast  -->
		<template x-if="state.saved === true"> 
			<div x-show="state.saved" class="main-content-toast fixed right-3 top-14 flex items-center justify-center px-3 py-2.5 gap-2 bg-white z-[999999] rounded-xl shadow-xl">
				<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M10.9996 21.3996C16.7434 21.3996 21.3996 16.7434 21.3996 10.9996C21.3996 5.25585 16.7434 0.599609 10.9996 0.599609C5.25585 0.599609 0.599609 5.25585 0.599609 10.9996C0.599609 16.7434 5.25585 21.3996 10.9996 21.3996ZM15.8188 9.31885C16.3265 8.81117 16.3265 7.98805 15.8188 7.48037C15.3112 6.97269 14.4881 6.97269 13.9804 7.48037L9.69961 11.7611L8.01885 10.0804C7.51117 9.57269 6.68805 9.57269 6.18037 10.0804C5.67269 10.5881 5.67269 11.4112 6.18037 11.9188L8.78037 14.5188C9.28805 15.0265 10.1112 15.0265 10.6188 14.5188L15.8188 9.31885Z" fill="#34D399"/>
				</svg>
				
				<span class="text-sm text-[#0F172A]"><?php esc_html_e( 'Saved Successfully', 'wp-dark-mode' ); ?></span>

				<a @click.prevent="state.saved = false" class="relative right-auto top-auto z-10 w-6 h-6 inline-flex justify-center items-center cursor-pointer text-gray-400 hover:text-gray-700 transition outline-none" v-bind="$attrs">
					<svg class="w-2.5" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M0.292893 0.292893C0.683417 -0.0976311 1.31658 -0.0976311 1.70711 0.292893L6 4.58579L10.2929 0.292893C10.6834 -0.0976311 11.3166 -0.0976311 11.7071 0.292893C12.0976 0.683417 12.0976 1.31658 11.7071 1.70711L7.41421 6L11.7071 10.2929C12.0976 10.6834 12.0976 11.3166 11.7071 11.7071C11.3166 12.0976 10.6834 12.0976 10.2929 11.7071L6 7.41421L1.70711 11.7071C1.31658 12.0976 0.683417 12.0976 0.292893 11.7071C-0.0976311 11.3166 -0.0976311 10.6834 0.292893 10.2929L4.58579 6L0.292893 1.70711C-0.0976311 1.31658 -0.0976311 0.683417 0.292893 0.292893Z" fill="currentColor"/>
					</svg>
				</a>
			</div>
		</template>

		<?php
		// Get social share loader.
		$this->render_template( 'admin/social-share/loader' );

		// Get social share sidebar.
		$this->render_template( 'admin/social-share/sidebar' );
		?>


		<!-- content  -->
		<section class="w-full flex flex-col md:flex-row">

			

			<div class="_content-section">

				<?php
				// Get social share channels.
				$this->render_template( 'admin/social-share/channels' );

				// Get social share customization.
				$this->render_template( 'admin/social-share/customization' );
				?>

			</div>

			<?php
			// Get social share preview.
			$this->render_template( 'admin/social-share/preview' );
			?>

		</section>

		<!-- content end  -->
	</div>

	<!-- footer  -->

	<?php do_action( 'wpdarkmode_social_share_footer' ); ?>

</div>