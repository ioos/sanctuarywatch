<!-- Social Share Buttons  -->
<?php
/**
 * Social Share Buttons Template
 *
 * @package wp_dark_mode
 * @since 2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit();

global $wpdb;
$counters = $wpdb->get_results( $wpdb->prepare( "SELECT count(ID) as count, channel FROM {$wpdb->prefix}wpdm_social_shares WHERE post_id = %d OR url = %s group by channel", get_the_ID(), get_permalink() ), ARRAY_A ); // phpcs:ignore

$total_shares = array_sum( array_column( $counters, 'count' ) );


/**
 * Social channels.
 *
 * @var object $social_share Social Share object
 */
$channels = false;

if ( $social_share && $social_share->channels ) {
	$channels = array_map(
		function ( $channel ) use ( $counters, $social_share ) { // phpcs:ignore Universal.FunctionDeclarations.NoLongClosures.ExceedsMaximum
			$mother_channel = array_filter(
				$social_share->all_channels,
				function ( $item ) use ( $channel ) {
					return $item['id'] === $channel['id'];
				}
			);

			$svg = array_values( $mother_channel )[0]['svg'];

			$channel['svg'] = ! empty( $svg ) ? $svg : '';

			$count = array_values(
				array_filter(
					$counters,
					function ( $counter ) use ( $channel ) {
						return $counter['channel'] === $channel['id'];
					}
				)
			);

			if ( 'both' === $social_share->button_label || 'share_count' === $social_share->button_label ) {
				$count = isset( $count[0]['count'] ) && $count[0]['count'] > 0 ? $count[0]['count'] : 0;

				$channel['count'] = apply_filters( 'wpdm_social_share_count', $count );
			}

			return $channel;
		},
		$social_share->channels
	);
}

/**
 * Visible channels
 */

$channel_visibility = intval( $social_share->channel_visibility );

$visible_channels = $channels;

if ( $channel_visibility > 0 ) {
	$visible_channels = array_slice( $visible_channels, 0, $channel_visibility );
}


/***
 * Right after the social share buttons
 */
do_action( 'before_wpdm_social_share' );
?>

<section class="
<?php
echo wp_sprintf('_social-share-container _align-%s',
	esc_attr( $social_share->button_alignment )
	);
