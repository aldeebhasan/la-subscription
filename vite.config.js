import vue from '@vitejs/plugin-vue';

/** @type {import('vite').UserConfig} */
export default {
    plugins: [vue()],
    build: {
        assetsDir: '',
        rollupOptions: {
            input: ['resources/js/app.js'],
            output: {
                entryFileNames: '[name].js',
                chunkFileNames: '[name].js',
                assetFileNames: '[name].[ext]',
            },
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
};
