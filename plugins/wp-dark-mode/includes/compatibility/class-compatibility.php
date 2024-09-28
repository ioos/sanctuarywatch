<?php
/**
 * Compatibility Class for WP Dark Mode
 *
 * @package WP_Dark_Mode
 */

// Namespace.
namespace WP_Dark_Mode\Compatibility;

// Theme Support.
require_once WP_DARK_MODE_INCLUDES . 'compatibility/class-themes.php';

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'Compatibility' ) ) {
    /**
     * Compatibility Class for WP Dark Mode
     */
    class Compatibility extends \WP_Dark_Mode\Base {

        /**
         * Return the slug of the theme.
         *
         * @return string
         */
        public function get_theme_slug() {
            // Return site theme.
            return wp_get_theme()->get_stylesheet();
        }

        /**
         * Get supported theme.
         *
         * @return string
         */
        public function get_supported_theme() {

            $themes_object = \WP_DARK_MODE\Compatibility\Themes::get_instance();

            $themes = [
                'twentytwenty' => [ $themes_object, 'twentytwenty' ],
                'twentytwentyone' => [ $themes_object, 'twentytwentyone' ],
                'twentytwentytwo' => [ $themes_object, 'twentytwentytwo' ],
                'twentytwentythree' => [ $themes_object, 'twentytwentythree' ],
                'twentytwentyfour' => [ $themes_object, 'twentytwentyfour' ],
                'astra' => [ $themes_object, 'astra' ],
                'generatepress' => [ $themes_object, 'generatepress' ],
                'oceanwp' => [ $themes_object, 'oceanwp' ],
                'neve' => [ $themes_object, 'neve' ],
                'hello-elementor' => [ $themes_object, 'hello_elementor' ],
                'storefront' => [ $themes_object, 'storefront' ],
                'flatsome' => [ $themes_object, 'flatsome' ],
                'avada' => [ $themes_object, 'avada' ],
                'enfold' => [ $themes_object, 'enfold' ],
                'divi' => [ $themes_object, 'divi' ],
                // Additional popular themes
                'betheme' => [ $themes_object, 'betheme' ],
                'beonepage' => [ $themes_object, 'beonepage' ],
                'newspaper' => [ $themes_object, 'newspaper' ],
                'jupiter' => [ $themes_object, 'jupiter' ],
                'soledad' => [ $themes_object, 'soledad' ],
                'salient' => [ $themes_object, 'salient' ],
                'uncode' => [ $themes_object, 'uncode' ],
                'bridge' => [ $themes_object, 'bridge' ],
                'x' => [ $themes_object, 'x' ],
                'sahifa' => [ $themes_object, 'sahifa' ],
                'the7' => [ $themes_object, 'the7' ],
                'spectra-one' => [ $themes_object, 'spectra_one' ],
                'virtue' => [ $themes_object, 'virtue' ],
            ];

            return apply_filters( 'wp_dark_mode_supported_themes', $themes );
        }

        /**
         * Check if theme is supported.
         *
         * @return bool
         */
        public function is_theme_supported() {
            $theme = $this->get_theme_slug();
            $themes = $this->get_supported_theme();

            return isset( $themes[ $theme ] );
        }

        /**
         * Get theme.
         *
         * @return string
         */
        public function get_theme() {
            $theme = $this->get_theme_slug();
            $themes = $this->get_supported_theme();

            return $themes[ $theme ];
        }


        /**
         * Actions
         *
         * @return void
         */
        public function actions() {

            // Get theme slug.
            $theme_slug = $this->get_theme_slug();

            // Theme actions built-in.
            if ( $this->is_theme_supported() ) {
                $theme = $this->get_theme();

                // If method exists, call it.
                if ( method_exists( $theme[0], $theme[1] ) ) {
                    try {
                        call_user_func( $theme );
                    } catch ( \Exception $e ) { // phpcs:ignore
                        // Do nothing.
                    }
                }
            }

            // Enqueue styles if file exists.
            if ( file_exists( WP_DARK_MODE_PATH . 'assets/css/themes/' . $theme_slug . '.css' ) ) {
                add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_theme_styles' ], 9999 );
            }

            // Do action.
            do_action( 'wp_dark_mode_theme_supports', $theme_slug );
        }

        /**
         * Enqueue theme styles
         *
         * @return void
         */
        public function enqueue_theme_styles() {
            // Get theme.
            $theme_slug = $this->get_theme_slug();

            // Enqueue styles.
            wp_enqueue_style( 'wp-dark-mode-theme-' . $theme_slug, ( WP_DARK_MODE_ASSETS . 'css/themes/' . $theme_slug . '.css' ), [], WP_DARK_MODE_VERSION );
        }
    }

    // Initialize the class.
    Compatibility::init();
}
