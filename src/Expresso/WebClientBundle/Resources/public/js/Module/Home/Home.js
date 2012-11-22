var oHome = function()
{
    this.name = 'Home';
    this.layout = '';
    this.Grid = '';
}

oHome.prototype.load = function()
{
    $('#main').empty();
    API.render("Home/Home", {}, '#main');
    
    $('.ui-home-layout-center').layout({
        applyDefaultStyles: true,
        north__closable:    false,
        north__resizable:   false,
        north__slidable:    false,
        east__closable:    false,
        center__size : $('.ui-home-layout-center').width()/2,
        east__size: $('.ui-home-layout-center').width()/2
    });

    Module.Grid = $("#imap-folder-table-messages").jqGrid({
        url : API.URL+"/rest/Mail/jqGridListMessages/Folder/INBOX",
        datatype: "json",
        mtype: 'GET',
        colNames:['#',' ', 'De', 'Assunto', 'Data'],
        colModel:[
            {name:'id',index:'msg_number', width:45, hidden:true, sortable:false},
            {name:'flags',index:'msg_number',edittype: 'image', width:60, formatter:flags2Class, sortable:false, title :false},
            {name:'from',index:'SORTFROM', width:100, sortable:true, formatter:fromFormatter},
            {name:'subject',index:'SORTSUBJECT', width:245, sortable:true, formatter:subjectFormatter},
            {name:'udate',index:'SORTARRIVAL', width:65, align:"center", sortable:true, formatter: date2Time}
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
        sortorder: "desc",
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
            Module.openMessage(id, null, function(){
                $("#"+id).find(".flags.unseen").removeClass("unseen").addClass("seen");    
            });
        }
    });

    $('.ui-home-layout').find("#billboard-pane, #poll-pane, #app-pane, #mail-pane").wijaccordion({
        //icons:null
        header: "h5"
    });
}

oHome.prototype.destroy = function()
{
    $('#main').empty();
    return true;
}

Module = new oHome();
