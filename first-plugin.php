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

 class WordCountAndTimePlugin {

    function __construct()
    {
        add_action('admin_menu', array($this, 'andminPage') );
        add_action( 'admin_init', array($this, 'settings'));
    }

    function settings(){

        add_settings_section('wcp_first_section', null, null, 'word-count-settings');

        add_settings_field( 'wcp_location','Display Location', array($this, 'locationHTML'), 'word-count-settings', 'wcp_first_section');

        //FUNCION MADRE donde sus argumentos, se comunican con todos los demas hooks nativos de WP 
        register_setting( 'wordcountplugin', 'wcp_location', array('sanitize_callback' => array($this, 'sanitizeLocation' ) ,'default', '0') );

        //

        add_settings_field( 'wcp_headline','Headline Text', array($this, 'headlineHTML'), 'word-count-settings', 'wcp_first_section');
        register_setting( 'wordcountplugin', 'wcp_headline', array('sanitize_callback' => 'sanitize_text_field', 'default', 'Post Statistics') );

        //

        add_settings_field( 'wcp_wordcount','Word Count', array($this, 'wordcountHTML'), 'word-count-settings', 'wcp_first_section');
        register_setting( 'wordcountplugin', 'wcp_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default', '1') );

        //
        add_settings_field( 'wcp_character','Character Count', array($this, 'characterHTML'), 'word-count-settings', 'wcp_first_section');
        register_setting( 'wordcountplugin', 'wcp_character', array('sanitize_callback' => 'sanitize_text_field', 'default', '1') );

        //
        add_settings_field( 'wcp_read_time','Read Time', array($this, 'readTimeHTML'), 'word-count-settings', 'wcp_first_section');
        register_setting( 'wordcountplugin', 'wcp_read_time', array('sanitize_callback' => 'sanitize_text_field', 'default', '1') );

    }

    function sanitizeLocation($input){

        if( $input != '0' AND $input != '1' ){

            add_settings_error('wcp_location','wcp_location_error', 'Display location must be either begining (1) or end (0).', 'error');
            return get_option('wcp_location');
        }
        
        return $input;
    }


    function readTimeHTML(){ ?>
        <input type="checkbox" name="wcp_read_time" value="1" <?php checked(get_option('wcp_read_time'), '1') ?> >
    <?php }

    function characterHTML(){ ?>
        <input type="checkbox" name="wcp_character" value="1" <?php checked(get_option('wcp_character'), '1') ?> >
    <?php }

    function wordcountHTML(){ ?>
        <input type="checkbox" name="wcp_wordcount" value="1" <?php checked(get_option('wcp_wordcount'), '1') ?> >
    <?php }

    function headlineHTML(){ ?>
        <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')) ?>">
    <?php }

    function locationHTML(){ ?>

        <select name="wcp_location" >
            <option value="0" <?php selected( get_option('wcp_location'), '0') ?> >Beginning of Post</option>
            <option value="1" <?php selected( get_option('wcp_location'), '1') ?>>End of Post</option>
        </select>

    <?php }

    function andminPage(){
        add_options_page('Word Count Settings', 'Word Count Plugin', 'manage_options', 'word-count-settings', array($this, 'settingsHTML') );
     }
    
    
     function settingsHTML(){ ?>
         <div class="wrap">
            <h1 class="">Word Count Settings</h1>
            <form action="options.php" method="POST">
                <?php
                    settings_fields('wordcountplugin');
                    do_settings_sections('word-count-settings');
                    submit_button();
                ?>
            </form>
         </div>
     <?php } 



 }

 $wordCountAndTimePlugin = new WordCountAndTimePlugin();





