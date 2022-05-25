require('./bootstrap');

window.mdconvert = new showdown.Converter();

setUrl = function(url, title) {
    var url = (window.location.protocol + "//" + window.location.hostname + "/") + url;

    window.history.replaceState({
        archubPush: true
    }, document.title, url);

    if (title) document.title = title;
};

(function($) {
    $.fn.offClick = function(a) {
        var caller = $(this);
        $(document).bind("mouseup.offClick", function(event) {
            var target = $(event.target);
            if (!target.is(caller) && !target.is(caller.find('*')))
                if (typeof a == "function") a();
        });
    }
})(jQuery);
