<?php
/**
 * Plugin Name: Meta box
 * Author: Rupom
 * Description: meta box
 * Version: 1.0
 *
 */

class metabox{
    function __construct(){
        add_action('admin_menu',array($this,'omb_add_metabox'));
        add_action( 'save_post',array($this,'callback_save_post'));
    }
    function is_secure($wp_nonce_action,$nonce_field_name,$post_id){
        $nonce = isset($_POST[$nonce_field_name]) ? $_POST[$nonce_field_name] : '';
        if($nonce == '' && !wp_verify_nonce($nonce, $wp_nonce_action) && !current_user_can('edit_post',$post_id) && !wp_is_post_autosave($post_id)){
            return false;
        }
        return true;
    }
    function callback_save_post($post_id){
        if(!$this->is_secure('wp_nonce_action','nonce_field_name',$post_id)){
            return $post_id;
        }
        $name = isset($_POST['mb_name']) ? $_POST['mb_name'] : '';
        $home = isset($_POST['mb_home']) ? $_POST['mb_home'] : '';
        if($name == '' || $home == ''){
            return $post_id; 
        }
        update_post_meta($post_id, 'mb_name', $name);
        update_post_meta($post_id, 'mb_home', $home);
    }
    function omb_add_metabox(){
        add_meta_box('new_mata', 'Information',array($this,'callback_for_metabox'),'page');
    }
    function callback_for_metabox($post){
        wp_nonce_field('wp_nonce_action','nonce_field_name');
        $name = get_post_meta($post->ID,'mb_name',true);
        $home = get_post_meta($post->ID,'mb_home',true);
        $data = <<<EOD
            <label for="">Name</label>
            <input type="text" name="mb_name" id="mb_name" value="{$name}">
            <label for="">Home</label>
            <input type="text" name="mb_home" id="mb_home" value="{$home}">
        EOD;
        echo $data;
    }
}
new metabox();
?>