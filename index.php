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
        if($nonce == '' && !wp_verify_nonce($nonce, $wp_nonce_action) && !current_user_can('edit_post',$post_id) && wp_is_post_autosave($post_id)){
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
        $fav_color = isset($_POST['fav_color']) ? $_POST['fav_color'] : array();
        $gender = isset($_POST['mb_gender']) ? $_POST['mb_gender'] : '';
        $country = isset($_POST['mb_citys']) ? $_POST['mb_citys'] : '';
        if($name == '' || $home == ''){
            return $post_id; 
        }
        update_post_meta($post_id, 'mb_name', $name);
        update_post_meta($post_id, 'mb_home', $home);
        update_post_meta($post_id, 'fav_color', $fav_color);
        update_post_meta($post_id, 'mb_gender', $gender);
        update_post_meta($post_id, 'mb_citys', $country);
    }
    function omb_add_metabox(){
        add_meta_box('new_mata', 'Information',array($this,'callback_for_metabox'),'page');
    }
    function callback_for_metabox($post){
        wp_nonce_field('wp_nonce_action','nonce_field_name');
        $name = get_post_meta($post->ID,'mb_name',true);
        $home = get_post_meta($post->ID,'mb_home',true);
        $fav_color = get_post_meta($post->ID,'fav_color',true);
        $p_gender = get_post_meta($post->ID,'mb_gender',true);
        $citys = get_post_meta($post->ID,'mb_citys',true);
        print_r($country);
        $colors = ['green','red','black','pink','yellow'];
        $genders = ['Male','Female','Others'];
        $cities = ['rangpur','dhaka','cumilla'];
        $data = <<<EOD
            <label for="">Name</label>
            <input type="text" name="mb_name" id="mb_name" value="{$name}">
            <label for="">Home</label>
            <input type="text" name="mb_home" id="mb_home" value="{$home}">
        EOD;
        foreach($colors as $color){
            $checked = '';
            if(in_array($color,$fav_color)){
                $checked = 'checked';
            }
            $data .= <<<EOD
            <label for="">{$color}</label>
            <input type="checkbox" name="fav_color[]" id="fav_color" value="{$color}" {$checked}> <br>
        EOD;
        }
        foreach($genders as $gender){
           $selected = '';
           if($p_gender == $gender){
            $selected = 'checked';
           }
            $data .= <<<EOD
            <label for="">{$gender}</label>
            <input type="radio" name="mb_gender" id="mb_gender" value="{$gender}" $selected> <br>
        EOD;
        }
        $data .="<select name='mb_citys' id='mb_citys' >";
        foreach($cities as $city){
            $selected='';
            if($citys == $city){
                $selected = 'selected';
            }
            $data .= <<<EOD
           <option value={$city} {$selected}>{$city} </option>
        EOD;
        }
        $data .="</select>";
        
        echo $data;
    }
}
new metabox();
?>
