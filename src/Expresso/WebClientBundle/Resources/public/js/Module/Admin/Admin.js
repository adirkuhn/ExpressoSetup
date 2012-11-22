var oAdmin= function()
{
    this.name = 'Admin';
}

oAdmin.prototype.load = function()
{
    $('#main').empty();
    $('#main').append('Modulo Admin !!!!!!!!!!!!!!!!!!!!!!')
}

oAdmin.prototype.destroy = function()
{
    $('#main').empty();
    return true;
}

Module = new oAdmin();
