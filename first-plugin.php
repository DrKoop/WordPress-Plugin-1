<?php

/**
 * Plugin Name: Word Counter
 * Plugin URI: https://ww.drkoopdev.com
 * Description: Conteo de Palabras, número de caracteres, y el tiempo que llevaría leer una dentro de una Entrada de Blog.
 * Author: Dr.Koop
 * Version: 1.0
 * Author URI: https://ww.drkoopdev.com
 * License: GPL2+
 * Text Domain: wcpdomain
 * Domain Path: /languages
 * Requires at least: 6.1
 * Requires PHP: 5.6
 *
 */

 class WordCountAndTimePlugin {

    function __construct()
    {
        add_action('admin_menu', array($this, 'andminPage') );
        add_action( 'admin_init', array($this, 'settings'));
        //Esta sentencia, solo  EJECUTA cuando WORDPRESS lo requiera y cumpla sus condiciones
        add_filter('the_content' , array($this, 'ifWrap'));
        add_action('init', array($this,'translation') );
    }

    function translation(){
        load_plugin_textdomain('wcpdomain', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    function ifWrap($content){
        //Verifica si el contenido esta dentro de una ENTRADA DE BLOG y si esta en el main
        //LOS 3 checkbox de ajustes las entradas sean true , es decir su valor por defecto en este caso es "1", marcados por el usuario
        if( is_main_query() AND is_single()  AND 
        (   get_option('wcp_wordcount', '1') OR 
            get_option('wcp_character', '1') OR
            get_option('wcp_read_time', '1') 
        )){
            //Esta sentencia, EJECUTA en el momento la funcion
            return $this -> modifyBlogHTML($content);
          }else{
            return $content;
          }

    }


    function modifyBlogHTML($content){

        $html = '<h3>'. esc_html(get_option('wcp_headline', 'Post Statistics')) .'</h3><p>';

        //Obtener el resultado de las estadisticas del POST
        if( 
            get_option('wcp_wordcount', '1') OR 
            get_option('wcp_character', '1')
         ){
            //Cuenta el numero de palabras
            $wordCount = str_word_count(strip_tags($content));
        }

        if(get_option('wcp_wordcount', '1')){
            $html .= esc_html(__('This post has','wcpdomain')) . ' ' . $wordCount . ' ' . esc_html(__('words.','wcpdomain')) .'<br>';
        }

        if(get_option('wcp_character', '1')){
            //Cuenta el numero de carcateres dentro de un parrafo
            $numberOfCharacters = strlen(strip_tags($content));
            

            $html .= esc_html(__('This post has','wcpdomain')) . ' ' . $numberOfCharacters . ' ' . esc_html(__('characters.','wcpdomain')) .'<br>' ;
        }

        if(get_option('wcp_read_time', '1')){
            
            

            $html .= esc_html(__('This post will take about','wcpdomain')) . ' ' . round($wordCount/225) . ' ' . esc_html(__('minute(s) to read.','wcpdomain')) .'<br>';
        }

        $html .= '</p>';
        ///

        //Imprime el html dependiendo dependiendo la preferencia del usuario
        if( get_option('wcp_location', '0') == '0'){
            return $html . $content;
        }

        return $content . $html;
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
        add_options_page('Word Count Settings', __('Word Count Plugin', 'wcpdomain'), 'manage_options', 'word-count-settings', array($this, 'settingsHTML') );
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





