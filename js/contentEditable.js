(function ($) {
    'use strict';
    /**
     * Makes contenteditable elements within a container generate change events.
     *
     * When you do, e.g. $obj.editable(), all the DOM elements with attribute contenteditable
     * that are children of the DOM element $obj will trigger a change event when their
     * contents is edited and changed.
     *
     * See: http://html5demos.com/contenteditable
     *
     * @return {*}
     */
    $.fn.editable = function () {
        this.on('focus', '[contenteditable]', function () {
            var $this = $(this);
            $this.data('beforeContentEdit', $this.html());
        });
        this.on('blur', '[contenteditable]', function () {
            var $this = $(this);
            if ($this.data('beforeContentEdit') !== $this.html()) {
                $this.removeData('beforeContentEdit').trigger('change');
            }
        });
        return this;
    };
}(jQuery));