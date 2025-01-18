function ChangeItem(element)
{
    var items = document.getElementsByClassName("item");
    for (let i=0; i<items.length; i++)
    {
        items[i].parentElement.parentElement.classList.remove('catGradient');
        items[i].parentElement.parentElement.classList.add('itemButton');
        element.parentElement.parentElement.classList.add('catGradient');
        element.parentElement.parentElement.classList.remove('itemButton');
    }
}
