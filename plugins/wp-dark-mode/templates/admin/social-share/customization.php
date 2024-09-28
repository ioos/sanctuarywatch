<?php
/**
 * WP Dark Mode - Social Share Customization
 * Allows user to customize the inline button styles and templates
 *
 * @package WP_DARK_MODE
 * @since 2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit();
?> <section class="w-full max-md p-3  max-w-md" x-show="isTab('customization')" x-transition:enter.opacity.40>
	<div class="transition duration-150" :class="{'opacity-40 pointer-events-none' : !options.enable}">
		<!-- Inline button templates  -->
		<div class="mb-8">
			<!-- title  -->
			<label for="inline_button_templates" class="font-semibold text-sm text-slate-700 cursor-pointer mb-2  flex gap-1">
				<?php esc_html_e( 'Inline Button Template', 'wp-dark-mode' ); ?> <span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'Choose any inline button template that suits your style', 'wp-dark-mode' ); ?>"></span>
			</label>

			<!-- customization fields  -->
			<div class="grid grid-cols-1 gap-3 w-full">
				<template x-for="templateIndex in 2">
					<div class="flex items-center gap-2 cursor-pointer group relative" @click.prevent="templateIndex > 1 && isFree ? showPromo : options.button_template = templateIndex">
						<!-- template selector  -->
						<div class="w-4 h-4 text-white ring-1 rounded-full flex items-center justify-center group-hover:bg-blue-500 group-hover:ring-blue-500 transition duration-150" 
							:class="{'bg-blue-500 ring-blue-500' : templateIndex == options.button_template, 'bg-slate-100 ring-gray-200' : templateIndex != options.button_template}">
							<svg x-show="templateIndex < 2 || !isFree" 
								xmlns="http://www.w3.org/2000/svg" 
								class="group-hover:opacity-100 group-hover:scale-100 transition duration-200 fa fa-check mt-0.5 fill-current text-white" 
								:class="{'opacity-100' : templateIndex == options.button_template, 'opacity-0 scale-0' : templateIndex != options.button_template}" 
								viewBox="0 0 16 16">
								<path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z" />
							</svg>
							<svg x-show="templateIndex > 1 && isFree" 
								xmlns="http://www.w3.org/2000/svg" 
								viewBox="0 0 64 64" 
								class="fill-current w-4 ring-purple-500 opacity-100 scale-100 text-purple-500 group-hover:text-white">								
								<?php //phpcs:ignore ?>
								<path d="M 32 9 C 24.832 9 19 14.832 19 22 L 19 27.347656 C 16.670659 28.171862 15 30.388126 15 33 L 15 49 C 15 52.314 17.686 55 21 55 L 43 55 C 46.314 55 49 52.314 49 49 L 49 33 C 49 30.388126 47.329341 28.171862 45 27.347656 L 45 22 C 45 14.832 39.168 9 32 9 z M 32 13 C 36.963 13 41 17.038 41 22 L 41 27 L 23 27 L 23 22 C 23 17.038 27.037 13 32 13 z"></path>
							</svg>
						</div>
						<!-- template display  -->
						<div class="w-full _social-share-container _fixed-size" :class="{'group-hover:opacity-0' : isFree && templateIndex > 1}">
							<div class="_channels-container _demo _spaced _both-label _channel-template-1 _spaced _rounded _no-wrap" :class="`_channel-template-${templateIndex}`">
								<div class="_channels _no-wrap transition duration-100">

									<!-- Dummy channel icons  -->
									<template x-for="channelId in ['facebook', 'twitter']">
										<div class="_channel transition duration-100" :class="[`_icon-${channelId}` ]">
											<!-- channel icon  -->
											<span class="_channel-icon"><span x-html="getIcon(channelId)"></span></span>
											<!-- channel label  -->
											<div class="_channel-label">
												<!-- channel name  -->
												<span class="_channel-name"><span class="capitalize" x-text="channelId"></span></span>
												<!-- channel count -->
												<span class="_channel-count transition duration-100">
													<span x-text="templateIndex > 1 ? randomNumber(60, 80) : randomNumber(1, 50)"></span>
												</span>
											</div>
										</div>
									</template>

									<!-- more button  -->
									<div class="_channel transition duration-100 _icon-light">
										<span class="_channel-icon">
											<span>
												<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
													<path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
												</svg>
											</span>
										</span>
										<div class="channel-label pr-3" :class="{'pl-2' : templateIndex == 1}">
											<span class="_channel-name">
												<span class="capitalize"><?php esc_html_e( 'More', 'wp-dark-mode' ); ?></span>
											</span>
											<!-- extra space  -->
											<span></span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- overlay -->
						<wpdm-upgrade switch="false" border="false" class="ml-6" overlay="false" align="left" x-show="templateIndex > 1 && isFree"></wpdm-upgrade>
					</div>
				</template>
			</div>
		</div>


		<!-- Share label  -->
		<div class="mb-8">
			<label for="share_label" class="font-semibold text-sm text-slate-700 cursor-pointer mb-2  flex gap-1">
				<?php esc_html_e( 'Share Label', 'wp-dark-mode' ); ?> 
				<span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'Share label appears before the inline button. Provide engaging texts to make sure your visitors are sharing the content!', 'wp-dark-mode' ); ?>">
				</span>
			</label>
			<div>
				<input type="text" x-model="options.share_via_label" class="input-text" id="share_label" placeholder="<?php esc_html_e( 'Sharing is Caring: ', 'wp-dark-mode' ); ?>">
			</div>
		</div>


		<!-- Button position -->
		<div class="mb-8">
			<label class="font-semibold text-sm text-slate-700 cursor-pointer mb-2  flex gap-1">
				<?php esc_html_e( 'Button Position', 'wp-dark-mode' ); ?> <span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'Choose the position where you want show your inline button.', 'wp-dark-mode' ); ?>"></span>
			</label>
			<div class="wpdm-button-group">
				<template x-for="(label, key) in constant.button_positions">
					<label class="wpdm-button" x-text="label" :class="{'active' : key === options.button_position}" @click.prevent="options.button_position = key"></label>
				</template>
			</div>
		</div>


		<!-- Button alignment -->
		<div class="mb-8">
			<label class="font-semibold text-sm text-slate-700 cursor-pointer mb-2  flex gap-1">
				<?php esc_html_e( 'Button Alignment', 'wp-dark-mode' ); ?> <span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'Choose the alignment of the button.', 'wp-dark-mode' ); ?>"></span>
			</label>
			<div class="wpdm-button-group">
				<template x-for="(label, key) in constant.button_alignments">
					<label class="wpdm-button" :title="label" :class="key === options.button_alignment ? 'active' : []" @click.prevent="options.button_alignment = key">
						<svg xmlns="http://www.w3.org/2000/svg" class="fill-current w-5" viewBox="0 0 16 16">
							<path x-show="key === 'left'" fill-rule="evenodd" d="M1.5 1a.5.5 0 0 1 .5.5v13a.5.5 0 0 1-1 0v-13a.5.5 0 0 1 .5-.5z" />
							<path x-show="key === 'left'" d="M3 7a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7z" />
							<path x-show="key === 'center'" d="M8 1a.5.5 0 0 1 .5.5V6h-1V1.5A.5.5 0 0 1 8 1zm0 14a.5.5 0 0 1-.5-.5V10h1v4.5a.5.5 0 0 1-.5.5zM2 7a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7z" />
							<path x-show="key === 'right'" fill-rule="evenodd" d="M14.5 1a.5.5 0 0 0-.5.5v13a.5.5 0 0 0 1 0v-13a.5.5 0 0 0-.5-.5z" />
							<path x-show="key === 'right'" d="M13 7a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V7z" />
							<path x-show="key === 'stretch'" d="M13 7a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V7z" />
						</svg>
					</label>
				</template>
			</div>
		</div>


		<!-- Button shape -->
		<div class="mb-8">
			<label class="font-semibold text-sm text-slate-700 cursor-pointer mb-2  flex gap-1">
				<?php esc_html_e( 'Button Shape', 'wp-dark-mode' ); ?> <span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'Choose the button shape that matches your site content.', 'wp-dark-mode' ); ?>"></span>
			</label>
			<div class="wpdm-button-group">
				<template x-for="(label, key) in constant.button_shapes">
					<label class="wpdm-button" x-text="label" :class="key === options.button_shape ? ['active'] : []" @click.prevent="options.button_shape = key"></label>
				</template>
			</div>
		</div>


		<!-- Button size -->
		<div class="mb-8">
			<label class="font-semibold text-sm text-slate-700 cursor-pointer mb-2  flex gap-1">
				<?php esc_html_e( 'Button Size', 'wp-dark-mode' ); ?> <span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'Choose the default size for your inline button.', 'wp-dark-mode' ); ?>"></span>
			</label>
			<div class="wpdm-button-group">
				<template x-for="(label, key) in constant.button_sizes">
					<label class="wpdm-button" x-text="label" :class="key === options.button_size ? ['active'] : []" @click.prevent="options.button_size = key ; updatePreview()"></label>
				</template>
			</div>
		</div>


		<!-- Button labels -->
		<div class="mb-8">
			<label class="font-semibold text-sm text-slate-700 cursor-pointer mb-2  flex gap-1">
				<?php esc_html_e( 'Button Label', 'wp-dark-mode' ); ?> <span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'Button labels can display your channel’s name and share count.', 'wp-dark-mode' ); ?>"></span>
			</label>
			<div class="wpdm-button-group">
				<template x-for="(label, key) in constant.button_labels">
					<label class="wpdm-button" x-text="label" :class="key === options.button_label ? 'active' : []" @click.prevent="options.button_label = key"></label>
				</template>
			</div>
		</div>

		<!-- Premium features  -->
		<!-- Button responsiveness  -->
		<div class="mb-8">
			<label class="font-semibold text-sm text-slate-700 cursor-pointer mb-2  flex gap-1">
				<?php esc_html_e( 'Hide Buttons on', 'wp-dark-mode' ); ?> <span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'Select where you want to hide your inline button.', 'wp-dark-mode' ); ?>"></span>
				<span x-show="!isPro" class="badge-ultimate">
					<?php esc_html_e( 'Ultimate', 'wp-dark-mode' ); ?>
				</span>
			</label>
			<div class=" group relative">
				<div class="wpdm-button-group" :class="{'group-hover:opacity-0' : isFree}">
					<template x-for="(label, key) in constant.devices">
						<label class="wpdm-button" :title="label.name" x-html="label.icon + ' ' + label.name" 
							:class="{'active' : options.hide_button_on[key] === true}" @click.prevent=" !isFree ? options.hide_button_on[key]=!options.hide_button_on[key] : showPromo"></label>
					</template>
				</div>
				<wpdm-upgrade x-show="isFree" switch="false" @click.prevent="showPromo" overlay="false" border="false" align="left"></wpdm-upgrade>
			</div>
		</div>


		<!-- Display button in post types  -->
		<div class="mb-8">
			<label class="font-semibold text-sm text-slate-700 cursor-pointer mb-2  flex gap-1">
				<?php esc_html_e( 'Display Buttons on', 'wp-dark-mode' ); ?> <span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'Choose where the inline button will appear on your website.', 'wp-dark-mode' ); ?>"></span>
				<span x-show="!isPro" class="badge-ultimate">
					<?php esc_html_e( 'Ultimate', 'wp-dark-mode' ); ?>
				</span>
			</label>
			<div class="wpdm-button-group gap-1 flex-wrap"">
				<template x-for=" post_type in constant.post_types">
				<label 
					class="wpdm-button rounded-sm relative group" 
					:title="post_type.name" 
					:class="[(options.post_types.includes(post_type.id) ? 'active' : '') ]" 
					@click.prevent="['post', 'page'].includes(post_type.id) || !isFree ? togglePostType(post_type.id) : showPromo">
					<svg x-show="['post', 'page'].includes(post_type.id) || isPro" xmlns="http://www.w3.org/2000/svg" class="fill-current w-3" :class="options.post_types.includes(post_type.id) ? '' : 'opacity-40'" viewBox="0 0 16 16">
						<path x-show="!options.post_types.includes(post_type.id)" d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
						<path x-show="options.post_types.includes(post_type.id)" d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
						<path x-show="options.post_types.includes(post_type.id)" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.235.235 0 0 1 .02-.022z" />
					</svg>
					<svg x-show="!['post', 'page'].includes(post_type.id) && isFree" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="fill-current w-4 ring-purple-500 opacity-100 scale-100 text-purple-500">
						<?php //phpcs:ignore ?>
						<path d="M 32 9 C 24.832 9 19 14.832 19 22 L 19 27.347656 C 16.670659 28.171862 15 30.388126 15 33 L 15 49 C 15 52.314 17.686 55 21 55 L 43 55 C 46.314 55 49 52.314 49 49 L 49 33 C 49 30.388126 47.329341 28.171862 45 27.347656 L 45 22 C 45 14.832 39.168 9 32 9 z M 32 13 C 36.963 13 41 17.038 41 22 L 41 27 L 23 27 L 23 22 C 23 17.038 27.037 13 32 13 z"></path>
					</svg>
					<span x-html="post_type.name"></span>
				</label>
				</template>
			</div>
		</div>


		<!-- Button spacing  -->
		<div class="mb-6 flex items-center gap-2 justify-between" @click="isFree ? showPromo : ''">
			<label for="button_spacing" class="font-semibold text-sm text-slate-700 cursor-pointer flex gap-1">
				<?php esc_html_e( 'Button Spacing', 'wp-dark-mode' ); ?> <span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'Select default spacing between each channel.', 'wp-dark-mode' ); ?>"></span>
				<span x-show="!isPro" class="badge-ultimate">
					<?php esc_html_e( 'Ultimate', 'wp-dark-mode' ); ?>
				</span>
			</label>
			<label for="button_spacing" class="_switcher group relative">
				<input type="checkbox" id="button_spacing" x-model="options.button_spacing" :disabled="isFree">
				<span></span>
				<wpdm-upgrade switch="true" @click.prevent="showPromo" overlay="false" border="false" align="right" text="Upgrade Now" x-show="isFree"></wpdm-upgrade>
			</label>
		</div>


		<!-- Total Share  -->
		<div class="flex items-center justify-between mb-6" @click="isFree ? showPromo : ''">
			<label for="total_share" class="font-semibold text-sm text-slate-700 cursor-pointer w-48 flex gap-1">
				<?php esc_html_e( 'Total Shares', 'wp-dark-mode' ); ?> 
				<span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'Enable total share counter. It will show how many times your content is shared via enabled channels.', 'wp-dark-mode' ); ?>"></span>
				<span x-show="!isPro" class="badge-ultimate">
					<?php esc_html_e( 'Ultimate', 'wp-dark-mode' ); ?>
				</span>
			</label>
			<label for="total_share" class="_switcher group relative">
				<input type="checkbox" :class="{'group-hover:opacity-0' : isFree}" :disabled="isFree" id="total_share" x-model="options.show_total_share_count">
				<span></span>
				<wpdm-upgrade switch="true" @click.prevent="showPromo" overlay="false" border="false" align="right" text="Upgrade Now" x-show="isFree"></wpdm-upgrade>
			</label>
		</div>
		<div class="mb-6">
			<label for="minimum_share" class="font-semibold text-sm text-slate-700 cursor-pointer mb-2  flex gap-1">
				<?php esc_html_e( 'Minimum Share Count', 'wp-dark-mode' ); ?> 
				<span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'This is the minimum number of shares a page needs to have before the share counter is shown.', 'wp-dark-mode' ); ?>"></span>
				<span x-show="!isUltimate" class="badge-ultimate">
					<?php esc_html_e( 'Ultimate', 'wp-dark-mode' ); ?>
				</span>
			</label>
			<div class="w-full group relative">
				<input type="number" :class="{'group-hover:opacity-0' :  !isUltimate}" x-model="options.minimum_share_count" min="0" class="input-text text-sm" id="minimum_share" :disabled="!isUltimate">
				<wpdm-upgrade switch="false" overlay="false" align="left" border="false" @click.prevent="showPromo" x-show="!isUltimate"></wpdm-upgrade>
			</div>
		</div>


		<!-- Maximum share count per visitor -->
		<div class="mb-6">
			<label class="font-semibold text-sm text-slate-700 cursor-pointer mb-2  flex gap-1">
				<?php esc_html_e( 'Maximum Share Click', 'wp-dark-mode' ); ?> 
				<span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'This is the maximum number of click count on a social channel you want to show your visitors', 'wp-dark-mode' ); ?>"></span>
				<span x-show="!isUltimate" class="badge-ultimate">
					<?php esc_html_e( 'Ultimate', 'wp-dark-mode' ); ?>
				</span>
			</label>
			<div class="w-full group relative">
				<input type="number" min="1" :class="{'group-hover:opacity-0' : !isUltimate}" max="7" class="input-text text-sm" x-model="options.maximum_click_count" :disabled="!isUltimate">
				<wpdm-upgrade switch="false" overlay="false" align="left" border="false" @click.prevent="showPromo" x-show="!isUltimate"></wpdm-upgrade>
			</div>
		</div>
		<!-- Show first N buttons -->
		<div class="mb-6">
			<label class="font-semibold text-sm text-slate-700 cursor-pointer mb-2  flex gap-1">
				<?php esc_html_e( 'Channel Visibility Count', 'wp-dark-mode' ); ?> 
				<span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'Set the number of channels that you want to show on your website. The rest will be shown after clicking on the \'More\' button.', 'wp-dark-mode' ); ?>"></span>
				<span x-show="!isUltimate" class="badge-ultimate">
					<?php esc_html_e( 'Ultimate', 'wp-dark-mode' ); ?>
				</span>
			</label>
			<div class="w-full group relative">
				<input type="number" min="1" :class="{'group-hover:opacity-0' : !isUltimate}" max="7" class="input-text text-sm" x-model="options.channel_visibility" :disabled="!isUltimate">
				<wpdm-upgrade switch="false" overlay="false" align="left" border="false" @click.prevent="showPromo" x-show="!isUltimate"></wpdm-upgrade>
			</div>
		</div>
	</div>
</section>