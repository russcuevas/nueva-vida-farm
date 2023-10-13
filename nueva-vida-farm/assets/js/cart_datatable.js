$(document).ready(function() {
        $('#example').DataTable( {
            "dom": '<lf<t>ip<l>',
            "ordering": true,
            "info": false,
            "paging": true,
            "bLengthChange": false,
            "searching": true,
        } );
    });