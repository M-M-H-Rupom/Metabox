var frame;
;(function($){
    $(document).ready(function(){
        var image_url = $('#mb_image_url').val();
        if(image_url){
            $('#mb_image_container').html(`<img style='height:187px; width:300px;' src='${image_url}'>`);
        }
        $(".mb_date_piker").datepicker();
        $("#image_upload").on("click",function(){
            frame = wp.media({
                title :'Select image',
                button: {
                    text : 'Insert image'
                },
                multiple:false
            });
            frame.on('select',function(){
                var attachment= frame.state().get('selection').first().toJSON();
                $('#mb_image_id').val(attachment.id);
                $('#mb_image_url').val(attachment.url);
                $('#mb_image_container').html(`<img style='height:187px; width:300px;' src='${attachment.url}' />`);
                console.log(attachment);
            });
            frame.open()
            return false;
        })
    })   
})(jQuery);



    