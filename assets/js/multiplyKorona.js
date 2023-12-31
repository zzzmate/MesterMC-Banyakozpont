function multiplyKorona()
{
    var korona = document.getElementById('korona-ertek').value;
    var placeholder = document.getElementById('korona-placeholder');

    if(!isNaN(korona) && korona != "" && korona != 0)
    {
        placeholder.value = korona * 156;
    }
    else
    {
        placeholder.value = "";
    }
}