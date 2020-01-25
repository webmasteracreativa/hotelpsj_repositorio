var $body = jQuery('body'),
    lastPanel = [];

var showSidepanel = function ($panel) {
    $body.css('overflow-y', 'hidden');
    var $backdrop = jQuery('<div class="slidePanel-wrapper"/>');
    $body.append($backdrop);
    $panel.css('transition', 'transform 0.6s ease');
    $panel.addClass('slidePanel-show').css('transform', 'translate3d(0%, 0px, 0px)');
    lastPanel.push($panel);
};

var hideSidepanel = function ($panel) {
    $panel.css('transform', $panel.hasClass('slidePanel-left') ? 'translate3d(-100%, 0px, 0px)' : 'translate3d(100%, 0px, 0px)');
    setTimeout(function () {
        $panel.removeClass('slidePanel-show');
    }, 600);
    var $backdrop = jQuery('.slidePanel-wrapper');
    if ($backdrop.length > 1) {
        jQuery($backdrop[0]).fadeOut(300, function () {
            jQuery(this).remove();
        });
    } else {
        $backdrop.fadeOut(300, function () {
            jQuery(this).remove();
        });
    }
    $body.css('overflow-y', 'auto');
    lastPanel.splice(lastPanel.length - 1, 1);
    $panel.trigger('sidePanel.hide');
};

jQuery(document).on("click", ".slidePanel-close", function (e) {
    hideSidepanel(jQuery(this).parents('.slidePanel'));
}).on("click", ".slidePanel-wrapper", function (e) {
    hideSidepanel(lastPanel[lastPanel.length - 1]);
});