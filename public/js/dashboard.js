/**
 * dashboard.js
 *
 * AJAX post handling for the dashboard.
 *
 * @package FV Community News
 * @subpackage Admin Dashboard
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */

(function($, options)
{
    $(document).ready(function() {
        var fvcnAdminDashboard = new Dashboard();
    });


    var Dashboard = function()
    {
        var o = this;

        $('#fvcn-dashboard-recent-posts-list').find('.fvcn-row-actions a').click(function(e) {
            o.action = $(this).parent().attr('class');
            o.post = $(this).parents('.fvcn-post');
            o.params = o.parseQueryString($(this).attr('href'));

            if ('edit' !== o.action) {
                e.preventDefault();
            }

            if ('trash' === o.action) {
                $.ajax($(this).attr('href'));
            } else {
                o.submitAction();
            }

            if ('publish' === o.action) {
                o.setPostPublished();
            }
            else if ('unpublish' === o.action) {
                o.setPostPending();
            }
            else if ('spam' === o.action || 'trash' === o.action) {
                o.removePost();
            }
        });
    };

    /**
     * parseQueryString()
     *
     * @link http://stevenbenner.com/2010/03/javascript-regex-trick-parse-a-query-string-into-an-object/
     * @param query
     * @return object
     */
    Dashboard.prototype.parseQueryString = function(query)
    {
        var params = {};

        query.replace(
            new RegExp("([^?=&]+)(=([^&#]*))?", "g"),
            function($0, $1, $2, $3) {
                params[ $1 ] = $3;
            }
        );

        return params;
    };

    Dashboard.prototype.submitAction = function()
    {
        var data = this.params;
        data.fvcn_action = data.action;
        data.action = options.action;
        data.nonce = options.nonce;

        $.ajax({
            type: 'post',
            url: options.ajaxurl,
            data: data,
            dataType: 'json',
            success: function(response) {

            },
            error: function() {

            }
        });
    };

    Dashboard.prototype.setPostPending = function()
    {
        this.post.addClass('pending').removeClass('approved');
    };

    Dashboard.prototype.setPostPublished = function()
    {
        this.post.addClass('approved').removeClass('pending');
    };

    Dashboard.prototype.removePost = function()
    {
        this.post.hide();
    };

})(jQuery, FvCommunityNewsAdminDashboardOptions);
