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
        add_action('save_post',array($this,'callback_save_post'));
        add_action('admin_enqueue_scripts', array($this,'metabox_css'));
        add_filter('user_contactmethods',array($this,'info_contact_method'));
    }
    function info_contact_method($methods){
        $methods['facebook'] = 'facebook';
        $methods['linkedin'] = 'linkedin';
        return $methods;
    }
    function metabox_css(){
        wp_enqueue_style( 'admin_css', plugin_dir_url( __FILE__ ).'/css/style.css');
        wp_enqueue_style( 'jquery_css','//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js');
        wp_enqueue_script( 'admin_js', plugin_dir_url( __FILE__ ).'/js/main.js', array('jquery','jquery-ui-datepicker'),time(), true );
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
        $image_id =isset($_POST['mb_image_id']) ? $_POST['mb_image_id'] : '';
        $image_url =isset($_POST['mb_image_url']) ? $_POST['mb_image_url'] : '';
        $post_page = $_POST['mb_post_page'];
        if($name == '' || $home == ''){
            return $post_id; 
        }
        update_post_meta($post_id, 'mb_name', $name);
        update_post_meta($post_id, 'mb_home', $home);
        update_post_meta($post_id, 'fav_color', $fav_color);
        update_post_meta($post_id, 'mb_gender', $gender);
        update_post_meta($post_id, 'mb_citys', $country);
        update_post_meta($post_id, 'mb_image_id', $image_id);
        update_post_meta($post_id, 'mb_image_url', $image_url);
        update_post_meta($post_id, 'mb_post_page', $post_page);
    }
    function omb_add_metabox(){
        add_meta_box('new_mata', 'Information',array($this,'callback_for_metabox'),'page');
    }
    function callback_for_metabox($post){
        wp_nonce_field('wp_nonce_action','nonce_field_name');
        $img_id = get_post_meta($post->ID, 'mb_image_id', true);
        $img_url = get_post_meta($post->ID, 'mb_image_url', true);
        $name = get_post_meta($post->ID,'mb_name',true);
        $home = get_post_meta($post->ID,'mb_home',true);
        $fav_color = get_post_meta($post->ID,'fav_color',true);
        $p_gender = get_post_meta($post->ID,'mb_gender',true);
        $citys = get_post_meta($post->ID,'mb_citys',true);
        $post_pages = get_post_meta($post->ID,'mb_post_page', true);
        print_r($post_pages);
        $colors = ['green','red','black','pink','yellow'];
        $genders = ['Male','Female','Others'];
        $cities = ['rangpur','dhaka','cumilla'];
        $data = <<<EOD
            <div class="info_name_label">
                <label for="">Name</label>
            </div>
            <div class="info_name_input">
                <input type="text" name="mb_name" id="mb_name" value="{$name}">
            </div>
            <div class="info_name_label">
                <label for="">Home</label>
            </div>
            <div class="info_name_input">
                <input type="text" name="mb_home" id="mb_home" value="{$home}">
            </div>
            <div class="info_name_input">
                <input type="text" class="mb_date_piker" id="mb_date_piker">
            </div>
            <label for="">Image</label>
            <div class="upload_image_part">
              <button id="image_upload"> Upload image </button> <br>
              <input type="hidden" name="mb_image_id" id="mb_image_id" value="{$img_id}">
              <input type="hidden" name="mb_image_url" id="mb_image_url" value="{$img_url}">
              <div id="mb_image_container">
              </div>
            </div>

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
        $args = array(
            'post_type' => 'page',
            'post_per_page' => -1,
        );
        $dropdown_list = '';
        $a_posts = new WP_Query($args);
        while($a_posts->have_posts()){
            $selected = '';
            $a_posts->the_post();
            if(get_the_ID() == $post_pages){
                $selected = 'selected';
            }
            
            $dropdown_list .= sprintf("<option value='%s' %s>%s</option>",get_the_ID(),$selected,get_the_title());
        }
        wp_reset_query();
        $data .= <<<EOD
            <select name="mb_post_page" id="mb_post_page">
                <option> Select </option>
                {$dropdown_list}
            </select>
        EOD;
        
        echo $data;
    }
}
new metabox();

?>
