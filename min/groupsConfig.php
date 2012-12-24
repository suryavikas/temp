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
        'sq-js' => array('//squareofone//catalog//view//javascript//jquery//tabs.js',
                            '//squareofone/catalog//view//javascript//jquery//ui//external//jquery.cookie.js',
                            '//squareofone/catalog//view//javascript//jquery/nivo-slider//jquery.nivo.slider.pack.js',
                            '//squareofone//catalog//view//javascript//hoverIntent.js',
                            '//squareofone//catalog//view//javascript//superfish.js',
                            '//squareofone//catalog//view//javascript//sq-common.js'),
        'sq-css' => array(
                            '//squareofone//catalog//view//theme//squareofone//stylesheet//skin.css',
                            '//squareofone//catalog//view//theme//squareofone//stylesheet//alice-min.css',
                            '//squareofone//catalog/view//theme//squareofone//stylesheet//stylesheet.css',
                            '//squareofone//catalog//view//theme//squareofone//stylesheet//style.css',
                            '//squareofone//catalog//view//theme//squareofone//stylesheet//superfish.css',
                            '//squareofone//catalog//view//theme//squareofone//stylesheet//superfish-navbar.css')
        );