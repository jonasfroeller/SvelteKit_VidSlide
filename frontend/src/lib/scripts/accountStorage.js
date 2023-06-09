// Scripts
import localStore from '$script/localStorage';

// JS-Framework/Library
import { browser } from '$app/environment';

// Stores
import { loginState, jwt, user } from '$store/account';

export default class accountCfg {
    /**
     * @param {any} cfg
     * @description save cfg to local storage
     */
    static async save(cfg) {
        if (browser) {
            return localStore.save('VidSlide-account', cfg);
        }
    }

    /**
     * @description load cfg from local storage
     * @returns {Promise<any>} cfg
     */
    static async load() {
        if (browser) {
            // load cfg from local storage
            return await localStore.load('VidSlide-account');
        }
    }

    static async clear() {
        if (browser) {
            // clear cfg from local storage
            loginState.set(false);
            jwt.set(null);
            user.set({
                data: {
                    VS_USER_ID: null,
                    USER_USERNAME: null,
                    USER_PROFILEPICTURE: null,
                    USER_PROFILEDESCRIPTION: null,
                    USER_DATETIMECREATED: null,
                    USER_LASTUPDATE: null,
                },
                socials: [],
                subscribed: [],
                subscribers: [],
                user_videos_liked: [],
                user_videos_disliked: [],
                user_comments_liked: [],
                user_comments_disliked: [],
                user_stats: {
                    videos: [],
                    likes: [],
                    views: [],
                    shares: []
                }
            });

            return await localStore.clear('VidSlide-account');
        }
    }
}