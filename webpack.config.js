var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/js/')
    .addEntry('contao-pwa-bundle', './assets/js/pwa_public_build.js')
    .setPublicPath('/public/js/')
    .configureBabel(() => {}, {
        useBuiltIns: 'entry',
        corejs: 3
    })
    .disableSingleRuntimeChunk()
    .enableSourceMaps(!Encore.isProduction())
;

module.exports = Encore.getWebpackConfig();