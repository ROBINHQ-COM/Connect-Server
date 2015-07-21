/**
 * Created by bwubs on 21-07-15.
 */
$(document).ready(function ($) {
    var updateQuote = function (hooks) {
        $('.quote').text("Everything is operating normally. You have " + hooks + " webhooks registered")
    };
    $('.hooks-on').click(function (event) {
        var button = $(this).ladda();
        button.ladda("start");
        $.get('hooks/on').done(function (response) {
            console.log(response);
            button.ladda("stop");
            updateQuote(response.num_hooks);
        });
    });

    $('.hooks-off').click(function (event) {
        console.log(event);
        var button = $(this).ladda();
        button.ladda("start");
        $.get('hooks/off').done(function (response) {
            button.ladda("stop");
            updateQuote(response.num_hooks);
        });
    })
});