?>
	 <?php
		if ( $social_share->hide_button_on['mobile'] && $social_share->hide_button_on['desktop'] ) {
			echo '_hidden';
		} elseif ( $social_share->hide_button_on['mobile'] ) {
			echo '_hide-on-mobile';
		} elseif ( $social_share->hide_button_on['desktop'] ) {
			echo '_hide-on-desktop';
		}
		?>
	 ">

	<!-- Share via text  -->
	<?php if ( ! empty( $social_share->share_via_label ) ) : ?>
	<span class="_share-label wp-dark-mode-ignore"><?php echo esc_html( $social_share->share_via_label ); ?></span>
	<?php endif; ?>

	<!-- channel container  -->
	<div class="_channels-container wp-dark-mode-ignore _channel-animation-4
		<?php
			echo '_channel-template-' . esc_html( $social_share->button_template );
			echo ' ';
			echo ( wp_validate_boolean( $social_share->button_spacing ) ) ? '_spaced' : '_no-spaced';
			echo ' ';
			echo ( '_' . esc_html( isset( $social_share->button_shape ) ? $social_share->button_shape : '' ) );
			echo ' ';
			echo 'both' === $social_share->button_label ? '_both-label' : '';
			echo ' ';
		?>
		">

		<!-- Share count  -->
		<?php if ( wp_validate_boolean( $social_share->show_total_share_count ) && $social_share->minimum_share_count <= $total_shares ) : ?>
		<div class="_total-share wp-dark-mode-ignore">
			<div class="_total-share-count wp-dark-mode-ignore">
				<span><?php echo esc_html( apply_filters( 'wpdm_social_share_count', intval( $total_shares ) ) ); ?></span>
				<span><?php echo esc_html( $social_share->shares_label ); ?></span>
			</div>
		</div>
		<?php endif; ?>

		<!-- social channels  -->
		<div class="_channels wp-dark-mode-ignore">
		<!-- Share Icons  -->
		<?php
		if ( $visible_channels && count( $visible_channels ) > 0 ) {
			foreach ( $visible_channels as $channel ) {
				?>
				<div class="wpdm-social-share-button wp-dark-mode-ignore _channel _icon-<?php echo esc_html( $channel['id'] ); ?> 
					<?php echo wp_validate_boolean( $channel['visibility']['mobile'] ) ? '' : '_hide-on-mobile'; ?> 
					<?php echo wp_validate_boolean( $channel['visibility']['desktop'] ) ? '' : '_hide-on-desktop'; ?>" 
					data-channel="<?php echo esc_html( $channel['id'] ); ?>">

					<!-- channel icon  -->
					<span class="_channel-icon wp-dark-mode-ignore <?php echo 'none' === $social_share->button_label ? '_channel-icon-full' : ''; ?>">
						<span>
							<?php echo wp_kses( $channel['svg'], $social_share->get_kses_extended_ruleset ); ?>
						</span>
					</span>

					<!-- channel label  -->
					<?php if ( 'none' !== $social_share->button_label ) : ?>
					<div class="_channel-label">
						<!-- share count per channel -->
						<?php if ( 'share_count' !== $social_share->button_label ) : ?>
						<span class="_channel-name wp-dark-mode-ignore">
							<span>
								<?php echo esc_html( $channel['name'] ); ?>
							</span>
						</span>
						<?php endif; ?>

						<!-- channel label  -->
						<?php if ( isset( $channel['count'] ) && 'channel_label' !== $social_share->button_label ) : ?>
							<span class="_channel-count wp-dark-mode-ignore">
								<span><?php echo esc_html( $channel['count'] ); ?></span>
							</span>
						<?php else : ?>

							<!-- visible only neither channel share count not channel label is found  -->
							<span></span>
						<?php endif; ?>
					</div>
					<?php endif; ?>

					<div class="_channel-overlay"></div> <!-- channel overlay  -->

				</div>
				<?php
			}
		}
		?>

		<!-- more channel toggler button -->
		<?php if ( count( $social_share->channels ) > $social_share->channel_visibility ) : ?>              
			<div class="wpdm-social-share-button wp-dark-mode-ignore _channel _icon-light _<?php echo esc_html( isset( $social_share->button_shape ) && ! empty( $social_share->button_shape ) ? $social_share->button_shape : 'rounded' ); ?>" data-channel="more">
				<span class="_channel-icon wp-dark-mode-ignore">
					<span> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"> <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" /> </svg></span>
				</span>
				<div class="_channel-label">
					<span class="_channel-name wp-dark-mode-ignore"><?php echo esc_html( $social_share->more_label ); ?></span>
					<span></span>
					<div class="_channel-overlay"></div>
				</div>
			</div>
		<?php endif; ?>

		</div>
	</div>

	<!-- all button modal  -->
	<?php
	if ( count( $social_share->channels ) > $social_share->channel_visibility ) :
		?>

		<div class="_wpdm-social-share-modal-overlay" style="display: none;"></div>

		<div class="_wpdm-social-share-modal _fixed-size-large" style="display: none;">

			<div class="_wpdm-social-share-modal-header">
				<div class="_wpdm-social-share-modal-title ">
					<?php echo ! empty( $social_share->share_via_label ) ? esc_html( $social_share->share_via_label ) : esc_html__( 'Share via:', 'wp-dark-mode' ); ?>
				</div>
				<!-- close  -->
				<div class="_wpdm-social-share-modal-close">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
						<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
					</svg>
				</div>

			</div>

			<div class="_wpdm-social-share-modal-body wp-dark-mode-ignore">

				<div class="_channels-container _inside_modal wp-dark-mode-ignore _spaced _rounded _channel-animation-5 _channel-template-1">
					<div class="_channels _channels-inside-modal wp-dark-mode-ignore">
						<!-- Share Icons  -->
						<?php
						if ( $channels && count( $channels ) > 0 ) :
							foreach ( $channels as $channel ) {
								?>

								<div class="wpdm-social-share-button wp-dark-mode-ignore _channel _icon-<?php echo esc_html( $channel['id'] ); ?> _rounded 
									<?php echo $channel['visibility']['mobile'] ? '' : '_hide-on-mobile'; ?> 
									<?php echo $channel['visibility']['desktop'] ? '' : '_hide-on-desktop'; ?>"
									data-channel="<?php echo esc_html( $channel['id'] ); ?>">

									<span class="_channel-icon wp-dark-mode-ignore">
										<span> <?php echo wp_kses( $channel['svg'], $social_share->get_kses_extended_ruleset ); ?> </span>
									</span>

									<div class="_channel-label">
										<span class="_channel-name wp-dark-mode-ignore"><span><?php echo esc_html( $channel['name'] ); ?></span></span>
										<span class="_channel-count wp-dark-mode-ignore"><span><?php echo esc_html( isset( $channel['count'] ) && ! empty( $channel['count'] ) ? $channel['count'] : 0 ); ?></span></span>
									</div>

									<div class="_channel-overlay"></div>
								</div>
								<?php
							}
						endif;
						?>

					</div>
				</div>

			</div>

		</div>
	<?php endif; ?>


</section>


<?php
/***
 * Right after the social share buttons
 */
do_action( 'after_wpdm_social_share' );

?>