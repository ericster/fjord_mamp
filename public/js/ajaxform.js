$(function(){
    $("#showform").click(function(){
        $("#winpopup").dialog({
            draggable:true,
            modal: true,
            autoOpen: false,
            height:500,
            width:800,
            resizable: true,
            title:'Form Ajax',
            position:'center'
        });
        $("#winpopup").load($(this).attr('href'));
        $("#winpopup").dialog("open");
        
        return false;
    });
});