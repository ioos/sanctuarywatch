<?php
/**
 * WP Dark Mode - Social Share Channels
 * Manage social share channels
 *
 * @package WP_DARK_MODE
 * @since 2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit();
?>

<!-- Manage channels	-->
<section class="w-full max-md p-3 max-w-md" x-show="isTab('channels')" x-transition:enter.opacity.40>
	<!-- enable social share	-->
	<div class="flex items-center justify-between mb-8">
		<label for="enable" class="font-semibold text-sm text-slate-700 cursor-pointer w-3/4"><?php esc_html_e( 'Enable Social Share (Inline Button)', 'wp-dark-mode' ); ?></label>
		<label for="enable" class="_switcher">
			<input type="checkbox" id="enable" x-model="options.enable" @change="toggleSocialShare">
			<span></span>
		</label>
	</div>

	<!-- content	-->
	<div class="w-full transition duration-150 relative" x-show="options.enable" :class=" {'opacity-20 pointer-events-none' : !options.enable}">

		<label class="font-semibold text-sm text-slate-700 cursor-pointer block mb-2"><?php esc_html_e( 'Enable your preferred social channels', 'wp-dark-mode' ); ?></label>
		<div class="relative">
			<input type="text" x-model="state.search_channels" class="input-text text-xs" placeholder="<?php esc_html_e( 'Search Channel', 'wp-dark-mode' ); ?>">

			<svg xmlns="http://www.w3.org/2000/svg" @click.prevent="state.search_channels = ''" class="w-3 fill-current cursor-pointer absolute right-2 top-1/2 -translate-y-1/2 opacity-30 hover:opacity-100 transition duration-150" 
				viewBox="0 0 16 16">
				<path x-show="!state.search_channels" d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
				<path x-show="state.search_channels" d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
			</svg>
		</div>

		<!-- grid social icons	-->
		<div x-show="filteredChannels.length" 
			class="_social-share-container _icons-grid _max-height grid gap-3 grid-cols-2 md:grid-cols-5 mt-4 inline flex-wrap overflow-y-auto scrollbar-thin hover:scrollbar-thumb-slate-300 scrollbar-track-transparent">

			<template x-for="channel in filteredChannels">
				<!-- single social channel	-->
				<div 
					class="inline-flex flex-col justify-center items-center gap-1 cursor-pointer _channels-container opacity-90 hover:opacity-100 transition duration-150 hover:grayscale-0" 
					:class="{ 'grayscale': !isChannelEnabled(channel.id) }" 
					@mouseover="channel.hover = true" @mouseleave="channel.hover = false"  @click.prevent="toggleChannel(channel.id)"
				>
					<!-- channel icon  -->
					<span class="text-lg w-8 h-8 pt-0.5 rounded-full text-white flex items-center justify-center _icon-svg" :class="[channel.hover || isChannelEnabled(channel.id) ? channel.class : 'bg-gray-300']" x-html="getIcon(channel.id)"></span>
					<!-- channel name -->
					<span class="text-xs text-center text-slate-600" x-text="channel.name"></span>

					<!-- tick, visible when the channel is enabled  -->
					<template x-if="channel.enabled">
						<svg xmlns="http://www.w3.org/2000/svg" class="fill-current w-4" :class="{ 'text-blue-500' : isChannelEnabled(channel.id), 'opacity-10' : !isChannelEnabled(channel.id) }" viewBox="0 0 16 16">
							<path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z" />
						</svg>
					</template>

					<!-- cross, visible when the channel is disabled  -->
					<template x-if="!channel.enabled">
						<svg xmlns="http://www.w3.org/2000/svg" class="fill-current w-4 text-slate-300" viewBox="0 0 16 16">
							<path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
						</svg>
					</template>

				</div>
				<!-- single icon ends	-->
			</template>

		</div>


		<div x-show="!filteredChannels.length" class="text-slate-400 text-center mt-4">
			<?php esc_html_e( 'Not found <span class="italic text-blue-500" x-text="state.search_channels"></span>', 'wp-dark-mode' ); ?>
		</div>

		<!-- channel footer buttons	-->
		<div class="flex flex-between gap-3 my-6 justify-center text-xs">

			<!-- show all button	-->
			<a href="javascript:;" 
				class="bg-transparent px-2 py-1 rounded-sm font-medium focus:outline-none text-blue-400 hover:ring-blue-600 ring-1 ring-blue-500 focus:text-blue-500 hover:bg-blue-50 inline-flex items-center gap-1 transition duration-150" 
				@click.prevent="state.showAllChannels = !state.showAllChannels">
				<svg xmlns="http://www.w3.org/2000/svg" class="fill-current w-3 transition duration-300" :class="{'rotate-180' : state.showAllChannels}" viewBox="0 0 16 16">
					<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" />
				</svg>
				<span x-text="state.showAllChannels ? ' <?php esc_html_e( 'See less channels', 'wp-dark-mode' ); ?>' : ' <?php esc_html_e( 'See more channels', 'wp-dark-mode' ); ?>'"></span>
			</a>

			<!-- Unlock button -->
			<a href="javascript:;" 
				x-show="isFree" 
				class="bg-red-50 px-2 py-1 rounded-sm font-medium focus:outline-none focus:ring-red-400 text-red-600 ring-1 ring-red-200 hover:text-red-400 hover:opacity-75 transition duration-150" 
				@click.prevent="showPromo">
				<?php esc_html_e( 'Unlock all channels', 'wp-dark-mode' ); ?>
			</a>
		</div>

		<!-- manage channels	-->
		<div class="mt-8">
			<!-- title  -->
			<label class="font-semibold text-sm text-slate-700 cursor-pointer mb-2 flex gap-2">
				<?php esc_html_e( 'Manage channels', 'wp-dark-mode' ); ?> 
				<span class="wpdarkmode-tooltip" title="<?php esc_html_e( 'Reorder your social channels. You can edit the channel label and device visibility for each. Drag channels to change their order.', 'wp-dark-mode' ); ?>"></span>
			</label>

			<!-- manage channels; sortable, editable  -->
			<div class="flex flex-col gap-2" dropzone="true">

				<template x-for="channel in enabledChannels">

					<!-- single channel -->
					<div class="flex flex-col gap-2 w-80 relative" 
						dropzone="true" 
						draggable="true"
						@dragstart="state.draggingChannel = channel.id" 
						@dragenter="handleDrag($event, channel.id)" 
						@dragend="handleDrop($event, channel.id)" 
						:class="`_channel-${channel.id}`">

						<div class="flex items-center gap-4 w-full cursor-pointer _social-share-container inline" tabindex="0">

							<!-- move icon -->
							<span tabindex="1" class="text-slate-200 transition duration-100 h-full flex items-center justify-center text-2xl cursor-grab" :class="{'text-blue-600' : state.draggingChannel === channel.id }">
								<svg xmlns="http://www.w3.org/2000/svg" class="fill-current w-8" viewBox="0 0 16 16">
									<path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
								</svg>
							</span>

							<!-- channel name -->
							<div class="flex items-center bg-slate-200 _channel-name">
								<div class="bg-slate-600 text-white h-10 w-10 flex items-center justify-center text-base _icon-svg" :class="`_icon-${channel.id}`" x-html="getIcon(channel.id)"></div>
								<div class="flex w-28 items-center gap-2 relative">
									<!-- editable name  -->
									<div class="text-sm h-full w-full pl-3 font-medium flex items-center focus:outline-none transition duration-100 focus:ring focus:ring-blue-400 h-8" 
										x-text="channel.name" 
										contenteditable="true" 
										@input="channel.name = $event.target.innerText"></div>
									<!-- pencil icon  -->
									<svg xmlns="http://www.w3.org/2000/svg" 
										class="fill-current w-3 absolute right-2.5 top-1/2 -translate-y-1/2" viewBox="0 0 16 16" 
										@click.prevent="editableChannel($event)">										
										<?php // phpcs:ignore ?>
										<path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z" /> 
									</svg>
								</div>
							</div>

							<!-- visibility: mobile -->
							<span 
								@click.prevent="channel.visibility.mobile = !channel.visibility.mobile" 
								class="bg-slate-200 h-10 w-11 flex items-center justify-center text-base transition duration-100" 
								:class="channel.visibility.mobile ? ['text-blue-500'] : ['text-slate-300']" 
								x-html="constant.devices.mobile.icon"></span>

							<!-- visibility: desktop -->
							<span 
								@click.prevent="channel.visibility.desktop = !channel.visibility.desktop" 
								class="bg-slate-200 h-10 w-11 flex items-center justify-center text-base transition duration-100" 
								:class="channel.visibility.desktop ? ['text-blue-500'] : ['text-slate-300']" 
								x-html="constant.devices.desktop.icon"></span>

							<!-- unselect channel -->
							<span @click.prevent="toggleChannel(channel.id)" class="bg-slate-200 text-slate-400 hover:text-red-600 transition duration-150 h-10 w-11 flex items-center justify-center text-base">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="fill-current w-6">
									<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
								</svg>
							</span>

						</div>

						<!-- sort dropzone	-->
						<div 
							class="h-full w-full absolute bg-white shadow z-20 ring rounded-sm ring-blue-400 flex items-center justify-center text-center font-semibold tracking-wider" 
							x-show="state.draggingChannel === channel.id" 
							draggable="true"><?php esc_html_e( 'Drop here', 'wp-dark-mode' ); ?></div>
					</div>
				</template>
			</div>
		</div>

	</div>

</section>