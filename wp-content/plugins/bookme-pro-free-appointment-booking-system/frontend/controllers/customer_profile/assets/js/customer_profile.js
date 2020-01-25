(function($) {
    window.bookmeProCustomerProfile = function(options) {
       $('.bookme-pro-show-past').on('click', function(e) {
           e.preventDefault();
           var $self = $(this),
               $table = $self.prevAll('table.bookme-pro-appointments-table'),
               ladda = Ladda.create(this);
           ladda.start();
           $.get(options.ajaxurl, {action: 'bookme_pro_get_past_appointments', csrf_token : BookmeProL10n.csrf_token, columns: $table.data('columns'), custom_fields: $table.data('custom_fields'), page: $table.data('page') + 1 }, function () {
           }, 'json').done(function (resp) {
               ladda.stop();
               if (resp.data.more) {
                   $self.find('span.bookme-pro-label').html(BookmeProL10n.show_more);
               } else {
                   $self.remove();
               }
               if (resp.data.html) {
                   $table.find('tr.bookme-pro--no-appointments').remove();
                   $(resp.data.html).hide().appendTo($table).show('slow');
                   $table.data('page', $table.data('page') + 1 );
               }
           });
       });
    };
})(jQuery);