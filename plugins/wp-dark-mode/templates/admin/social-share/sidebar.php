<?php
/**
 * WP Dark Mode Social Share Sidebar
 * Render social share sidebar template from admin panel.
 *
 * @package WP_DARK_MODE
 * @since 2.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit();
?>
<div class="flex items-center sidebar-width bg-slate-100 border-b border-slate-100">

	<template x-for="tab in constant.tabs">
		<a 
			x-show="options.enable || ['channels'].includes(tab.id)" 
			href="#" 
			@click.prevent="setTab(tab.id)" 
			class="group flex items-center gap-3 cursor-pointer px-3 py-3 hover:text-blue-500 transition duration-100 focus:ring-0 border-r border-slate-200 last:border-0" 
			:class="{ 'bg-white text-blue-500 focus:text-blue-500': isTab(tab.id) }">

			<span class="text-base transition duration-150 group-hover:text-blue-500" :class="{
			'text-blue-600' : isTab(tab.id),
			'text-slate-500' : !isTab(tab.id)
			}" x-html="tab.icon"></span>

			<span class="font-semibold" x-text="tab.title"></span>

		</a>
	</template>

</div>