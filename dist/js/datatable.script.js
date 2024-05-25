(function ($) {
    "use strict";
var editor;
 $('#example').DataTable({
    dom: 'Bfrtip',buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
     responsive: true,
     lengthChange: true
 });
 
 //POS table
 $('#postable').DataTable({
     responsive: true,
     lengthChange: true
 });
  

})(jQuery);
