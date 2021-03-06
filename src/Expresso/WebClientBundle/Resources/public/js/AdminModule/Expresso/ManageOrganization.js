var oManageUser = function()
{
    this.name = 'ManageUser';
    this.resizeGrid = function() { setTimeout(function(){ $(main).find(".grid-list-organizations").setGridWidth($('#main').width())},200); }
}

oManageUser.prototype.load = function()
{
    var main = API.render( 'ExpressoAdmin/ManageOrganizationView' , [] , '#main' );

    $(main).find("#grid-list-organizations").jqGrid({
        url : API.URL+"/rest/Expresso/jqGridListUsers",
        datatype: "json",
        mtype: 'GET',
        colNames:[ 'Uid de login', 'Nome', 'Email', 'Ações'],
        colModel:[
            {name:'uid',index:'uid',  width:'10%', sortable:false},
            {name:'cn',index:'name', width:'50%',sortable:false },
            {name:'mail',index:'mail',  width:'30%',sortable:false},
            {index:'actions', width:'10%',sortable:false},
        ],
        rowList:[25,50,100],
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
        multiselect: false,
        width: $('#main').width() - 10,
        height: 'auto',
        pager:'#pager-grid-list-users'
    });

    $(window).bind('resize', this.resizeGrid);
    $("input:button, button").button();
    $(".create-organization-button").click(function(){
        $(".create-organization-panel").show().prev().hide();
    });

    $("#accordion").wijaccordion({
        header : "h5"
    });

    $(".type-select").chosen({
        no_results_text: "Não foi encontrado nenhum resultado"
    });

    new dgCidadesEstados({
        estado: $(".states-select")[0],
        cidade: $(".city-select")[0]
    });

    $(".states-select").chosen({
        no_results_text: "Não foi encontrado nenhum resultado"
    }).change(function(){
        setTimeout(function(){
            $(".city-select").trigger("liszt:updated");
        },300);
    });

    $(".city-select").chosen({
        no_results_text: "Não foi encontrado nenhum resultado"
    });

    $(".users-select").chosen({
        no_results_text: "Não foi encontrado nenhum resultado"
    });

    $(".form-button").click(function(){
        $('#main').toggle();
    }).filter(".save-organization").click(function(){

    });

}

oManageUser.prototype.jqgridFilterUser = function(  filter )
{
    $("#grid-list-organizations").setGridParam(
    {
        url:API.URL+"/rest/Expresso/jqGridListUsers/Filter/" + Base64.encode(filter)
    }
    ).trigger("reloadGrid");
}

oManageUser.prototype.destroy = function()
{
    $('#main').empty();
    $(window).unbind('resize', this.resizeGrid);
    return true;
}

Module = new oManageUser();
