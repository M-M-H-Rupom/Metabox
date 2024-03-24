var frame;
;(function($){
    $(document).ready(function(){
        $(".mb_date_piker").datepicker();
        $("#image_upload").on("click",function(){
            frame = wp.media({
                title :'Select image',
                button: {
                    text : 'Insert image'
                },
                multiple:false
            })
            frame.open()
            return false;
        })
    })   
})(jQuery);



    