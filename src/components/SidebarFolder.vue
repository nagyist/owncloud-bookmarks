<!--
  - Copyright (c) 2020-2024. The Nextcloud Bookmarks contributors.
  -
  - This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
  -->

<template>
	<NcAppSidebar v-if="isActive"
		class="sidebar"
		:name="folder.title"
		:active.sync="activeTab"
		@close="onClose">
		<NcAppSidebarTab id="folder-details"
			:name="t('bookmarks', 'Details')"
			:order="0">
			<template #icon>
				<InformationVariantIcon :size="20" />
			</template>
			<h3>{{ t('bookmarks', 'Owner') }}</h3>
			<NcUserBubble :user="folder.userId" :display-name="folder.userDisplayName" />
			<h3>{{ t('bookmarks', 'Bookmarks') }}</h3>
			{{ bookmarkCount }}
		</NcAppSidebarTab>
		<NcAppSidebarTab v-if="isSharable"
			id="folder-sharing"
			:name="t('bookmarks', 'Sharing')"
			:order="1">
			<template #icon>
				<ShareVariantIcon :size="20" />
			</template>
			<div class="participant-select">
				<AccountIcon :size="20" :class="{'share__avatar': true }" />
				<NcSelect v-model="participant"
					label="displayName"
					track-by="user"
					class="participant-select__selection"
					:user-select="true"
					:options="participantSearchResults"
					:loading="isSearching"
					:placeholder="t('bookmarks', 'Select a user or group')"
					@option:selected="onAddShare"
					@search="onParticipantSearch" />
			</div>
			<div class="share" v-if="publicLinksAllowed">
				<LinkIcon :size="20" :class="{'share__avatar': true, active: publicLink }" />
				<h3 class="share__title">
					{{ t('bookmarks', 'Share link') }}
				</h3>
				<div class="share__privs">
					<div v-if="publicLink"
						v-tooltip="t('bookmarks', 'Reading allowed')"
						:aria-label="t('bookmarks', 'Reading allowed')">
						<EyeIcon :size="20"
							:fill-color="colorMainText" />
					</div>
				</div>
				<NcActions class="share__actions">
					<template v-if="publicLink">
						<NcActionButton @click="onCopyPublicLink">
							<template #icon>
								<ClipboardIcon :size="20" />
							</template>
							{{ t('bookmarks', 'Copy link') }}
						</NcActionButton>
						<NcActionButton icon="icon-clippy" @click="onCopyRssLink">
							<template #icon>
								<RssIcon :size="20" />
							</template>
							{{ t('bookmarks', 'Copy RSS feed') }}
						</NcActionButton>
						<NcActionSeparator />
						<NcActionButton @click="onDeletePublicLink">
							<template #icon>
								<DeleteIcon :size="20" />
							</template>
							{{ t('bookmarks', 'Delete link') }}
						</NcActionButton>
					</template>
					<NcActionButton v-else @click="onAddPublicLink">
						<template #icon>
							<PlusIcon :size="20" />
						</template>
						{{ t('bookmarks', 'Create public link') }}
					</NcActionButton>
				</NcActions>
			</div>
			<div v-for="share of shares" :key="share.id">
				<div class="share">
					<NcAvatar v-if="share.type < 2"
						:user="share.participant"
						class="share__avatar"
						:size="44" />
					<CircleIcon v-if="share.type === 7" :size="20" class="share_avatar" />
					<h3 class="share__title">
						{{ share.participantDisplayName }}
					</h3>
					<div class="share__privs">
						<div v-if="share.canShare"
							v-tooltip="t('bookmarks', 'Resharing allowed')"
							:aria-label="t('bookmarks','Resharing allowed')">
							<ShareAllIcon :size="20"
								:fill-color="colorMainText" />
						</div>
						<div v-if="share.canWrite"
							v-tooltip="t('bookmarks','Editing allowed')"
							:aria-label="t('bookmarks','Editing allowed')">
							<PencilIcon :size="20"
								:fill-color="colorMainText" />
						</div>
						<div v-tooltip="t('bookmarks','Reading allowed')"
							:aria-label="t('bookmarks', 'Reading allowed')">
							<EyeIcon :size="20"
								:fill-color="colorMainText" />
						</div>
					</div>
					<NcActions class="share__actions">
						<NcActionCheckbox :checked="share.canWrite" @update:checked="onEditShare(share.id, {canWrite: $event, canShare: share.canShare})">
							{{ t('bookmarks', 'Allow editing') }}
						</NcActionCheckbox>
						<NcActionCheckbox :checked="share.canShare" @update:checked="onEditShare(share.id, {canWrite: share.canWrite, canShare: $event})">
							{{ t('bookmarks', 'Allow resharing') }}
						</NcActionCheckbox>
						<NcActionButton icon="icon-delete" @click="onDeleteShare(share.id)">
							{{ t('bookmarks', 'Remove share') }}
						</NcActionButton>
					</NcActions>
				</div>
			</div>
		</NcAppSidebarTab>
	</NcAppSidebar>
