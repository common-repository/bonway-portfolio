document.addEventListener("DOMContentLoaded", function() {
    jQuery('.js-bsp-select-btn').click(function() {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = jQuery(this);
        wp.media.editor.send.attachment = function(props, attachment) {
            console.log(attachment.url);
            jQuery(".js-image-thumb").attr('src', attachment.url);
            jQuery(".js-bsp-image").val(attachment.id);
            wp.media.editor.send.attachment = send_attachment_bkp;
        };
        wp.media.editor.open(button);
        return false;
    });

    jQuery(".js-colour-selector").each(function(i, val){
        jQuery(this).on("change", function() {
            var newVal = (jQuery(this)[0].value);
            jQuery(this).next().text(newVal);
        })
    });

    jQuery(".js-readmore").click(function(){
        var checked = $(this).is(":checked");

        if(checked) {
            $.each($(".js-readmore-req"), function(){
                $(this).removeClass("hide");
            });
            $.each($(".js-readmore-field"), function(){
                $(this).removeClass("readonly").prop("readonly", false).prop("required", true);
            });
        } else {
            $.each($(".js-readmore-req"), function(){
                $(this).addClass("hide");
            });
            $.each($(".js-readmore-field"), function(){
                $(this).addClass("readonly").prop("readonly", true).prop("required", false);
            });
        }
    });

    jQuery(".js-bonwaybsp-shortcode").on("click", function() {
        copyValue(jQuery(this));
    });

    jQuery(".js-bonwaybsp-copy-btn").on("click", function() {
        var sibling = jQuery(this).next();
        copyValue(sibling);
    });
});

function copyValue(el) {
    jQuery(el).focus();
    jQuery(el).select();
    jQuery(el).next().css("display", "block");
    jQuery(el).next().fadeTo("slow", 1, function() {
        setTimeout(function(){
            jQuery(el).next().fadeTo("slow", 0, function(){
                jQuery(el).next().css("display", "none");
            });
        }, 4000);
    });
    document.execCommand("copy");
}
