<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 *
 * See http://code.google.com/p/minify/wiki/CustomSource for other ideas
 **/

return array(
        'sq-js' => array(   'catalog//view//javascript//jquery//tabs.js',
                            'catalog//view//javascript//jquery//ui//external//jquery.cookie.js',
                            'catalog//view//javascript//jquery/nivo-slider//jquery.nivo.slider.pack.js',
                            'catalog//view//javascript//hoverIntent.js',
                            'catalog//view//javascript//superfish.js',
                            'catalog//view//javascript//sq-common.js'),
        'sq-css' => array(
                            'catalog//view//theme//squareofone//stylesheet//skin.css',
                            'catalog//view//theme//squareofone//stylesheet//alice-min.css',
                            'catalog/view//theme//squareofone//stylesheet//stylesheet.css',
                            'catalog//view//theme//squareofone//stylesheet//style.css',
                            'catalog//view//theme//squareofone//stylesheet//superfish.css',
                            'catalog//view//theme//squareofone//stylesheet//superfish-navbar.css')
        );