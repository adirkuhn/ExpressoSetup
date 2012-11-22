var oHome = function()
{
    this.name = 'AdminHome';
}

oHome.prototype.load = function()
{
    $('#main').empty();
    $('#main').append('Modulo Admin Home Em Construção!')
}

oHome.prototype.destroy = function()
{
    $('#main').empty();
    return true;
}

Module = new oHome();
