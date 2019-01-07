var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('src/Resources/public/js/')
    .addEntry('contao-pwa-bundle', './src/Resources/assets/js/contao-pwa-bundle.js')
    .addEntry('babel-polyfill', [
        '@babel/polyfill'
    ])
    .setPublicPath('/public/js/')
    .disableSingleRuntimeChunk()
    .configureBabel(function(babelConfig) {
        babelConfig.presets.push('minify');
    })
    .enableSourceMaps(!Encore.isProduction())
;

module.exports = Encore.getWebpackConfig();