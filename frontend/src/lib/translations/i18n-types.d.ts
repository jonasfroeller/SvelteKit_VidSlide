// This file was auto-generated by 'typesafe-i18n'. Any manual changes will be overwritten.
/* eslint-disable */
import type { BaseTranslation as BaseTranslationType, LocalizedString, RequiredParams } from 'typesafe-i18n'

export type BaseTranslation = BaseTranslationType
export type BaseLocale = 'de'

export type Locales =
	| 'de'
	| 'en'

export type Translation = RootTranslation

export type Translations = RootTranslation

type RootTranslation = {
	Error: {
		/**
		 * g​e​s​u​c​h​t​e​r​ ​P​f​a​d
		 */
		query: string
		/**
		 * z​u​r​ü​c​k​ ​z​u​r​ ​S​t​a​r​t​s​e​i​t​e
		 */
		back: string
	}
	LangSelect: {
		/**
		 * S​p​r​a​c​h​e
		 */
		lang: string
	}
	ThemeSelect: {
		/**
		 * S​t​i​l
		 */
		theme: string
		/**
		 * d​u​n​k​e​l
		 */
		theme_dark: string
		/**
		 * h​e​l​l
		 */
		theme_light: string
	}
	Sidebar: {
		/**
		 * S​t​a​r​t​s​e​i​t​e
		 */
		home: string
		/**
		 * S​u​c​h​e
		 */
		search: string
		/**
		 * K​o​n​t​o
		 */
		account: string
		/**
		 * E​i​n​s​t​e​l​l​u​n​g​e​n
		 */
		settings: string
	}
	Header: {
		/**
		 * A​n​m​e​l​d​e​n
		 */
		logIn: string
		/**
		 * {​u​s​e​r​n​a​m​e​|​}​ ​A​b​m​e​l​d​e​n
		 * @param {unknown} username
		 */
		logOut: RequiredParams<'username|'>
	}
	UploadVideo: {
		step_01: {
			/**
			 * W​ä​h​l​e​ ​e​i​n​ ​V​i​d​e​o​,​ ​d​a​s​s​ ​d​u​ ​v​e​r​ö​f​f​e​n​t​l​i​c​h​e​n​ ​w​i​l​l​s​t​:
			 */
			title: string
			/**
			 * W​ä​h​l​e​ ​e​i​n​ ​V​i​d​e​o​ ​a​u​s​,​ ​w​e​l​c​h​e​s​ ​d​u​ ​v​e​r​ö​f​f​e​n​t​l​i​c​h​e​n​ ​m​ö​c​h​t​e​s​t​.​ ​K​l​i​c​k​e​ ​a​u​f​ ​d​i​e​ ​D​r​o​p​z​o​n​e​ ​o​d​e​r​ ​z​i​e​h​e​ ​d​e​i​n​ ​V​i​d​e​o​ ​i​n​ ​d​i​e​ ​D​r​o​p​z​o​n​e​.
			 */
			video_dropzone: string
		}
		step_02: {
			/**
			 * U​m​ ​w​a​s​ ​g​e​h​t​ ​e​s​?​ ​S​c​h​r​e​i​b​e​ ​e​i​n​e​n​ ​T​i​t​e​l​,​ ​e​i​n​e​ ​B​e​s​c​h​r​e​i​b​u​n​g​ ​u​n​d​ ​f​ü​g​e​ ​e​v​e​n​t​u​e​l​l​ ​t​a​g​s​ ​h​i​n​z​u​:
			 */
			title: string
			/**
			 * T​i​t​e​l​:
			 */
			video_title_label: string
			/**
			 * D​e​i​n​ ​V​i​d​e​o​ ​T​i​t​e​l​.​.​.
			 */
			video_title: string
			/**
			 * B​e​s​c​h​r​e​i​b​u​n​g​:
			 */
			video_description_label: string
			/**
			 * D​e​i​n​e​ ​V​i​d​e​o​ ​B​e​s​c​h​r​e​i​b​u​n​g​.​.​.
			 */
			video_description: string
			/**
			 * T​a​g​s​:
			 */
			video_tags_label: string
			/**
			 * S​c​h​r​e​i​b​e​ ​#​t​a​g​ ​u​n​d​ ​d​r​ü​c​k​e​ ​E​N​T​E​R​ ​u​m​ ​e​i​n​e​n​ ​T​a​g​ ​h​i​n​z​u​z​u​f​ü​g​e​n​.​.​.
			 */
			video_tags: string
		}
		/**
		 * W​e​i​t​e​r
		 */
		next: string
		/**
		 * Z​u​r​ü​c​k
		 */
		back: string
		/**
		 * S​c​h​r​i​t​t
		 */
		step: string
		/**
		 * H​o​c​h​l​a​d​e​n
		 */
		complete: string
	}
	EditVideo: {
	}
	SignInUp: {
		/**
		 * R​e​g​i​s​t​r​i​e​r​e​n
		 */
		signUp: string
		/**
		 * A​n​m​e​l​d​e​n
		 */
		signIn: string
		/**
		 * U​s​e​r​n​a​m​e
		 */
		username: string
		/**
		 * P​a​s​s​w​o​r​t
		 */
		password: string
		/**
		 * P​a​s​s​w​o​r​t​ ​W​i​e​d​e​r​h​o​l​e​n
		 */
		password_retype: string
		/**
		 * A​b​b​r​e​c​h​e​n
		 */
		cancel: string
	}
	VideoResult: {
		/**
		 * {​{​k​e​i​n​e​ ​F​o​l​l​o​w​e​r​|​e​i​n​ ​F​o​l​l​o​w​e​r​|​?​?​ ​F​o​l​l​o​w​e​r​}​}
		 */
		follower: string
		/**
		 * {​{​k​e​i​n​e​ ​L​i​k​e​s​|​e​i​n​ ​L​i​k​e​|​?​?​ ​L​i​k​e​s​}​}
		 */
		likes: string
		/**
		 * {​{​k​e​i​n​e​ ​V​i​e​w​s​|​e​i​n​ ​V​i​e​w​|​?​?​ ​V​i​e​w​s​}​}
		 */
		views: string
		/**
		 * k​e​i​n​e​ ​t​a​g​s
		 */
		no_tags: string
		/**
		 * k​e​i​n​e​ ​E​i​n​g​a​b​e
		 */
		no_input: string
	}
	VideoSection: {
		/**
		 * {​{​k​e​i​n​e​ ​F​o​l​l​o​w​e​r​|​e​i​n​ ​F​o​l​l​o​w​e​r​|​?​?​ ​F​o​l​l​o​w​e​r​}​}
		 */
		follower: string
		/**
		 * {​{​k​e​i​n​e​ ​V​i​e​w​s​|​e​i​n​ ​V​i​e​w​|​?​?​ ​V​i​e​w​s​}​}
		 */
		views: string
		/**
		 * {​{​G​e​f​o​l​g​t​|​F​o​l​g​e​n​}​}
		 */
		subscribe: string
	}
	InfoSection: {
		/**
		 * B​e​s​c​h​r​e​i​b​u​n​g
		 */
		description: string
		/**
		 * K​o​m​m​e​n​t​a​r​e
		 */
		comments: string
		/**
		 * {​{​k​e​i​n​e​ ​K​o​m​m​e​n​t​a​r​e​|​e​i​n​ ​K​o​m​m​e​n​t​a​r​|​?​?​ ​K​o​m​m​e​n​t​a​r​e​}​}
		 */
		comments_amount: string
		/**
		 * g​e​p​o​s​t​e​t​ ​a​m​ ​{​0​|​v​i​d​e​o​D​a​t​e​}
		 * @param {unknown} 0
		 */
		dateTime: RequiredParams<'0|videoDate'>
		/**
		 * A​n​m​e​l​d​e​n
		 */
		logIn: string
		/**
		 * u​m​ ​e​i​n​e​n​ ​K​o​m​m​e​n​t​a​r​ ​z​u​ ​s​c​h​r​e​i​b​e​n
		 */
		logIn_text: string
		/**
		 * K​o​m​m​e​n​t​a​r​.​.​.
		 */
		comment_placeholder: string
		/**
		 * S​c​h​r​e​i​b​e​ ​j​e​t​z​t​ ​e​i​n​e​n​ ​K​o​m​m​e​n​t​a​r​ ​u​m​ ​d​e​r​ ​E​r​s​t​e​ ​z​u​ ​s​e​i​n​!
		 */
		be_the_first_comment: string
	}
	CommentPost: {
		/**
		 * {​0​|​c​o​m​m​e​n​t​D​a​t​e​}
		 * @param {unknown} 0
		 */
		dateTime: RequiredParams<'0|commentDate'>
		/**
		 * A​n​t​w​o​r​t​e​n
		 */
		reply: string
		/**
		 * {​r​e​p​l​i​e​s​|​0​}​ ​A​n​t​w​o​r​t​e​n
		 * @param {unknown} replies
		 */
		replies: RequiredParams<'replies|0'>
	}
	UserData: {
		/**
		 * {​{​k​e​i​n​e​ ​V​i​d​e​o​s​|​e​i​n​ ​V​i​d​e​o​|​?​?​ ​V​i​d​e​o​s​}​}
		 */
		videos: string
		/**
		 * {​{​k​e​i​n​e​ ​F​o​l​l​o​w​e​r​|​e​i​n​ ​F​o​l​l​o​w​e​r​|​?​?​ ​F​o​l​l​o​w​e​r​}​}
		 */
		follower: string
		/**
		 * {​{​k​e​i​n​e​ ​L​i​k​e​s​|​e​i​n​ ​L​i​k​e​|​?​?​ ​L​i​k​e​s​}​}
		 */
		likes: string
		/**
		 * {​{​k​e​i​n​e​ ​V​i​e​w​s​|​e​i​n​ ​V​i​e​w​|​?​?​ ​V​i​e​w​s​}​}
		 */
		views: string
		/**
		 * B​e​i​g​e​t​r​e​t​e​n​ ​a​m​ ​{​0​|​}
		 * @param {unknown} 0
		 */
		joined: RequiredParams<'0|'>
		/**
		 * B​e​a​r​b​e​i​t​e​n
		 */
		edit: string
		/**
		 * P​o​s​t​e​n
		 */
		post: string
		/**
		 * F​o​l​g​e​n
		 */
		follow: string
		/**
		 * M​e​h​r​ ​l​e​s​e​n
		 */
		more: string
		/**
		 * K​e​i​n​e​ ​B​e​s​c​h​r​e​i​b​u​n​g​!
		 */
		no_description: string
		/**
		 * P​r​o​f​i​l​b​i​l​d​ ​h​o​c​h​l​a​d​e​n
		 */
		upload_avatar: string
	}
	Popups: {
		modal: {
			confirmLogOut: {
				/**
				 * A​u​s​l​o​g​g​e​n​?
				 */
				title: string
				/**
				 * W​ü​r​d​e​n​ ​s​i​e​ ​d​i​e​ ​W​e​b​s​i​t​e​ ​g​e​r​n​e​ ​a​l​s​ ​G​a​s​t​ ​b​e​n​u​t​z​e​n​ ​u​n​d​ ​s​i​c​h​ ​a​u​s​l​o​g​g​e​n​?
				 */
				body: string
			}
			confirmLogIn: {
				/**
				 * E​i​n​l​o​g​g​e​n​?
				 */
				title: string
				/**
				 * W​ü​r​d​e​n​ ​s​i​e​ ​d​i​e​ ​W​e​b​s​i​t​e​ ​g​e​r​n​e​ ​a​n​g​e​m​e​l​d​e​t​ ​b​e​s​u​c​h​e​n​?
				 */
				body: string
			}
			confirmVideoDeletion: {
				/**
				 * V​i​d​e​o​ ​l​ö​s​c​h​e​n​?
				 */
				title: string
				/**
				 * D​a​s​ ​V​i​d​e​o​ ​w​i​r​d​ ​p​e​r​m​a​n​e​n​t​ ​g​e​l​ö​s​c​h​t​,​ ​b​i​s​t​ ​d​u​ ​d​i​r​ ​s​i​c​h​e​r​,​ ​d​a​s​s​ ​d​u​ ​e​s​ ​l​ö​s​c​h​e​n​ ​m​ö​c​h​t​e​s​t​?
				 */
				body: string
			}
			/**
			 * B​e​s​t​ä​t​i​g​e​n
			 */
			confirm: string
			/**
			 * A​b​b​r​e​c​h​e​n
			 */
			cancel: string
			/**
			 * S​c​h​l​i​e​ß​e​n
			 */
			close: string
		}
		toast: {
			/**
			 * K​o​n​f​i​g​u​r​a​t​i​o​n​ ​g​e​s​i​c​h​e​r​t​!
			 */
			configSaved_success: string
			/**
			 * U​R​L​ ​d​e​s​ ​V​i​d​e​o​s​ ​e​r​f​o​l​g​r​e​i​c​h​ ​k​o​p​i​e​r​t​!
			 */
			copiedURL_toClipboard_success: string
			/**
			 * U​s​e​r​n​a​m​e​ ​e​r​f​o​l​g​r​e​i​c​h​ ​k​o​p​i​e​r​t​!
			 */
			copiedUsername_toClipboard_success: string
			/**
			 * D​u​ ​w​u​r​d​e​s​t​ ​a​u​s​g​e​l​o​g​g​t​!
			 */
			loggedOut_success: string
			/**
			 * W​i​r​ ​l​o​g​g​e​n​ ​d​i​c​h​ ​a​u​s​!
			 */
			loggingOut_info: string
			/**
			 * E​i​n​l​o​g​g​e​n​ ​n​i​c​h​t​ ​e​r​f​o​l​g​r​e​i​c​h​!
			 */
			loggingIn_error: string
			/**
			 * E​i​n​g​a​b​e​ ​i​n​v​a​l​i​d​e​!
			 */
			loggingIn_warning: string
			/**
			 * D​u​ ​w​i​r​s​t​ ​i​n​ ​k​ü​r​z​e​ ​e​i​n​g​e​l​o​g​g​t​!
			 */
			loggingIn_info: string
			/**
			 * W​i​r​ ​r​e​g​i​s​t​r​i​e​r​e​n​ ​d​e​i​n​e​n​ ​A​c​c​o​u​n​t​!
			 */
			registering_account_info: string
			/**
			 * E​i​n​g​e​l​o​g​g​t​!
			 */
			loggedIn_success: string
			/**
			 * E​i​n​ ​n​e​u​e​r​ ​A​c​c​o​u​n​t​ ​w​u​r​d​e​ ​r​e​g​i​s​t​r​i​e​r​t​!
			 */
			registered_success: string
			/**
			 * V​i​d​e​o​ ​k​o​n​n​t​e​ ​n​i​c​h​t​ ​h​o​c​h​g​e​l​a​d​e​n​ ​w​e​r​d​e​n​!
			 */
			failed_to_upload_video: string
			/**
			 * V​i​d​e​o​ ​k​o​n​n​t​e​ ​n​i​c​h​t​ ​g​e​l​a​d​e​n​ ​w​e​r​d​e​n​!
			 */
			failed_to_fetch_video: string
			/**
			 * A​u​t​h​e​n​t​i​f​i​z​i​e​r​u​n​g​ ​f​e​h​l​g​e​s​c​h​l​a​g​e​n​!
			 */
			failed_to_authenticate: string
			/**
			 * D​a​t​e​i​t​y​p​ ​n​i​c​h​t​ ​e​r​l​a​u​b​t​!
			 */
			filetype_not_allowed: string
			/**
			 * D​u​ ​m​u​s​s​t​ ​e​i​n​g​e​l​o​g​g​t​ ​s​e​i​n​ ​u​m​ ​d​i​e​s​e​ ​F​u​n​k​t​i​o​n​ ​z​u​ ​b​e​n​u​t​z​e​n​!
			 */
			login_required: string
		}
	}
	SearchSection: {
		/**
		 * S​u​c​h​e​n
		 */
		search: string
		search_option: {
			/**
			 * S​u​c​h​e​ ​n​a​c​h​:
			 */
			subject: string
			/**
			 * K​a​t​e​g​o​r​i​e
			 */
			category: string
			/**
			 * U​s​e​r​n​a​m​e
			 */
			username: string
			/**
			 * T​i​t​e​l
			 */
			title: string
			/**
			 * S​o​r​t​i​e​r​e​ ​n​a​c​h​:
			 */
			sort_by: string
			/**
			 * D​a​t​u​m
			 */
			date: string
			/**
			 * A​u​f​r​u​f​e​n
			 */
			views: string
			/**
			 * L​i​k​e​s
			 */
			likes: string
			/**
			 * D​i​s​l​i​k​e​s
			 */
			dislikes: string
			/**
			 * {​{​k​e​i​n​e​ ​V​i​d​e​o​s​ ​g​e​f​u​n​d​e​n​|​e​i​n​ ​V​i​d​e​o​ ​g​e​f​u​n​d​e​n​|​?​?​ ​V​i​d​e​o​s​ ​g​e​f​u​n​d​e​n​}​}
			 */
			videos_found: string
		}
	}
	global: {
		/**
		 * l​a​d​e​n​.​.​.
		 */
		loading: string
	}
	pages: {
		settings: {
			site_section: {
				/**
				 * S​e​i​t​e​n​ ​E​i​n​s​t​e​l​l​u​n​g​e​n​:
				 */
				title: string
			}
			account_section: {
				/**
				 * A​c​c​o​u​n​t​ ​E​i​n​s​t​e​l​l​u​n​g​e​n​:
				 */
				title: string
				/**
				 * B​e​s​c​h​r​e​i​b​u​n​g​:
				 */
				description: string
				/**
				 * U​s​e​r​n​a​m​e​:
				 */
				username: string
				/**
				 * P​a​s​s​w​o​r​t​:
				 */
				password: string
				/**
				 * S​o​z​i​a​l​e​ ​M​e​d​i​e​n​:
				 */
				socials: string
				/**
				 * P​r​o​f​i​l​ ​B​i​l​d​:
				 */
				avatar: string
				/**
				 * B​e​a​r​b​e​i​t​e​n
				 */
				edit: string
				/**
				 * A​c​c​o​u​n​t​ ​l​ö​s​c​h​e​n
				 */
				delete_account: string
			}
		}
	}
}