</template>
<script>
import { NcAppSidebar, NcUserBubble, NcActionSeparator, NcActionCheckbox, NcActionButton, NcActions, NcSelect, NcAvatar, NcAppSidebarTab } from '@nextcloud/vue'
import { getCurrentUser } from '@nextcloud/auth'
import { generateUrl, generateOcsUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { actions, mutations } from '../store/index.js'
import { EyeIcon, PencilIcon, ShareAllIcon, ShareVariantIcon, InformationVariantIcon, ClipboardIcon, DeleteIcon, RssIcon, PlusIcon, LinkIcon, AccountIcon, CircleIcon } from './Icons.js'

export default {
	name: 'SidebarFolder',
	components: { NcAppSidebar, NcAppSidebarTab, NcAvatar, NcSelect, NcActionButton, NcActionCheckbox, NcActions, NcUserBubble, NcActionSeparator, EyeIcon, PencilIcon, ShareAllIcon, ShareVariantIcon, InformationVariantIcon, ClipboardIcon, RssIcon, PlusIcon, DeleteIcon, LinkIcon, AccountIcon, CircleIcon },
	data() {
		return {
			participantSearchTimeout: null,
			participantSearchResults: [],
			participant: null,
			isSearching: false,
			activeTab: '',
		}
	},
	computed: {
		isActive() {
			if (!this.$store.state.sidebar) return false
			return this.$store.state.sidebar.type === 'folder'
		},
		folder() {
			if (!this.isActive) return
			const folders = this.$store.getters.getFolder(this.$store.state.sidebar.id)
			const folder = folders[0]
			if (folder.userId === getCurrentUser()) {
				this.$store.dispatch(actions.LOAD_SHARES_OF_FOLDER, folder.id)
				this.$store.dispatch(actions.LOAD_PUBLIC_LINK, folder.id)
			}
			return folder
		},
		isOwner() {
			if (!this.folder) return
			const currentUser = getCurrentUser()
			return currentUser && this.folder.userId === currentUser.uid
		},
		permissions() {
			return this.$store.getters.getPermissionsForFolder(this.folder.id)
		},
		publicLinksAllowed() {
			return this.$store.state.settings.shareapi_allow_links
		},
		isSharable() {
			if (!this.folder) return
			return this.isOwner || (!this.isOwner && this.permissions.canShare)
		},
		isEditable() {
			if (!this.folder) return
			return this.isOwner || (!this.isOwner && this.permissions.canWrite)
		},
		shares() {
			if (!this.folder) return
			return this.$store.getters.getSharesOfFolder(this.folder.id)
		},
		token() {
			if (!this.folder) return
			return this.$store.getters.getTokenOfFolder(this.folder.id)
		},
		publicLink() {
			if (!this.token) return
			return window.location.origin + generateUrl('/apps/bookmarks/public/' + this.token)
		},
		rssLink() {
			return (
				window.location.origin
					+ generateUrl(
						'/apps/bookmarks/public/rest/v2/bookmark?'
							+ new URLSearchParams({
								format: 'rss',
								folder: this.folder.id,
								page: -1,
								token: this.token,
							}),
					).toString()
			)
		},
		bookmarkCount() {
			return this.$store.state.countsByFolder[this.folder.id]
		},
	},

	watch: {
		'$store.state.sidebar.tab'(newActiveTab) {
			this.activeTab = newActiveTab
		},
	},

	methods: {
		onClose() {
			this.$store.commit(mutations.SET_SIDEBAR, null)
		},
		async onAddPublicLink() {
			await this.$store.dispatch(actions.CREATE_PUBLIC_LINK, this.folder.id)
			this.onCopyPublicLink()
		},
		onCopyPublicLink() {
			navigator.clipboard.writeText(this.publicLink)
			this.$store.commit(mutations.SET_NOTIFICATION, t('bookmarks', 'Link copied'))
		},
		onCopyRssLink() {
			navigator.clipboard.writeText(this.rssLink)
			this.$store.commit(mutations.SET_NOTIFICATION, t('bookmarks', 'RSS feed copied'))
		},
		async onDeletePublicLink() {
			await this.$store.dispatch(actions.DELETE_PUBLIC_LINK, this.folder.id)
		},
		async onParticipantSearch(searchTerm) {
			if (!searchTerm) {
				return
			}
			if (this.participantSearchTimeout) {
				clearTimeout(this.participantSearchTimeout)
			}
			this.isSearching = true
			this.participantSearchTimeout = setTimeout(async () => {
				const {
					data: {
						ocs: {
							data,
							meta,
						},
					},
				} = await axios.get(generateOcsUrl('apps/files_sharing/api/v1', 1) + `/sharees?format=json&itemType=folder&search=${searchTerm}&lookup=false&perPage=200&shareType[]=0&shareType[]=1&shareType[]=7`)
				if (meta.status !== 'ok') {
					this.participantSearchResults = []
					return
				}
				const users = data.exact.users.concat(data.users)
				const groups = data.exact.groups.concat(data.groups)
				const circles = data.exact.circles.concat(data.circles)
				this.participantSearchResults = users.map(result => ({
					user: result.value.shareWith,
					displayName: result.label,
					icon: 'icon-user',
					isNoUser: false,
				})).concat(groups.map(result => ({
					user: result.value.shareWith,
					displayName: result.label,
					icon: 'icon-group',
					isNoUser: true,
				}))).concat(circles.map(result => ({
					user: result.value.shareWith,
					displayName: result.label,
					icon: 'icon-circle',
					isNoUser: true,
					isCircle: true,
				})))
				this.isSearching = false
			}, 300)
		},
		async onAddShare(user) {
			await this.$store.dispatch(actions.CREATE_SHARE, { folderId: this.folder.id, participant: user.user, type: user.isNoUser ? (user.isCircle ? 7 : 1) : 0 })
		},
		async onEditShare(shareId, { canWrite, canShare }) {
			await this.$store.dispatch(actions.EDIT_SHARE, { shareId, canWrite, canShare })
		},
		async onDeleteShare(shareId) {
			await this.$store.dispatch(actions.DELETE_SHARE, shareId)
		},
	},
}
</script>
<style>
	.sidebar h3 {
		font-size: 1em;
		font-weight: normal;
	}

	.participant-select {
		display: flex;
	}

	.participant-select__selection {
		flex: 1;
		margin-top: 5px !important;
	}

	.participant-select__actions {
		flex-grow: 0;
	}

	.share {
		display: flex;
		align-items: center;
		margin-top: 10px;
	}

	.share__avatar {
		flex-grow: 0;
		height: 44px;
		width: 44px;
	}

	.share__avatar.active {
		background-color: var(--color-primary-element-light);
		border-radius: 44px;
	}

	.share__privs {
		display: flex;
		width: 70px;
		flex-direction: row;
		justify-content: end;
	}

	.share__privs > * {
		padding-right: 5px;
	}

	.share__title {
		flex: 1;
		padding-left: 10px;
		margin: 0 !important;
	}

	.share__actions {
		flex-grow: 0;
	}
</style>
