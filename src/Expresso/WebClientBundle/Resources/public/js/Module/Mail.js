function buildMessageButtons(content, id){
    //FLAGS BUTTON SET
    content.find(".message-flags").find(".message-important-flag").button({
        icons: {
            primary: "ui-icon-notice"
        },
        text: false
    }).click(function(){

        }).end().find(".message-unread-flag").button({
            icons: {
                primary: "ui-icon-mail-closed"
            },
            text: false
        }).click(function(){

        }).end().find(".message-spam-flag").button({
            icons: {
                primary: "ui-icon-alert"
            },
            text: false
        }).click(function(){

        }).end().find(".message-followup-flag").button({
            icons: {
                primary: "ui-icon-flag"
            },
            text: false
        });
    content.find(".message-flags").buttonset();

    //MESSAGES ACTION NEXT PREV BUTTON SET
    content.find(".messages-controll").find(".message-prev").button({
        icons: {
            primary: "ui-icon-carat-1-w"
        },
        text: false
    }).click(function(){

        }).next().button({
            icons: {
                primary: "ui-icon-carat-1-e"
            },
            text: false
        });
    content.find(".messages-controll").buttonset();

    //
    content.find(".message-forward").button({
        icons:{
            primary: "ui-icon-arrowreturnthick-1-e"
        },
        text : false
    });

    var teste = content.find(".message-reply-menu-content").clone();
    content.find(".message-reply-menu-content").remove();

    content.find(".button.message-reply").button({
        icons:{
            primary: "ui-icon-arrowreturnthick-1-w"
        },
        text : false
    }).next().button({
            icons:{
                primary: "ui-icon-triangle-1-s"
            },
            text : false
        }).parent().buttonset();

    content.find("span.message-reply").append(teste);

    content.find(".message-reply").next().menu({
        content : content.find(".message-reply-menu-content").html(),
        positionOpts: {
            posX: 'left',
            posY: 'bottom',
            offsetX: -140,
            offsetY: 0,
            directionH: 'right',
            directionV: 'down',
            detectH: true, // do horizontal collision detection
            detectV: true, // do vertical collision detection
            linkToFront: false
        },
        flyOut: true,
        showSpeed: 100,
        crumbDefaultText: '>'
    });


    content.find(".button.message-edit").button({
        icons:{
            primary: "ui-icon-pencil"
        },
        text : false
    });
    //BUTTON MORE AND TAGS
    content.find(".message-tags").button({
        icons: {
            primary: "ui-icon-tag",
            secondary: "ui-icon-triangle-1-s"
        },
        text: false
    }).next().next().button({
            icons: {
                secondary: "ui-icon-triangle-1-s"
            },
            text: true
        });

    content.find(".message-more").menu({
        content : content.find(".message-more-menu-content").html(),
        flyOut: true,
        positionOpts: {
            posX: 'left',
            posY: 'bottom',
            offsetX: -140,
            offsetY: 0,
            directionH: 'right',
            directionV: 'down',
            detectH: true, // do horizontal collision detection
            detectV: true, // do vertical collision detection
            linkToFront: false
        },
        showSpeed: 100,
        crumbDefaultText: '>'
    });

    content.find(".message-tags-menu-content ul li").remove();
    //REMOVE MESSAGE BUTTON
    content.find(".message-remove").button({
        icons: {
            primary: "ui-icon-trash"
        },
        text: false
    }).click(function(){
        Module.deleteMsg([id]);
        var index = $('li', Module.tabs).index(".ui-tabs-selected");
        Module.tabs.wijtabs('remove', index);
    });

    //PREFERENCES BUTTON
    content.find(".messages-preferences").button({
        icons: {
            primary: "ui-icon-gear",
            secondary: "ui-icon-triangle-1-s"
        },
        text: false
    }).css("width", "45px");

    //MESSAGE DETAILS 
    content.find(".message-hide-show-details").click(function(){
        $(this).find("span").toggle();
        content.find(".message-details").toggle("blind");
    }).hover(
        function(){
            $(this).addClass("message-detail-hover");
        },
        function(){
            $(this).removeClass("message-detail-hover");
        }
    );
}

var oMail = function()
{
    this.name = 'Mail';
    this.tabs = '';
    this.actualFolder = 'INBOX';
    this.actualTab = 0;
    this.Grid = '';
}

