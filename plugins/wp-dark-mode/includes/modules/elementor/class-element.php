<?php
/**
 * Elementor Controls Inits for WP Dark Mode
 * Loads all the required files for Elementor Controls
 *
 * @version 1.0.0
 * @package WP Dark Mode
 */

// Namespace.
namespace WP_Dark_Mode\Module\Elementor;

// Exit if directly called.
defined( 'ABSPATH' ) || exit();

// Check class is already exists.
if ( ! class_exists( 'Element' ) ) {
	/**
	 * Loads Elementor Controls Inits for WP Dark Mode
	 *
	 * @version 1.0.0
	 * @package WP Dark Mode
	 */
	class Element extends \WP_Dark_Mode\Base {

		// Use trait.
		use \WP_Dark_Mode\Traits\Utility;

		/**
		 * Actions
		 *
		 * Calling method
		 *
		 * @return void
		 * @version 1.0.0
		 */
		public function actions() {
			add_action( 'elementor/widgets/register', [ $this, 'register_widget' ] );
			add_action( 'elementor/controls/register', [ $this, 'register_control' ], 11 );
			add_action ( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		}

		/**
		 * Register image choose control
		 *
		 * @param \Elementor\Controls_Manager $controls_manager Elementor controls manager.
		 *
		 * @return void
		 * @version 1.0.0
		 */
		public function register_control( $controls_manager ) {
			include __DIR__ . '/controls/class-elementor-control-switch.php';
			$controls_manager->register( new \WP_Dark_Mode\Module\Elementor\Controls\DarkModeSwitch() );
		}

		/**
		 * Register widget
		 */
		public function register_widget() {
			include __DIR__ . '/widgets/class-elementor-widget.php';
			\Elementor\Plugin::instance()->widgets_manager->register( new \WP_Dark_Mode\Module\Elementor\DarkModeWidget() );
		}

		/**
		 * Enqueue scripts
		 */
		public function enqueue_scripts() {

			// Bail, if ultimate version is active.
			if ( $this->is_ultimate() ) {
				return;
			}

			require_once WP_DARK_MODE_TEMPLATE . 'admin/upgrade-popup.php';
			?>
<script>
	(() => {
			
		document.addEventListener('click', (e) => {
			const closest = e.target?.closest( '.wp-dark-mode-switch-style-elementor' )
			// Bail if closest is null.
			if ( ! closest ) {
				return;
			}

			const select = closest.querySelector( 'select' );

			// On change select.
			const onChange = (e) => {
				const value = e.target.value;
				
				// if value is more than 3
				if ( value > 3 ) {
					window.WPDarkModePromo.show()
					e.target.value = 3;
				} 

				// Remove event listener.
				select.removeEventListener( 'change', onChange );
			}
			select.addEventListener( 'change', onChange );
		});

	})()
</script>
			<?php
		}
	}

	// Instantiate the class.
	Element::init();
}
