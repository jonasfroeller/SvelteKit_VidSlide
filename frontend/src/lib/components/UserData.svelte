<script lang="ts">
	/* --- INIT --- */
	// Backend Api
	import Api from '$api/api';
	import { Route } from '$api/api-routes';

	// Translation
	import translation from '$translation/i18n-svelte';

	// JS-Framework/Library
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';

	// CSS-Framework/Library
	import { clipboard, modalStore } from '@skeletonlabs/skeleton';
	import { toastStore } from '@skeletonlabs/skeleton';
	import { FileDropzone } from '@skeletonlabs/skeleton';
	import { FileButton } from '@skeletonlabs/skeleton';

	// Components
	import Avatar from '$component/Avatar.svelte';
	import Popups from '$component/Popups.svelte';
	let popups; // popups in Popups.svelte

	// Stores
	import {
		loginState,
		user,
		user_stats,
		user_videos_liked,
		user_videos_disliked,
		user_comments_liked,
		user_comments_disliked,
		user_subscribed,
		user_subscribers,
		user_social
	} from '$store/account';

	// Props
	export let page;

	/* --- LOGIC --- */
	async function fetchUser(id_specification = '') {
		let response = await Api.get(
			Route.REQUEST_METHOD.GET.medium.user.root,
			Route.REQUEST_METHOD.GET.medium.user.id.username.branch,
			Route.REQUEST_METHOD.GET.medium.user.id.username.medium_id(id_specification)
		);
		return response;
	}

	function formatUser(user: JSON) {
		let formattet_object = user?.data;
		formattet_object['user'] = formattet_object[0][0];
		delete formattet_object['0'];

		return formattet_object;
	}

	onMount(async () => {
		let username = page.includes('account') ? page.split('/')[3] : this_user_username;
		let user = await fetchUser(username);
		current_user = formatUser(user);

		current_user_socials = {};
		current_user_socials['instagram'] = current_user?.socials?.find(
			(social) => social.SOCIAL_PLATFORM.toLowerCase() === 'instagram'
		);
		current_user_socials['youtube'] = current_user?.socials?.find(
			(social) => social.SOCIAL_PLATFORM.toLowerCase() === 'youtube'
		);
		current_user_socials['tiktok'] = current_user?.socials?.find(
			(social) => social.SOCIAL_PLATFORM.toLowerCase() === 'tiktok'
		);
		current_user_socials['twitter'] = current_user?.socials?.find(
			(social) => social.SOCIAL_PLATFORM.toLowerCase() === 'twitter'
		);

		const socials = document.querySelectorAll('.social-media-site');
		if (socials) {
			let maxBoxWidth = 0;

			socials.forEach((box) => {
				const boxWidth = box.offsetWidth;
				if (boxWidth > maxBoxWidth) {
					maxBoxWidth = boxWidth;
				}
			});

			socials.forEach((box) => {
				box.style = `min-width: ${maxBoxWidth}px !important;`;
			});
		}
	});

	let current_user = null;
	let current_user_socials = null;
	$: current_user_username = current_user?.user?.USER_USERNAME ?? $translation.global.loading();
	$: current_user_profile_description =
		current_user?.user?.USER_PROFILEDESCRIPTION ?? $translation.UserData.no_description();
	$: current_user_follower = current_user?.stats?.views?.length ?? 0;
	$: current_user_views = current_user?.stats?.views?.length ?? 0;
	$: current_user_videos = current_user?.stats?.videos?.length ?? 0;
	$: current_user_date_joined = current_user?.user?.USER_DATETIMECREATED ?? '???';

	$: this_user_id = $user?.data?.VS_USER_ID ?? -1;
	$: this_user_username = $user?.data?.USER_USERNAME ?? '??';
	$: this_user_profile_description =
		$user?.data?.USER_PROFILEDESCRIPTION ?? $translation.UserData.no_description();
	$: this_user_avatar = $user?.data?.USER_PROFILEPICTURE;
	$: this_user_date_joined = $user?.data?.USER_DATETIMECREATED ?? '???';
	$: this_user_last_edit = $user?.data?.USER_LASTUPDATE ?? '???';

	let editable = false;
</script>

