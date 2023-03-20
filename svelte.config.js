import adapter from '@sveltejs/adapter-node';
import { vitePreprocess } from '@sveltejs/kit/vite';

/** @type {import('@sveltejs/kit').Config} */
const config = {
	kit: {
		adapter: adapter(),
		alias: {
			$main: "src",
			$translation: "src/lib/translations",
			$image: "src/lib/assets/imgs",
			$component: "src/lib/components",
			$store: "src/lib/stores",
			$script: "src/lib/scripts",
			$server: "src/lib/server",
		  },
		  prerender: {
			entries: ["/en", "/de"]
		  },
	},
	preprocess: vitePreprocess()
};

export default config;