oMail.prototype.load = function()
{
    var clearGridTimer = "";

    var clearMarkersOnGrid = function (){
        Module.Grid.jqGrid('setGridWidth', $("#tabs").width()).trigger("reloadGrid");
    };

    var resize = function() {
        if (clearGridTimer) {
            window.clearTimeout(clearGridTimer);
        }
        clearGridTimer = window.setTimeout(clearMarkersOnGrid, 200);
    };

    $('#main').empty();
    API.render("Mail/Mail", {}, '#main');
    $('.ui-mail-layout').layout({
        applyDefaultStyles: true,
        center__maxSize : 0,
        center__contentSelector : 'ui-layout-mail-center',
        livePaneResizing: true,
        west__closable:false,
        west__resizable:   false,
        west__slidable:    false
    });

    //$(".ui-layout-mail-center").css("overflow", "none");
    Module.tabs = $("#tabs").wijtabs({
        scrollable: true,
        sortable: false,
        add : function(event, ui) {
            $(ui.tab).parents("li:first").find("span.tab-close:last").hover(function(){$(this).toggleClass("ui-icon-circle-close ui-icon-close");}, function(){$(this).toggleClass("ui-icon-circle-close ui-icon-close");})
        },
        tabTemplate: "<li role='tab' style='cursor: pointer;'><a href='#{href}'>#{label}</a><span class='ui-icon tab-close ui-icon-close' title='Fechar Aba'>Fechar Aba</span></li>",
        select : function(event, ui){
            Module.actualTab = ui.index;
        }
    });   

    $(window).resize(resize);
    $("#main .ui-mail-layout .ui-layout-center").resize(resize);

    Module.loadFolders(function(){
        $('[id="INBOX"] .folder:first').addClass("selected-folder");    
    });

    Module.Grid = $("#imap-folder-table-messages").jqGrid({
        url : API.URL+"/rest/Mail/jqGridListMessages/Folder/INBOX",
        datatype: "json",
        mtype: 'GET',
        colNames:['#',' ', 'De', 'Assunto', 'Data', 'Tamanho'],
        colModel:[
            {name:'id',index:'msg_number', width:45, hidden:true, sortable:false},
            {name:'flags',index:'msg_number',edittype: 'image', width:60, formatter:flags2Class, sortable:false, title :false},
            {name:'from',index:'SORTFROM', width:100, sortable:true, formatter:fromFormatter},
            {name:'subject',index:'SORTSUBJECT', width:245, sortable:true, formatter:subjectFormatter},
            {name:'udate',index:'SORTARRIVAL', width:65, align:"center", sortable:true, formatter: date2Time},
            {name:'size',index:'SORTSIZE', width:55, align:"center", sortable:true, formatter: bytes2Size}
        ],
        rowNum:25,
        jsonReader : {
            root:"rows",
            page: "page",
            total: "total",
            records: "records",
            repeatitems: false,
            id: "0"
        },
        //rowList:[10,25,50],
        pager: "#imap-folder-table-pager",
        sortorder: "desc",
        multiselect: true,
        autowidth: true,
        height : '100%',
        loadComplete: function(data) {
            $('.timable').each(function (i) {
                $(this).countdown({
                    since: new Date(parseInt(this.title)), 
                    significant: 1,
                    layout: 'h&aacute; {d<}{dn} {dl} {d>}{h<}{hn} {hl} {h>}{m<}{mn} {ml} {m>}{s<}{sn} {sl}{s>}', 
                    description: ' atr&aacute;s'
                });                 
            });
        },
        onSelectRow: function(id){
            if(!$(arguments.callee.arguments[2].srcElement).hasClass("cbox")){
                if(!$(arguments.callee.arguments[2].srcElement).children().hasClass("cbox")){
                    $("#"+id).addClass("ui-state-highlight").attr("aria-selected", "true").find(".cbox").attr("checked", "checked");
                    Module.openMessage(id, null, function(){
                        $("#"+id).find(".flags.unseen").removeClass("unseen").addClass("seen");    
                    });
                }
            }
        },
        gridComplete: function(){
            API.restGET("Mail/Folder/"+Module.actualFolder, function(data){
                Module.folderNumbers(data);
                Module.loadFolders(function(){
                    $('[id="'+Module.actualFolder+'"] .folder:first').addClass("selected-folder");    
                });
            });
        }
    });
    Module.Grid.jqGrid('navGrid','#imap-folder-table-pager',{edit:false,add:false,search:false,del:true, position : "left", delfunc : function(){
        //$(this).jqGrid('getGridParam','selarrrow');
        var msgs = $(this).jqGrid('getGridParam','selarrrow');
        Module.deleteMsg(msgs);
        Module.Grid.trigger("reloadGrid");
    }});
    Module.Grid.jqGrid('bindKeys', {"onEnter":function( rowid ) { alert("You enter a row with id:"+rowid)} } );

    API.restGET("Mail/Folder/INBOX", function(data){
        var tab = $("#tabs li:first");
        tab.find(".folder-tab-name").html(data[0].cn);
        tab.find(".folder-tab-new-msgs-number").html(data[0].status.Unseen);
        tab.find(".folder-tab-total-msgs-number").html(data[0].status.Messages);
    });
    
    $('#tabs ul.ui-tabs-nav:first span.ui-icon-circle-close.tab-close').live('click', function () {
        var index = $('li', Module.tabs).index($(this).parent());
        Module.tabs.wijtabs('remove', index);
    });
}

oMail.prototype.unorphanize = function(root, element) {
    var ok = false;
    var f = 0;
    for (var i=0; i<root.length; i++) {
        if (root[i].id == element.parentFolder) {
            element.children = new Array(); 
            root[i].children.push(element);
            return true;
        } else if (ok = Module.unorphanize(root[i].children, element)) {
            break;
        }
    }
    return ok;
}

