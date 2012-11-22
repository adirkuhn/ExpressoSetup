var oServiceCall = function()
{
    this.name = 'ServiceCall';
    this.viewCallCreate = false;
}

oServiceCall.prototype.destroy = function()
{
    $('#main').empty();
    return true;
}

oServiceCall.prototype.load = function()
{
    $('#main').empty();

    $('#main').append( API.render('ServiceCall/ServiceCall', {}));

    var ServiceCallView = $('div.ServiceCall');

    ServiceCallView.find('div.buttons-options button')
        .filter('button.new-call').button().click(function(evt){
            Module.newCall();
        }).end()
        .filter('button.schedule-downtime').button().click(function(evt){


        }).end();


    ServiceCallView.find('#outfitter').chosen();
    /*
    * Data Temporario
    */
    var data = [{id: 1, isAvaliable: true, gitec: 'Gitec', sr: 'Sr', loterico: 'Mr. Coutinho', number: 'HDO321654987', startDate: '23/10/2012', scheduleDate: '23/10/2012', category: 'Hardware', type: 'ADSL', ocurrence: 'Erro ao utilizar'} ,{id: 1, isAvaliable: true, gitec: 'Gitec', sr: 'Sr', loterico: 'Mr. Coutinho', number: 'HDO321654987', startDate: '23/10/2012', scheduleDate: '23/10/2012', category: 'Hardware', type: 'ADSL', ocurrence: 'Erro ao utilizar'}];

    ServiceCallView.find('#table-call').jqGrid({
        data: data,
        datatype: "local",
        height: 180,
        rowNum: 10,
        colNames:['id', 'isAvaliable',  'Código Gitec', 'Código SR', 'Código Lotérico', 'Número Chamado', 'Data de Abertura', 'Data de Agendamento', 'Categoria', 'Tipo', 'Ocorrência', 'Ações'],
        colModel:[
            {name:'id',index:'id',hidden:true},
            {name:'isAvaliable',index:'isAvaliable', hidden:true},

            {name:'gitec',index:'gitec', resizable: false, align: 'center', width:80 /*,hidden:(Profile.name == Profile.names.lottery || Profile.name == Profile.names.gitec || Profile.name == Profile.names.sr)*/},
            {name:'sr',index:'sr', resizable: false, align: 'center', width:80/*, hidden:(Profile.name == Profile.names.lottery || Profile.name == Profile.names.gitec || Profile.name == Profile.names.sr)*/},
            {name:'loterico',index:'loterico', resizable: false, align: 'center', width:100/*, hidden:(Profile.name == Profile.names.lottery)*/},

            {name:'number',index:'number', resizable: false, align: 'center', width:100},
            {name:'startDate',index:'startDate', resizable: false, align: 'center', width: 100},
            {name:'scheduleDate',index:'scheduleDate', resizable: false, align: 'center', width: 100},
            {name:'category',index:'category', resizable: false, align: 'center', width: 100},
            {name:'type',index:'type', resizable: false, align: 'center', width: 100},
            {name:'ocurrence',index:'ocurrence', resizable: false, align: 'center', width: 100},
            {name:'buttons', sortable:false, selectable:false, resizable: false, align: 'center', width:70}
        ],
        gridComplete: function(){
            var tb = $('#table-call');
            var data =  tb.jqGrid('getRowData');
            for(var i=0;i < data.length;i++){
                var button = "<span title=\"Detalhes\" class=\"icon-select button tiny grid-view "+ data[i].id +"\" />";
                button += data[i].isAvaliable != "false" ? "<span title=\"Avaliar\" class=\"icon-clipboard button tiny grid-view "+ data[i].id +"\" />" : "";
                tb.jqGrid('setRowData',data[i].id,{buttons:button});
            }

            tb.find('span.icon-select').button({
                icons: {
                    primary: "ui-icon-search"
                },
                text: false
            }).click(function(evt){
                    serviceCallDetail($(this).attr('class').match(/[0-9]+/g)[0]);
                });

            tb.find('span.icon-clipboard').button({
                icons: {
                    primary: "ui-icon-clipboard"
                },
                text: false
            }).click(function(evt){
                    evaluateTreatment($(this).attr('class').match(/[0-9]+/g)[0]);
                });
        },
        pager: '#plist-call',
        autowidth: true
    });
}

oServiceCall.prototype.newCall = function(id){
    var call = id;

    var html = API.render('ServiceCall/NewCall', {});


        Module.viewCallCreate = $('#new-call-view').append('<div title="Ajuda Novo Caixa M@il" class="new-call active"> <div>').find('.new-call.active').html(html).dialog({
            resizable: false,
            modal:true,
            autoOpen: false,
            width:655,
            dialogClass: 'custom-dialog ServiceCall',
            position: 'center',
            close: function(event, ui) {

            }
    });


    this.viewCallCreate.dialog('open');

    this.viewCallCreate.qtip({
        content: {
            style: {
                classes: 'new-call-helpers-qtip'
            },
            text: API.render('ServiceCall/NewCallHelpers'),
            title: {
                text: 'Ajuda',
                button: true
            }
        },
        position: {
            my: 'left center', // Use the corner...
            at: 'right center' // ...and opposite corner
        },
        show: {
            event: false, // Don't specify a show event...
            ready: true // ... but show the tooltip when ready
        },
        hide: false, // Don't specify a hide event either!
        style: {
            classes: 'ui-tooltip-shadow ui-tooltip-bootstrap'
        }
    });


}

Module = new oServiceCall();