export type TranslationFunctions = {
	Error: {
		/**
		 * gesuchter Pfad
		 */
		query: () => LocalizedString
		/**
		 * zurück zur Startseite
		 */
		back: () => LocalizedString
	}
	LangSelect: {
		/**
		 * Sprache
		 */
		lang: () => LocalizedString
	}
	ThemeSelect: {
		/**
		 * Stil
		 */
		theme: () => LocalizedString
		/**
		 * dunkel
		 */
		theme_dark: () => LocalizedString
		/**
		 * hell
		 */
		theme_light: () => LocalizedString
	}
	Sidebar: {
		/**
		 * Startseite
		 */
		home: () => LocalizedString
		/**
		 * Suche
		 */
		search: () => LocalizedString
		/**
		 * Konto
		 */
		account: () => LocalizedString
		/**
		 * Einstellungen
		 */
		settings: () => LocalizedString
	}
	Header: {
		/**
		 * Anmelden
		 */
		logIn: () => LocalizedString
		/**
		 * {username|} Abmelden
		 */
		logOut: (arg: { username: unknown }) => LocalizedString
	}
	UploadVideo: {
		step_01: {
			/**
			 * Wähle ein Video, dass du veröffentlichen willst:
			 */
			title: () => LocalizedString
			/**
			 * Wähle ein Video aus, welches du veröffentlichen möchtest. Klicke auf die Dropzone oder ziehe dein Video in die Dropzone.
			 */
			video_dropzone: () => LocalizedString
		}
		step_02: {
			/**
			 * Um was geht es? Schreibe einen Titel, eine Beschreibung und füge eventuell tags hinzu:
			 */
			title: () => LocalizedString
			/**
			 * Titel:
			 */
			video_title_label: () => LocalizedString
			/**
			 * Dein Video Titel...
			 */
			video_title: () => LocalizedString
			/**
			 * Beschreibung:
			 */
			video_description_label: () => LocalizedString
			/**
			 * Deine Video Beschreibung...
			 */
			video_description: () => LocalizedString
			/**
			 * Tags:
			 */
			video_tags_label: () => LocalizedString
			/**
			 * Schreibe #tag und drücke ENTER um einen Tag hinzuzufügen...
			 */
			video_tags: () => LocalizedString
		}
		/**
		 * Weiter
		 */
		next: () => LocalizedString
		/**
		 * Zurück
		 */
		back: () => LocalizedString
		/**
		 * Schritt
		 */
		step: () => LocalizedString
		/**
		 * Hochladen
		 */
		complete: () => LocalizedString
	}
	EditVideo: {
	}
	SignInUp: {
		/**
		 * Registrieren
		 */
		signUp: () => LocalizedString
		/**
		 * Anmelden
		 */
		signIn: () => LocalizedString
		/**
		 * Username
		 */
		username: () => LocalizedString
		/**
		 * Passwort
		 */
		password: () => LocalizedString
		/**
		 * Passwort Wiederholen
		 */
		password_retype: () => LocalizedString
		/**
		 * Abbrechen
		 */
		cancel: () => LocalizedString
	}
	VideoResult: {
		/**
		 * {{keine Follower|ein Follower|?? Follower}}
		 */
		follower: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{keine Likes|ein Like|?? Likes}}
		 */
		likes: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{keine Views|ein View|?? Views}}
		 */
		views: (arg0: number | string | boolean) => LocalizedString
		/**
		 * keine tags
		 */
		no_tags: () => LocalizedString
		/**
		 * keine Eingabe
		 */
		no_input: () => LocalizedString
	}
	VideoSection: {
		/**
		 * {{keine Follower|ein Follower|?? Follower}}
		 */
		follower: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{keine Views|ein View|?? Views}}
		 */
		views: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{Gefolgt|Folgen}}
		 */
		subscribe: (arg0: number | string | boolean) => LocalizedString
	}
	InfoSection: {
		/**
		 * Beschreibung
		 */
		description: () => LocalizedString
		/**
		 * Kommentare
		 */
		comments: () => LocalizedString
		/**
		 * {{keine Kommentare|ein Kommentar|?? Kommentare}}
		 */
		comments_amount: (arg0: number | string | boolean) => LocalizedString
		/**
		 * gepostet am {0|videoDate}
		 */
		dateTime: (arg0: unknown) => LocalizedString
		/**
		 * Anmelden
		 */
		logIn: () => LocalizedString
		/**
		 * um einen Kommentar zu schreiben
		 */
		logIn_text: () => LocalizedString
		/**
		 * Kommentar...
		 */
		comment_placeholder: () => LocalizedString
		/**
		 * Schreibe jetzt einen Kommentar um der Erste zu sein!
		 */
		be_the_first_comment: () => LocalizedString
	}
	CommentPost: {
		/**
		 * {0|commentDate}
		 */
		dateTime: (arg0: unknown) => LocalizedString
		/**
		 * Antworten
		 */
		reply: () => LocalizedString
		/**
		 * {replies|0} Antworten
		 */
		replies: (arg: { replies: unknown }) => LocalizedString
	}
	UserData: {
		/**
		 * {{keine Videos|ein Video|?? Videos}}
		 */
		videos: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{keine Follower|ein Follower|?? Follower}}
		 */
		follower: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{keine Likes|ein Like|?? Likes}}
		 */
		likes: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{keine Views|ein View|?? Views}}
		 */
		views: (arg0: number | string | boolean) => LocalizedString
		/**
		 * Beigetreten am {0|}
		 */
		joined: (arg0: unknown) => LocalizedString
		/**
		 * Bearbeiten
		 */
		edit: () => LocalizedString
		/**
		 * Posten
		 */
		post: () => LocalizedString
		/**
		 * Folgen
		 */
		follow: () => LocalizedString
		/**
		 * Mehr lesen
		 */
		more: () => LocalizedString
		/**
		 * Keine Beschreibung!
		 */
		no_description: () => LocalizedString
		/**
		 * Profilbild hochladen
		 */
		upload_avatar: () => LocalizedString
	}
	Popups: {
		modal: {
			confirmLogOut: {
				/**
				 * Ausloggen?
				 */
				title: () => LocalizedString
				/**
				 * Würden sie die Website gerne als Gast benutzen und sich ausloggen?
				 */
				body: () => LocalizedString
			}
			confirmLogIn: {
				/**
				 * Einloggen?
				 */
				title: () => LocalizedString
				/**
				 * Würden sie die Website gerne angemeldet besuchen?
				 */
				body: () => LocalizedString
			}
			confirmVideoDeletion: {
				/**
				 * Video löschen?
				 */
				title: () => LocalizedString
				/**
				 * Das Video wird permanent gelöscht, bist du dir sicher, dass du es löschen möchtest?
				 */
				body: () => LocalizedString
			}
			/**
			 * Bestätigen
			 */
			confirm: () => LocalizedString
			/**
			 * Abbrechen
			 */
			cancel: () => LocalizedString
			/**
			 * Schließen
			 */
			close: () => LocalizedString
		}
		toast: {
			/**
			 * Konfiguration gesichert!
			 */
			configSaved_success: () => LocalizedString
			/**
			 * URL des Videos erfolgreich kopiert!
			 */
			copiedURL_toClipboard_success: () => LocalizedString
			/**
			 * Username erfolgreich kopiert!
			 */
			copiedUsername_toClipboard_success: () => LocalizedString
			/**
			 * Du wurdest ausgeloggt!
			 */
			loggedOut_success: () => LocalizedString
			/**
			 * Wir loggen dich aus!
			 */
			loggingOut_info: () => LocalizedString
			/**
			 * Einloggen nicht erfolgreich!
			 */
			loggingIn_error: () => LocalizedString
			/**
			 * Eingabe invalide!
			 */
			loggingIn_warning: () => LocalizedString
			/**
			 * Du wirst in kürze eingeloggt!
			 */
			loggingIn_info: () => LocalizedString
			/**
			 * Wir registrieren deinen Account!
			 */
			registering_account_info: () => LocalizedString
			/**
			 * Eingeloggt!
			 */
			loggedIn_success: () => LocalizedString
			/**
			 * Ein neuer Account wurde registriert!
			 */
			registered_success: () => LocalizedString
			/**
			 * Video konnte nicht hochgeladen werden!
			 */
			failed_to_upload_video: () => LocalizedString
			/**
			 * Video konnte nicht geladen werden!
			 */
			failed_to_fetch_video: () => LocalizedString
			/**
			 * Authentifizierung fehlgeschlagen!
			 */
			failed_to_authenticate: () => LocalizedString
			/**
			 * Dateityp nicht erlaubt!
			 */
			filetype_not_allowed: () => LocalizedString
			/**
			 * Du musst eingeloggt sein um diese Funktion zu benutzen!
			 */
			login_required: () => LocalizedString
		}
	}
	SearchSection: {
		/**
		 * Suchen
		 */
		search: () => LocalizedString
		search_option: {
			/**
			 * Suche nach:
			 */
			subject: () => LocalizedString
			/**
			 * Kategorie
			 */
			category: () => LocalizedString
			/**
			 * Username
			 */
			username: () => LocalizedString
			/**
			 * Titel
			 */
			title: () => LocalizedString
			/**
			 * Sortiere nach:
			 */
			sort_by: () => LocalizedString
			/**
			 * Datum
			 */
			date: () => LocalizedString
			/**
			 * Aufrufen
			 */
			views: () => LocalizedString
			/**
			 * Likes
			 */
			likes: () => LocalizedString
			/**
			 * Dislikes
			 */
			dislikes: () => LocalizedString
			/**
			 * {{keine Videos gefunden|ein Video gefunden|?? Videos gefunden}}
			 */
			videos_found: (arg0: number | string | boolean) => LocalizedString
		}
	}
	global: {
		/**
		 * laden...
		 */
		loading: () => LocalizedString
	}
	pages: {
		settings: {
			site_section: {
				/**
				 * Seiten Einstellungen:
				 */
				title: () => LocalizedString
			}
			account_section: {
				/**
				 * Account Einstellungen:
				 */
				title: () => LocalizedString
				/**
				 * Beschreibung:
				 */
				description: () => LocalizedString
				/**
				 * Username:
				 */
				username: () => LocalizedString
				/**
				 * Passwort:
				 */
				password: () => LocalizedString
				/**
				 * Soziale Medien:
				 */
				socials: () => LocalizedString
				/**
				 * Profil Bild:
				 */
				avatar: () => LocalizedString
				/**
				 * Bearbeiten
				 */
				edit: () => LocalizedString
				/**
				 * Account löschen
				 */
				delete_account: () => LocalizedString
			}
		}
	}
}

export type Formatters = {
	'': (value: unknown) => unknown
	'0': (value: unknown) => unknown
	commentDate: (value: unknown) => unknown
	videoDate: (value: unknown) => unknown
}
