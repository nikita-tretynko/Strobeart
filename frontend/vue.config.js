const path = require("path");

module.exports = {
    configureWebpack: config => {
    },
    chainWebpack: config => {
        config.plugins.delete('pwa');
        config.plugins.delete('workbox');
    },
    publicPath: '/app',
    outputDir: path.resolve(__dirname, "../public/app"),
}
