var oAPI = function ( URL , assectURL )
{
    this.URL = URL;
    this.assectURL = assectURL;
    this.folderModule = this.assectURL+'/js/Module/';
}

oAPI.prototype.render = function ( template , data , append  )
{
    try
    {
        if(append)
            return $(append).append( new EJS({url: this.URL + '/template/EJS/' + template + '.ejs'}).render( { data: data } ) );
        else
            return new EJS({url: this.URL + '/template/EJS/' + template + '.ejs'}).render( { data: data } );
    }
    catch(e)
    {
        Exception.logException( 'API.render'  , e ,  arguments )
        return false;
    }

}

oAPI.prototype.renderRestAppend = function ( template , restURL , append )
{
    var sucessREAD = function ( data , headers , xhr)
    {
        API.render( template  , data, append );
    }

    API.restGET( restURL ,  sucessREAD , function ()  {  Exception.log( 'API.renderRestAppend' , arguments ) } );
}

oAPI.prototype.loadModule = function ( module )
{
    if(Module.destroy())
    {
        if(typeof window['o'+module] !== 'function') //Evita uma nova requisição caso o modulo ja tenha sido carregado.
        {
            try
            {
                $.getScript(API.folderModule+module+'.js' , function(){Module.load()});
            }
            catch(e)
            {
                Exception.logException( 'API.loadModule'  , e ,  arguments )
                return false;
            }
        }
        else
        {
            Module = new window['o'+module]();
            Module.load();
        }
    }
    else
    {
        Exception.log( 'API.loadModule' , 'Falha ao destruir modulo');
    }
}

oAPI.prototype.restGET = function ( url , sucess , error )
{
    $.read( this.URL + '/rest/' + url , '' , sucess , error ?  error : function ()  {  Exception.log( 'API.restGET' , arguments ) } );
}

oAPI.prototype.restPUT = function ( url , data , sucess , error )
{
    $.update( this.URL + '/rest/' + url , data , sucess , error ?  error : function ()  {  Exception.log( 'API.restPUT' , arguments ) } );
}

oAPI.prototype.restDELETE = function ( url , sucess , error )
{
    $.destroy( this.URL + '/rest/' + url , '' , sucess , error ?  error : function ()  {  Exception.log( 'API.restDELETE' , arguments ) } );
}

oAPI.prototype.restPOST = function (url , data , sucess , error)
{
    $.create( this.URL + '/rest/' + url , data , sucess , error ?  error : function ()  {  Exception.log( 'API.restPOST' , arguments ) } );

}

//Fim API
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Implementa Cache do Navegador e Adiciona o Script ao debug
jQuery.extend({
    getScript: function(url, callback) {
        var head = document.getElementsByTagName("head")[0];
        var script = document.createElement("script");
        script.src = url;

        // Handle Script loading
        {
            var done = false;

            // Attach handlers for all browsers
            script.onload = script.onreadystatechange = function(){
                if ( !done && (!this.readyState ||
                    this.readyState == "loaded" || this.readyState == "complete") ) {
                    done = true;
                    if (callback)
                        callback();

                    // Handle memory leak in IE
                    script.onload = script.onreadystatechange = null;
                }
            };
        }

        head.appendChild(script);

        // We handle everything using the script element injection
        return undefined;
    }
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////