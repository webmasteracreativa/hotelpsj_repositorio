jQuery(function ($) {
    $('body').bookmeProHelp();
});

jQuery.fn.bookmeProHelp = function() {
    this.find('.help-block').each(function () {
        var $help  = jQuery(this),
            $label = $help.prev('label'),
            $icon  = jQuery('<span class="dashicons dashicons-editor-help bookme-pro-color-gray"></span>');

        $icon.attr('title',$help.text());
        $label.append($icon);
        $icon.tooltipster({
            theme: 'tooltipster-borderless',
            maxWidth: 300,
            delay: 100
        });
        $help.remove();
    });
};