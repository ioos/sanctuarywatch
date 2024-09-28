<?php
/**
 * WP Dark Mode - Social Share Preview.
 * Renders social share preview section in admin panel.
 *
 * @package WP_DARK_MODE
 * @since 2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit();
?>
<div x-show="options.enable" class="_preview-section w-full p-4 rounded-sm transition duration-75 relative">
	<div class="w-full sm:sticky z-0 sm:top-40 flex flex-col gap-2 items-center justify-center">
		<div class="w-full flex items-center justify-center">

			<!-- channel icon preview; visible if the tab is not social-meta  -->
			<div x-show="!isTab('social-meta')" class="bg-slate-100 p-3 rounded-md shadow w-full border border-slate-200">

				<!-- title  -->
				<div class="font-semibold text-slate-600 text-sm mb-4"><?php esc_html_e( 'Preview', 'wp-dark-mode' ); ?></div>

				<!-- skeleton -->
				<div class="flex items-center justify-between gap-2 mb-2">
					<div class="h-8 w-full bg-slate-300 rounded-md"></div>
				</div>


				<!-- preview element -->
				<template x-for="index in options.button_position === 'both' ? 2 : 1">
					<div class="w-full">

						<!-- skeleton -->
						<div x-show="options.button_position !== 'both' && index === 1 && ['both', 'below'].includes(options.button_position)" class="flex flex-col gap-2 w-full">
							<div class="flex items-center justify-between gap-2">
								<div class="h-7 w-full bg-slate-300 rounded-md"></div>
								<div class="h-7 w-full bg-slate-300 rounded-md"></div>
								<div class="h-7 w-1/4 bg-slate-300 rounded-md"></div>
							</div>
							<div class="flex items-center w-2/3 justify-between gap-2">
								<div class="h-7 w-full bg-slate-300 rounded-md"></div>
								<div class="h-7 w-full bg-slate-300 rounded-md"></div>
								<div class="h-7 w-2/3 bg-slate-300 rounded-md"></div>
							</div>
						</div>
						<!-- skeleton -->

						<section x-show="options.enable" class="my-6 _social-share-container" :class="{ 
							'opacity-50 pointer-events-none' : !options.enable, 
							'_align-left' : options.button_alignment === 'left', 
							'_align-center' : options.button_alignment === 'center', 
							'_align-right' : options.button_alignment === 'right', 
							'_align-stretch' : options.button_alignment === 'stretch' 
						  }">
							<!-- Share label  -->
							<span x-show="options.share_via_label" 
								class="_share-label focus:border-0 focus:outline-none focus:ring focus:ring-blue-500" 
								x-text="options.share_via_label" 
								contenteditable="true" 
								@input="options.share_via_label = $event.target.innerText"></span>


							<div class="_channels-container _channel-animation-1" :class="{ 
								'_spaced' : options.button_spacing,
								'_no-spaced' : !options.button_spacing,
								'_rounded' : options.button_shape === 'rounded',
								'_circle' : options.button_shape === 'circle',
								'_rectangular' : options.button_shape === 'rectangular',
								'_slanted' : options.button_shape === 'slanted', 
								'_channel-template-2' : options.button_template == '2',
								'_both-label' : options.button_label === 'both',
							}">

								<!-- Share label  -->
								<template x-if="options.show_total_share_count">
									<div class="_total-share">
										<div class="_total-share-count" x-show="options.show_total_share_count">
											<span x-text="randomNumber(100, 200)"></span>
											<span 
												class="focus:outline-none focus:border-0 focus:ring focus:ring-blue-500 transition duration-75" 
												x-text="options.shares_label || 'Share'" 
												contenteditable="true" 
												@input="options.shares_label = $event.target.innerText"></span>
										</div>
									</div>
								</template>

								<div class="_channels transition duration-75">
									<!-- Share Icons  -->
									<template x-for="channel in enabledChannelsForPreview">

										<div class="_channel transition duration-75" :class="[`_icon-${channel.id}`]">
											<span class="_channel-icon">
												<span x-html="getIcon(channel.id)"></span>
											</span>
											<div class="_channel-label" x-show="options.button_label !== 'none'">
												<span class="_channel-name" x-show="['channel_label', 'both'].includes(options.button_label)">
													<span class="transition duration-75 focus:border-0 focus:outline-none" x-text="channel.name" contenteditable="true" @input="channel.name = $event.target.innerText"></span>
												</span>
												<span class="_channel-count transition duration-75" x-show="['share_count', 'both'].includes(options.button_label)">
													<span x-text="options.button_template > 1 ? randomNumber(60, 80) : randomNumber(1, 50)"></span>
												</span>
											</div>
											<span class="_channel-overlay"></span>
										</div>

									</template>

									<template x-if="enabledChannelsForPreview.length < enabledChannels.length">
										<div  class="_channel transition duration-75 _icon-light _icon-more">
											<span class="_channel-icon">
												<span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
														<path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
													</svg></span>
											</span>
											<div class="_channel-label">
												<span class="_channel-name  transition duration-75 focus:border-0 focus:outline-none" x-text="options.more_label" contenteditable="true" @input="options.more_label = $event.target.textContent"></span>
												<span></span>
											</div>
											<span class="_channel-overlay"></span>
										</div>
									</template>								   

								</div>
							</div>

						</section>

						<!-- skeleton -->
						<div x-show="options.button_position !== 'bellow' && index === 1 && ['both', 'above'].includes(options.button_position)" class="flex flex-col gap-2 w-full">
							<div class="flex items-center justify-between gap-2 ">
								<div class="h-7 w-full bg-slate-300 rounded-md"></div>
								<div class="h-7 w-full bg-slate-300 rounded-md"></div>
								<div class="h-7 w-1/4 bg-slate-300 rounded-md"></div>
							</div>
							<div class="flex items-center w-2/3 justify-between gap-2">
								<div class="h-7 w-full bg-slate-300 rounded-md"></div>
								<div class="h-7 w-full bg-slate-300 rounded-md"></div>
								<div class="h-7 w-2/3 bg-slate-300 rounded-md"></div>
							</div>
						</div>
						<!-- skeleton -->
					</div>
				</template>


			</div>

		</div>

		<!-- Save buttons  -->
		<div class="flex justify-end gap-2 mt-2">
			<button type="button" 
				class="px-4 py-2 rounded-sm text-sm transition duration-75" 
				@click.prevent="saveOptionsButton" 
				:class="{ 'bg-green-500 hover:bg-green-600 text-white pl-2' :isChanged, 'bg-slate-200 cursor-not-allowed text-slate-500 pointer-events-none' : !isChanged }">
				<span class="flex items-center gap-1">

					</span>
					<span x-show="state.isChanged"  class="flex items-center justify-center gap-1"> <svg xmlns="http://www.w3.org/2000/svg" class="fill-current w-5" viewBox="0 0 16 16">
						<path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
					</svg> Save & Publish</span>
					<span x-show="! state.isChanged &&  options.enable" class="flex items-center justify-center gap-1"> 
						<svg xmlns="http://www.w3.org/2000/svg" class="fill-current w-5" viewBox="0 96 960 960" width="48"><path d="M294 814 70 590l43-43 181 181 43 43-43 43Zm170 0L240 590l43-43 181 181 384-384 43 43-427 427Zm0-170-43-43 257-257 43 43-257 257Z"/></svg> 
						Published
					</span>
					<span x-show="! state.isChanged &&  ! options.enable">Saved</span>
				</span>
			</button>
		</div>
	</div>
</div>