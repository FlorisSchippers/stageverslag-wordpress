(function ($) {
    $(document).ready(function () {
        bindselectchange = function () {
            $(document).on('change', '#wpmf_upload_input_version', function (event) {
                jQuery('#wpmf_progress').hide();
                $('#wpmf_result').html(null);
                $('#wpmf_bar').width(0);
                $('#wpmf_percent').html('0%');
                if (typeof event.target.files[0] != "undefined") {
                    var type = event.target.files[0].type;
                    if (type.substr(0, 5) == 'image') {
                        var tmppath = URL.createObjectURL(event.target.files[0]);
                        $(".wpmf_img_replace").fadeIn("fast").attr('src', URL.createObjectURL(event.target.files[0]));
                    }
                } else {
                    $(".wpmf_img_replace").fadeOut("fast");
                }
            });
        }

        bindselectchange();
        
        function genFormReplace(id) {
            var form_replace = '<div class="replace_wrap">';
                form_replace += '<img class="wpmf_img_replace" src="">';
                form_replace += '<form id="wpmf_form_upload" method="post" action="'+ wpmflang.ajaxurl +'" enctype="multipart/form-data">';
                form_replace += '<input class="hide" type="file" name="wpmf_replace_file" id="wpmf_upload_input_version"><input type="submit" value="'+ wpmflang.replace +'" class="button-primary wpmf_submit_upload" id="submit-upload"/>';
                form_replace += '<input type="hidden" name="action" value="wpmf_replace_file">';
                form_replace += '<input type="hidden" name="post_selected" value="'+ id +'">';
                form_replace += '</form>';
                form_replace += '<div id="wpmf_progress"><div id="wpmf_bar"></div><div id="wpmf_percent">0%</div></div><div id="wpmf_result"></div>';
                form_replace += '</div>';
            return form_replace;
        }
        
        function replace_img() {
            var wpmf_bar = jQuery('#wpmf_bar');
            var wpmf_percent = jQuery('#wpmf_percent');
            var wpmf_result = jQuery('#wpmf_result');
            var wpmf_percentValue = '0%';

            jQuery('#wpmf_form_upload').ajaxForm({
                beforeUpload: function () {
                    wpmf_result.empty();
                    wpmf_percentValue = '0%';
                    wpmf_bar.width = wpmf_percentValue;
                    wpmf_percent.html(wpmf_percentValue);
                },
                uploadProgress: function (event, position, total, wpmf_percentComplete) {
                    jQuery('#wpmf_progress').show();
                    var wpmf_percentValue = wpmf_percentComplete + '%';
                    wpmf_bar.width(wpmf_percentValue);
                    wpmf_percent.html(wpmf_percentValue);
                },
                success: function () {
                    var wpmf_percentValue = '100%';
                    wpmf_bar.width(wpmf_percentValue);
                    wpmf_percent.html(wpmf_percentValue);
                },
                complete: function (xhr) {
                    jQuery('#wpmf_result').html(xhr.responseText);
                }
            });
        }
        
        var myreplaceForm = wp.media.view.AttachmentsBrowser;
        wp.media.view.AttachmentsBrowser = wp.media.view.AttachmentsBrowser.extend({
            createSingle: function() {
                myreplaceForm.prototype.createSingle.apply(this, arguments);
                var sidebar = this.sidebar;
                var single = this.options.selection.single();

                var form_replace = genFormReplace(single.id);
                if (wpmflang.wpmf_pagenow != 'upload.php') {
                    if (typeof wpmflang.override != 'undefined' && wpmflang.override == 1) {
                        $(sidebar.$el).find('.attachment-info').append(form_replace);
                    }
                }
                replace_img();
            }
        });
        
        var myReplace = wp.media.view.Modal;
        wp.media.view.Modal = wp.media.view.Modal.extend({
            open: function () {
                myReplace.prototype.open.apply(this, arguments);
                if (wpmflang.wpmf_pagenow == 'upload.php') {
                    if (typeof wpmflang.override != 'undefined' && wpmflang.override == 1) {
                        var attachmentID = $('.attachment-details').data('id');
                        var form_replace = genFormReplace(attachmentID);                               
                        $('.details').append(form_replace);
                        replace_img();
                    }
                }
            }
        });
    });
}(jQuery));