oMail.prototype.countUnseenChildren = function(folder){
    if(folder.children.length){
        for(var i=0; i< folder.children.length; i++){
            if(folder.children[i].children.length)
                folder.children[i]['children_unseen'] = (folder.children[i]['children_unseen'] ? folder.children[i]['children_unseen'] : 0) + Module.countUnseenChildren(folder.children[i]);
            
            folder['children_unseen'] = (folder['children_unseen'] ? folder['children_unseen'] : 0)+ (folder.children[i]['children_unseen'] ? folder.children[i]['children_unseen'] : 0) + parseInt(folder.children[i].status.Unseen);            
        }
        return folder['children_unseen'];
    }else{
        return parseInt(folder.status.Unseen);
    }
}

oMail.prototype.changeFolder = function(folder){
    Module.actualFolder = folder;
    Module.Grid.jqGrid('setGridParam', {url : API.URL+"/rest/Mail/jqGridListMessages/Folder/"+folder}).trigger("reloadGrid");
    API.restGET("Mail/Folder/"+folder, function(data){
        Module.folderNumbers(data);
        Module.actualTab  = 0;
        Module.tabs.wijtabs('select', 0);
    });
}

oMail.prototype.folderNumbers = function(data){
    var tab = $("#tabs li:first");
    tab.find(".folder-tab-name").html(data[0].cn);
    tab.find(".folder-tab-new-msgs-number").html(data[0].status.Unseen);
    tab.find(".folder-tab-total-msgs-number").html(data[0].status.Messages);
}

oMail.prototype.openMessage = function(id, folder, callback){
    API.restGET("Mail/InfoMessage/Folder/"+(folder ? folder : Module.actualFolder)+"/UID/"+id, function(data){
        data['folder'] = folder ? folder : Module.actualFolder;
        folder = Base64.encode(data['folder']);
        folder = folder.replace(/=/gi, '');
        if(!$('[id="tabs-'+folder+'-'+id+'"]"').length){
            Module.tabs.wijtabs('add', '#tabs-'+folder+'-'+id, $.trim(limit(data.subject, 20)) == "" ? "(Sem assunto)" : limit(data.subject, 20));        
            $('[id="tabs-'+folder+'-'+id+'"]"').html(API.render("Mail/Email",data));
            buildMessageButtons($('[id="tabs-'+folder+'-'+id+'"]"'), id);
        }
        var index = $('li', Module.tabs).index(Module.tabs.find(".ui-tabs-nav").find('[href="#tabs-'+folder+'-'+id+'"]').parent());
        Module.actualTab  = index;
        Module.tabs.wijtabs('select', index);
        if(callback)
            callback();
    });
}

oMail.prototype.loadFolders = function(callback){
    API.restGET("Mail/ListFolders", function(data){
        var tree1 = new Array();
        var tree2 = new Array();
        var tree3 = new Array();
        for (var i=0; i<data.length; i++) {
            if (/^INBOX/.test(data[i].id)) {
                if (!Module.unorphanize(tree1, data[i])) {
                    data[i].children = new Array();
                    tree1.push(data[i]);
                }
            }
            else if (/^user/.test(data[i].id)) {
                if (!Module.unorphanize(tree2, data[i])) {
                    data[i].children = new Array();
                    tree2.push(data[i]);
                }
            }
            else if (/^local_messages/.test(data[i].id)) {
                if (!Module.unorphanize(tree3, data[i])) {
                    data[i].children = new Array();
                    tree3.push(data[i]);
                }
            }
        }
        for(var i =0; i<tree1.length; i++){
            Module.countUnseenChildren(tree1[i]);
        }
        for(var i =0; i<tree2.length; i++){
            Module.countUnseenChildren(tree2[i]);
        }
        for(var i =0; i<tree3.length; i++){
            Module.countUnseenChildren(tree3[i]);
        }
        var html = API.render("Mail/Folder", {folders: [tree1, tree2, tree3]});
        $('.imap-folders').html(html).children().treeview({
            animated: "fast"
        }).find(".folder:not(.head_folder)").unbind("click").click(function(){
            $(".mainfoldertree .folder.selected-folder").removeClass("selected-folder");
            Module.changeFolder($(this).parent().attr("id"));
            $(this).addClass("selected-folder");
        });

        if(callback){
            callback();
        }
    });
}

oMail.prototype.deleteMsg = function(msgs)
{
    $.each(msgs, function(index, value){
        API.restDELETE("Mail/Message/Folder/"+Module.actualFolder+"/UID/"+value, function(){
            Module.Grid.trigger("reloadGrid");
        }, function(){
            alert("Ocorreu algum problema, sentimos muito pelo incoveniente");
        });
    });
}

oMail.prototype.destroy = function()
{
    $('#tabs ul.ui-tabs-nav:first span.ui-icon-circle-close.tab-close').die();
    $("#main ui-layout-center").unbind("resize");
    $('#main').empty();
    $(window).unbind("resize");
    return true;
}

Module = new oMail();
