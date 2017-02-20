jQuery(document).ready(function($) {
    $(document).on('change', '.method', function(e){method_select( $(this) );});

    function method_select(th) {
        var widget = th.closest('.widget-inside');
        var method = widget.find('.method option:selected').val();
        var recommend = widget.find('.recommend');
        var similar = widget.find('.similar');

        if(method == 'recommend') {
            recommend.show();
        } else {
            recommend.hide();
        }

        if(method == 'similarity') {
            similar.show();
        } else {
            similar.hide();
        }
    }
});