var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('src/Resources/public/js/')
    .addEntry('contao-pwa-bundle', './src/Resources/assets/js/pwa_public_build.js')
    .setPublicPath('/public/js/')
    .configureBabel(() => {}, {
        useBuiltIns: 'entry',
        corejs: 3
    })
    .disableSingleRuntimeChunk()
    .enableSourceMaps(!Encore.isProduction())
;

module.exports = Encore.getWebpackConfig();