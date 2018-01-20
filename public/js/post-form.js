/**!
 * Post Form
 *
 * @package FV Community News
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
(function($, options)
{
    'use strict';

    $(document).ready(function()
    {
        var FvCommunityNewsPostForm =
        {
            $form: $('.fvcn-post-form'),
            $submit: $('#fvcn_post_form_submit'),
            $loader: $('.fvcn-post-form-loader'),

            init: function()
            {
                var that = this;

                $.ajaxSetup ({
                    cache: false
                });

                $('.fvcn-post-form-new-post').ajaxForm({
                    url: options.ajaxurl,
                    data: {
                        nonce:  options.nonce,
                        action: options.action
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        that.showLoader();
                        that.disableSubmitButton();
                    },
                    success: function(response) {
                        that.hideLoader();
                        that.enableSubmitButton();
                        that.clearAllMessages();

                        if ('true' === response.success) {
                            if ('' === response.permalink || '' !== response.message) {
                                that.hideForm();
                                that.showResponseMessage(response.message);
                            } else {
                                window.location.href = response.permalink;
                            }
                        } else {
                            $.each(response.errors, function(field, error) {
                                that.displayMessage(field, error);
                            });
                        }
                    }
                });
            },

            showLoader: function()
            {
                this.$loader.show();
            },

            hideLoader: function()
            {
                this.$loader.hide();
            },

            disableSubmitButton: function()
            {
                this.$submit.attr('disabled', 'disabled');
            },

            enableSubmitButton: function()
            {
                this.$submit.removeAttr('disabled');
            },

            hideForm: function()
            {
                this.$form.slideUp('fast');
            },

            showResponseMessage: function(message)
            {
                this.$form.parent().append('<div class="fvcn-post-added">' + message + '</div>');
            },

            clearAllMessages: function()
            {
                $('.fvcn-error').html('');
            },

            displayMessage: function(field, message)
            {
                $('.' + field.replace(/_/g, '-') + ' > .fvcn-error').html('<ul class="fvcn-template-notice error"><li>' + message + '</li></ul>');
            }
        };

        if (FvCommunityNewsPostForm.$form.length) {
            FvCommunityNewsPostForm.init();
        }
    });
})(jQuery, FvCommunityNewsJavascript);
