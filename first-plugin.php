<?php

/**
 * Plugin Name: Primer Plugin Test
 * Plugin URI: https://ww.drkoopdev.com
 * Description: Un Primer Asombroso Plugin
 * Author: Dr.Koop
 * Version: 1.0
 * Author URI: https://ww.drkoopdev.com
 * License: GPL2+
 * Text Domain: drkoop,plugin
 * Requires at least: 6.1
 * Requires PHP: 5.6
 *
 */


 add_filter('the_content','addToEndofPost');


 function addToEndofPost($content){

    if( is_page() && is_main_query() ){
        return $content . '<p>Desde el contenido  de la paginaen el main DR KOOP</p>' ;
    }else{
        return $content . 'No estas en el main koop, pero estas en ealguna parte del blog..';
    }


 }