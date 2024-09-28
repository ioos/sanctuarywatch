<?php
/**
 * Promotional Popup (Legacy)
 * Shows when the user is NOT using the ultimate version of WP Dark Mode.
 *
 * @package WP Dark Mode
 * @since 1.0
 */
defined( 'ABSPATH' ) || exit;


// Count down time.
$countdown_timer = get_transient( 'wp_dark_mode_promo_countdown_timer' );


if ( empty( $countdown_timer ) || $countdown_timer < time() ) {
	$countdown_timer = strtotime( '+ 14 hours' );
	set_transient( 'wp_dark_mode_promo_countdown_timer', $countdown_timer, 14 * HOUR_IN_SECONDS );
}

// Formatted data.
$data = [
	'counter_time' => $countdown_timer,
	'discount'     => 35,
];

$countdown_time = [
	'year'   => gmdate( 'Y', $countdown_timer ),
	'month'  => gmdate( 'm', $countdown_timer ),
	'day'    => gmdate( 'd', $countdown_timer ),
	'hour'   => gmdate( 'H', $countdown_timer ),
	'minute' => gmdate( 'i', $countdown_timer ),
];


?>

<div class="wp-dark-mode-promo hidden <?php echo ! empty( $class ) ? esc_attr( $class ) : ''; ?>">
	<div class="wp-dark-mode-promo-inner">

		<span class="close-promo">&times;</span>

		<img src="<?php echo esc_url( WP_DARK_MODE_ASSETS ) . '/images/gift-box.svg'; ?>" class="promo-img">

		<?php
		echo wp_sprintf( '<h3 class="promo-title">%s</h3>', esc_html__( 'Unlock all the features', 'wp-dark-mode' ) );

		echo wp_sprintf( '<div class="discount"> <span class="discount-special">SPECIAL</span> <span class="discount-text">%s%s OFF</span></div>', esc_html( $data['discount'] ), '%' );
		?>

		<div class="wp-dark-mode-timer">
			<div class="days">
				<span data-days>00</span>
				<span><?php esc_html_e( 'DAYS', 'wp-dark-mode' ); ?></span>
			</div>
			<div class="hours">
				<span data-hours>00</span>
				<span><?php esc_html_e( 'HOURS', 'wp-dark-mode' ); ?></span>
			</div>
			<div class="minutes">
				<span data-minutes>00</span>
				<span><?php esc_html_e( 'MINUTES', 'wp-dark-mode' ); ?></span>
			</div>
			<div class="seconds">
				<span data-seconds>00</span>
				<span><?php esc_html_e( 'SECONDS', 'wp-dark-mode' ); ?></span>
			</div>
		</div>

		<a href="https://go.wppool.dev/LaSV" target="_blank"><?php echo wp_sprintf( 'Claim %s%s Discount', esc_html( $data['discount'] ), '%' ); ?></a>
	</div>

	<style>
		.wp-dark-mode-promo {
			opacity: .95;
		}

		.wp-dark-mode-promo-inner {
			animation: wp-dark-mode-promo .01s ease-in-out forwards;
		}
		@keyframes wp-dark-mode-promo {
			0% {
				opacity: 0;
				transform: scale(0.8);
			}

			100% {
				opacity: .95;
				transform: scale(1);
			}
		}


		.wp-dark-mode-timer {
			text-align: center;
			padding: 0 0 10px;
		}

		.wp-dark-mode-timer > div {
			display: inline-block;
			margin: 0 14px;

			width: 47px;
			background: url(<?php echo esc_url( WP_DARK_MODE_ASSETS ) . '/images/timer.svg'; ?>) no-repeat 0 0;
			background-size: contain;
			line-height: 40px;
		}

		.wp-dark-mode-timer > div > span:first-child {
			font-size: 28px;
			color: #fff;
			height: 47px;
			margin: 0 0 2px;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.wp-dark-mode-timer > div > span:last-child {
			font-family: Arial, serif;
			font-size: 12px;
			text-transform: uppercase;
			color: #fff;
		}

		.wp-dark-mode-promo-inner .discount {
			position: relative;
			margin: 45px 0 15px;
		}
	</style>


	<script>
	(() => {
		window.WPDarkModePromo = {
			el (selector) {
				return document.querySelector(selector) || null;
			},
			get container() {
				return document.querySelector('.wp-dark-mode-promo') || null;
			},

			get close() {
				return document.querySelector('.wp-dark-mode-promo .close-promo') || null;
			},
			startCountdown () {
				const THIS = this;
				const countDownDate = new Date('<?php echo esc_html( $countdown_time['year'] ); ?>-<?php echo esc_html( $countdown_time['month'] ); ?>-<?php echo esc_html( $countdown_time['day'] ); ?> <?php echo esc_html( $countdown_time['hour'] ); ?>:<?php echo esc_html( $countdown_time['minute'] ); ?>:00').getTime();
				
				const el = s => document.querySelector(s);

				const x = setInterval( function () {
					const now = new Date().getTime();
					const distance = countDownDate - now;

					const days = Math.floor(distance / (1000 * 60 * 60 * 24));
					const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
					const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
					const seconds = Math.floor((distance % (1000 * 60)) / 1000);

					['days', 'hours', 'minutes', 'seconds'].forEach( unit => {
						const el = document.querySelector(`[data-${unit}]`);
						const value = eval(unit);

						if( el === null ) {
							return;
						}

						if (value < 10) {
							el.innerHTML = `0${value}`;
						} else {
							el.innerHTML = value;
						}
					});

					if (distance < 0) {
						clearInterval(x);
						document.querySelector('.wp-dark-mode-timer')?.innerHTML('<span class="expired">EXPIRED</span>');							}
				}, 1000);

			},
			show() {
				this.container?.classList.remove('hidden');

				this.startCountdown();
			},
			hide() {
				this.container?.classList.add('hidden');
			},
			events(){
				// On click close button
				this.close?.addEventListener('click', (e) => {
					e.preventDefault();
					e.stopPropagation();
					this.hide();
				});

				// On click outside
				this.container?.addEventListener('click', e => {
					if (e.target !== this.container) {
						return;
					}

					e.preventDefault();
					e.stopPropagation();
					this.hide();
				});

				// On press escape
				document.addEventListener('keydown', e => {
					if (e.key === 'Escape') {
						e.preventDefault();
						e.stopPropagation();
						this.hide();
					}
				});

				// On click .wp-dark-mode-locked class
				document.addEventListener('click', e => {
					// Has class wp-dark-mode-locked or attribute data-wp-dark-mode-locked for current or parent element
					if ( e.target.closest('.wp-dark-mode-locked') || e.target.closest('[data-wp-dark-mode-locked="true"]') ) {
						e.preventDefault();
						e.stopPropagation();
						this.show();
					}
				});
			}
		}

		// Events
		document.addEventListener('DOMContentLoaded', () => {
			window.WPDarkModePromo.events();
		});
	})()
	</script>

</div>