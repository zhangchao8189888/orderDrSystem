$(function() {
    $(".edit_btn").click(function(){
        var id = $(this).attr('data-id');
        $("#modal-edit-event").modal({show:true});
    });
});