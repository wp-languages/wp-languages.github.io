$( function () {
    var packages = $( 'h3' );
    var timer;

    var search = $( 'input#search' );
    search.keyup( function () {
        clearTimeout( timer );
        var ms = 350; // milliseconds
        var needle = $( this ).val().toLowerCase(), show;
        timer = setTimeout( function () {
            packages.each( function () {
                show = $( this ).text().toLowerCase().indexOf( needle ) !== -1;
                $( this ).parent().toggle( show );
            } );
        }, ms );
    } ).focus();
    search.change( function () {
        window.location.hash = "!/" + $( this ).val().toLowerCase();
    } );

    $( window ).on( "hashchange", function () {
        var $input = $( 'input#search' );
        if ( window.location.hash.indexOf( "#!/" ) === 0 ) {
            $input.val( window.location.hash.replace( /#!\//, "" ).toLowerCase() );
            $input.trigger( "keyup" );
        }
        else {
            var $anchor = $( "h3[id='" + window.location.hash.replace( /^#/, "" ) + "']" );
            if ( $anchor.length !== $anchor.filter( ":visible" ).length ) {
                $input.val( "" ).trigger( "keyup" );
                $anchor.get( 0 ).scrollIntoView();
            }
        }
    } );

    $( window ).trigger( "hashchange" );
} );
