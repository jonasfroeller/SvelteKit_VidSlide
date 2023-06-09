<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte';

	// Stores
	import { loginState, user } from '$store/account';

	// CSS-Framework/Library
	import { ProgressBar } from '@skeletonlabs/skeleton';
	import { modalStore } from '@skeletonlabs/skeleton';

	// Components
	import CommentPost from '$component/CommentPost.svelte';
	import Avatar from '$component/Avatar.svelte';
	import Popups from '$component/Popups.svelte';
	let popups; // popups in Popups.svelte

	// Backend API
	import Api from '$api/api';
	import { Route } from '$api/api-routes';

	// Props
	export let video_id;
	export let video_title;
	export let video_description;
	export let video_date_time_posted;
	export let video_tags = [];
	export let video_comments = []; // TODO: replies

	/* --- LOGIC --- */
	const openLoginModal = () => {
		modalStore.trigger(popups.signInUpForm);
	};

	let selectedBox = true; // true if comments are selected

	$: this_user_avatar = $user?.data?.USER_PROFILEPICTURE;
	$: this_user_username = $user?.data?.USER_USERNAME ?? '??';

	let COMMENT_MESSAGE = '';
	// $: updateCommentSection = video_comments.length;
	async function postComment() {
		if (COMMENT_MESSAGE.length === 0) {
			// TODO
		}

		let params = [
			{
				attribute_name: 'VS_VIDEO_ID',
				attribute: Route.REQUEST_METHOD.POST.medium.comment.params.VS_VIDEO_ID(video_id)
			},
			{
				attribute_name: 'COMMENT_MESSAGE',
				attribute: Route.REQUEST_METHOD.POST.medium.comment.params.COMMENT_MESSAGE(COMMENT_MESSAGE)
			}
		];

		const res = await Api.post(params, Route.REQUEST_METHOD.POST.medium.comment.root);

		video_comments.push({
			VS_VIDEO_ID: video_id,
			COMMENT_MESSAGE: COMMENT_MESSAGE
		});

		// updateCommentSection = video_comments.length; TODO
	}
</script>

{#key $translation}
	<Popups bind:this={popups} />
{/key}

<article class="flex flex-col gap-2">
	<div>
		<div class="btn-group variant-ghost-tertiary">
			<button
				class={selectedBox ? 'active' : ''}
				on:click={() => {
					selectedBox = true;
				}}>{$translation.InfoSection.comments()}</button
			>
			<button
				class={!selectedBox ? 'active' : ''}
				on:click={() => {
					selectedBox = false;
				}}>{$translation.InfoSection.description()}</button
			>
		</div>
	</div>
	<div
		class="border border-gray-500 rounded-md p-4 pt-0 w-[360px] h-[640px] overflow-auto relative"
	>
		{#if selectedBox}
			<div id="comments">
				<div class="top-0 pt-4 pb-2 w-full sticky bg-surface-50 dark:bg-surface-900 z-[888]">
					<h3>{$translation.InfoSection.comments_amount(video_comments?.length)}</h3>
					{#if $loginState}
						<div id="write-comment" class="flex items-center gap-2 p-2 justify-center">
							{#if this_user_avatar != undefined}
								<Avatar
									size="medium"
									comment_avatar={this_user_avatar}
									comment_username={this_user_username}
								/>
							{:else}
								<Avatar size="medium" comment_username={this_user_username} />
							{/if}
							<textarea
								bind:value={COMMENT_MESSAGE}
								class="textarea outline-none hover:outline-none input p-2 w-fit resize-none focus:border-secondary-400-500-token"
								rows="1"
								maxlength="200"
								placeholder={$translation.InfoSection.comment_placeholder()}
							/>
							<button
								on:click={() => postComment()}
								type="button"
								class="btn-icon variant-ghost-secondary"
							>
								<iconify-icon class="cursor-pointer text-2xl" icon="material-symbols:send" />
							</button>
						</div>
					{:else}
						<button
							class="underline hover:no-underline dark:text-tertiary-300 text-tertiary-500"
							on:click={openLoginModal}>{$translation.InfoSection.logIn()}</button
						>
						{$translation.InfoSection.logIn_text()}
					{/if}
					<hr />
				</div>
				<div class="flex flex-col gap-6">
					{#each video_comments as comment}
						<CommentPost
							comment_id={comment?.COMMENT_ID}
							comment_username={comment?.USER_USERNAME}
							comment_date_time_posted={comment?.COMMENT_DATETIMEPOSTED}
							comment_text={comment?.COMMENT_MESSAGE}
							comment_avatar={comment?.USER_PROFILEPICTURE}
							comment_likes={comment?.COMMENT_LIKES?.length}
							comment_dislikes={comment?.COMMENT_DISLIKES?.length}
							comment_replies={0}
						/>
						<!-- TODO: replies -->
					{:else}
						{$translation.InfoSection.be_the_first_comment()}
					{/each}
				</div>
			</div>
		{:else}
			<div id="info" class="pt-4">
				<h3>{video_title}</h3>
				<p class="text-md">
					{@html video_description}
				</p>
			</div>
		{/if}
	</div>
	<hr />
	<p class="text-xs text-primary-700 dark:text-primary-500 text-ellipsis">
		{#if video_date_time_posted != 'date and time loading...'}
			{$translation.InfoSection.dateTime(new Date(video_date_time_posted))}
		{/if}
	</p>
	<div id="video-tags" class="flex flex-wrap gap-1">
		{#each video_tags as tag}
			<span class="chip variant-ringed truncate">#{tag?.HASHTAG_NAME}</span>
		{:else}
			<span class="truncate text-xs text-primary-700 dark:text-primary-500"
				>{$translation.VideoResult.no_tags()}</span
			>
		{/each}
	</div>
</article>
