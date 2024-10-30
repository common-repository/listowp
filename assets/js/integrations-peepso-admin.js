jQuery(function ($) {
    let $listoProfile = $('input[name=listowp_profile_enable]');

    // Handle enable profile checkbox.
    $listoProfile.on('click', function () {
        let $fields = $(this).closest('.form-group').nextAll('.form-group');

        if (this.checked) {
            $fields.show();
        } else {
            $fields.hide();
        }
    });

    // Set initial visibility.
    $listoProfile.triggerHandler('click');
});
