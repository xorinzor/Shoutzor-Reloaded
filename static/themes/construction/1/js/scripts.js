/*
    Show latest tweets
*/
jQuery(function($) {
    $(".show-tweets").tweet({
        username: "mysocialsync",
        page: 1,
        count: 10,
        loading_text: "loading tweets..."
    }).bind("loaded", function() {
        var ul = $(this).find(".tweet_list");
        var ticker = function() {
            setTimeout(function() {
                ul.find('li:first').animate( {marginTop: '-4em'}, 500, function() {
                    $(this).detach().appendTo(ul).removeAttr('style');
                });
                ticker();
            }, 5000);
        };
        ticker();
    });
});


/*
    Progress bar
*/
var percentage = $('.progress .bar').attr("data-percentage");
$('.progress .bar').animate({width: (percentage)+'%'}, 1000);


/*
    Subscription form
*/
jQuery(document).ready(function() {

    $('.success-message').hide();
    $('.error-message').hide();

    $('.subscription-form-container form').submit(function() {
        var postdata = $('.subscription-form-container form').serialize();
        $.ajax({
            type: 'POST',
            url: 'https://www.mysocialsync.com/signup/',
            data: postdata,
            dataType: 'json',
            success: function(json) {
                if(json.valid == 0) {
                    $('.success-message').hide();
                    $('.error-message').hide();
                    $('.error-message').html(json.message);
                    $('.error-message').fadeIn();
                }
                else {
                    $('.error-message').hide();
                    $('.success-message').hide();
                    $('.subscription-form-container form').hide();
                    $('.success-message').html(json.message);
                    $('.success-message').fadeIn();
                }
            }
        });
        return false;
    });
});