{#key $translation}
	<Popups bind:this={popups} />
{/key}

{#if page.includes('account')}
	<div id="user-info" class="flex flex-wrap justify-between gap-4 items-center w-full pb-2">
		<div class="flex items-center gap-2">
			{#if editable}
				<FileButton name="avatar" />
			{:else}
				<Avatar comment_username={current_user_username} size="large" />
			{/if}
			<div class="flex flex-col">
				<button
					id="username"
					class="text-lg flex {editable
						? 'cursor-text rounded-md outline-2 outline-offset-0 outline-dashed outline-tertiary-900 dark:outline-tertiary-50 text-tertiary-900 dark:text-tertiary-50'
						: 'text-primary-700 dark:text-primary-400'}"
					contenteditable={editable}
					use:clipboard={current_user_username}
					on:click={() => {
						if (!editable) {
							toastStore.trigger(popups.copiedUsername_toClipboard_success);
						}
					}}
				>
					{current_user_username}
				</button>
				<div id="stats" class="text-md text-primary-700 dark:text-primary-500 flex divide-x">
					<p class="unstyled p-1">
						{$translation.UserData.follower(current_user_follower)}&nbsp;
					</p>
					<p class="unstyled p-1">
						&nbsp;{$translation.UserData.views(current_user_views)}&nbsp;
					</p>
					<p class="unstyled p-1">
						&nbsp;{$translation.UserData.videos(current_user_videos)}&nbsp;
					</p>
					<p class="unstyled p-1">
						&nbsp;{$translation.UserData.joined(current_user_date_joined)}
					</p>
				</div>
			</div>
		</div>
		<div class="flex gap-2">
			{#if $loginState && this_user_id === current_user?.user?.VS_USER_ID}
				<button
					on:click={() => {
						if (this_user_id === current_user?.user?.VS_USER_ID) {
							editable = !editable;
						}
					}}
					class="btn variant-ringed hover:variant-filled h-1/2"
					type="button"
				>
					{$translation.UserData.edit()}
				</button>
				<button
					on:click={() => modalStore.trigger(popups.uploadVideo)}
					class="btn variant-ringed hover:variant-filled h-1/2"
					type="button"
				>
					{$translation.UserData.post()}
				</button>
			{:else if $loginState}
				<button class="btn variant-ringed hover:variant-filled h-1/2" type="button">
					{$translation.UserData.follow()}
				</button>
			{:else}
				<button
					on:click={() => toastStore.trigger(popups.login_required)}
					class="btn variant-ringed hover:variant-filled h-1/2"
					type="button"
				>
					{$translation.UserData.follow()}
				</button>
			{/if}
		</div>
	</div>
	<hr />
	<div id="user-description" class="flex justify-between p-2">
		<div class="flex flex-col">
			<div
				class="textbox p-2 {editable
					? 'rounded-md outline-2 outline-offset-0 outline-dashed outline-tertiary-900 dark:outline-tertiary-50 text-tertiary-900 dark:text-tertiary-50'
					: ''}"
				contenteditable={editable}
			>
				{current_user_profile_description}
			</div>
			<!-- <button class="btn variant-ringed hover:variant-filled h-1/2 w-fit mt-2" type="button">
				{$translation.UserData.more()}
			</button> -->
		</div>
		<div id="user-socials" class="flex gap-2 pl-2 pt-2 h-min">
			{#if current_user_socials?.instagram}
				<a href={current_user_socials.instagram.SOCIAL_URL} target="_blank" class="transition"
					><iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:instagram" /></a
				>
			{/if}
			{#if current_user_socials?.youtube}
				<a href={current_user_socials.youtube.SOCIAL_URL} target="_blank" class="transition"
					><iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:youtube" /></a
				>
			{/if}
			{#if current_user_socials?.tiktok}
				<a href={current_user_socials.tiktok.SOCIAL_URL} target="_blank" class="transition"
					><iconify-icon
						class="cursor-pointer"
						width="30"
						height="30"
						icon="ic:baseline-tiktok"
					/></a
				>
			{/if}
			{#if current_user_socials?.twitter}
				<a href={current_user_socials.twitter.SOCIAL_URL} target="_blank" class="transition"
					><iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:twitter" /></a
				>
			{/if}
		</div>
	</div>
	<hr />
{:else}
	<!-- Settings -->
	<div class="pt-4">
		<h3>{$translation.pages.settings.account_section.title()}</h3>
		<div class="flex flex-col flex-wrap gap-8 divide-y divide-primary-900">
			<div id="account" class="flex flex-col p-2">
				<div class="flex flex-col md:flex-row gap-2">
					<label class="label">
						<span>{$translation.pages.settings.account_section.username()}</span>
						<div class="input-group input-group-divider grid-cols-[1fr_auto]">
							<input
								disabled
								class="input p-2"
								type="text"
								placeholder={this_user_username}
								value={this_user_username}
							/>
							<button class="variant-soft-secondary"
								>{$translation.pages.settings.account_section.edit()}</button
							>
						</div>
					</label>
					<label class="label">
						<span>{$translation.pages.settings.account_section.password()}</span>
						<div class="input-group input-group-divider grid-cols-[1fr_auto]">
							<input
								disabled
								class="input p-2"
								type="password"
								placeholder="●●●●●●●●"
								value="●●●●●●●●"
							/>
							<button class="variant-soft-secondary"
								>{$translation.pages.settings.account_section.edit()}</button
							>
						</div>
					</label>
				</div>
			</div>
			<div id="socials" class="flex flex-col gap-2 p-2">
				<h4>{$translation.pages.settings.account_section.socials()}</h4>
				<div class="input-group input-group-divider md:grid-cols-[auto_1fr_auto]">
					<div class="social-media-site input-group-shim">
						<span class="block mx-auto md:m-0">https://www.instagram.com/</span>
					</div>
					<input disabled class="outline-none p-2" type="text" placeholder="@your_username" />
					<button class="variant-soft-secondary"
						><span class="block mx-auto">{$translation.pages.settings.account_section.edit()}</span
						></button
					>
				</div>
				<div class="input-group input-group-divider md:grid-cols-[auto_1fr_auto]">
					<div class="social-media-site input-group-shim">
						<span class="block mx-auto md:m-0">https://www.youtube.com/</span>
					</div>
					<input disabled class="outline-none p-2" type="text" placeholder="@your_username" />
					<button class="variant-soft-secondary"
						><span class="block mx-auto">{$translation.pages.settings.account_section.edit()}</span
						></button
					>
				</div>
				<div class="input-group input-group-divider md:grid-cols-[auto_1fr_auto]">
					<div class="social-media-site input-group-shim">
						<span class="block mx-auto md:m-0">https://www.twitter.com/</span>
					</div>
					<input disabled class="outline-none p-2" type="text" placeholder="@your_username" />
					<button class="variant-soft-secondary"
						><span class="block mx-auto">{$translation.pages.settings.account_section.edit()}</span>
					</button>
				</div>
				<div class="input-group input-group-divider md:grid-cols-[auto_1fr_auto]">
					<div class="social-media-site input-group-shim">
						<span class="block mx-auto md:m-0">https://www.tiktok.com/</span>
					</div>
					<input disabled class="outline-none p-2" type="text" placeholder="@your_username" />
					<button class="variant-soft-secondary"
						><span class="block mx-auto">{$translation.pages.settings.account_section.edit()}</span
						></button
					>
				</div>
			</div>
			<div id="avatar" class="p-2">
				<div class="flex flex-col gap-2 w-fit">
					<h4>{$translation.pages.settings.account_section.avatar()}</h4>
					<div class="flex items-center gap-2">
						<FileDropzone name="files">
							<svelte:fragment slot="lead"
								><iconify-icon
									class="cursor-pointer"
									width="30"
									height="30"
									icon="mdi:file-upload"
								/></svelte:fragment
							>
							<svelte:fragment slot="message"
								>{$translation.UserData.upload_avatar()}</svelte:fragment
							>
							<svelte:fragment slot="meta">JPG, PNG, GIF, BMP, WEBP</svelte:fragment>
						</FileDropzone>
						<Avatar
							comment_avatar={this_user_avatar}
							comment_username={this_user_username}
							size="large"
						/>
					</div>
				</div>
			</div>
			<div id="description" class="p-2">
				<div class="flex flex-col gap-2 w-fit">
					<h4>{$translation.pages.settings.account_section.description()}</h4>
					<textarea
						disabled
						class="textarea p-2 w-96 max-w-[50vw]"
						rows="4"
						placeholder={this_user_profile_description}
						value={this_user_profile_description}
					/>
					<button class="btn btn-sm variant-ghost-secondary w-full"
						>{$translation.pages.settings.account_section.edit()}</button
					>
				</div>
			</div>
			<div class="p-2">
				<button class="btn variant-filled-error hover:variant-ghost-error"
					>{$translation.pages.settings.account_section.delete_account()}</button
				>
			</div>
			<div id="account-info text-primary-400-500-token">
				{this_user_last_edit} |
				{this_user_date_joined}
			</div>
		</div>
	</div>
{/if}
