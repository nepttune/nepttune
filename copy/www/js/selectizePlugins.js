Selectize.define( 'clear_selection', function ( options ) {
    var self = this;
    self.plugins.settings.dropdown_header = {
        title: 'Clear Selection'
    };
    this.require( 'dropdown_header' );
    self.setup = (function () {
        var original = self.setup;
        return function () {
            original.apply( this, arguments );
            this.$dropdown.on( 'mousedown', '.selectize-dropdown-header', function ( e ) {
                self.setValue( '' );
                self.close();
                self.blur();

                return false;
            });
        }
    })();
});

