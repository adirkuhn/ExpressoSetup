$(document).ready(function(){
    var Layout = $("body").layout({
        applyDefaultStyles: true,
        north__size:        100,
        north__closable:    false,
        north__resizable:   false,
        north__slidable:    false,
        south__closable:    false,
        south__resizable:   false,
        south__slidable:    false,
        south__size:        40,
        center__maxSize : 0,
        livePaneResizing: true
        //showDebugMessages: true
    });

    API.renderRestAppend("Expresso/Menu", "Expresso/MenuTop", ".north-menu-images");

    $('li.north-menu-user.north-menu-day').html(' - ' + new Date().toString('dd/MM/yyyy'));

    API.loadModule('Home');
});