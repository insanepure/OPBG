function SetCell(cell, url)
{
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            cell.innerHTML = this.responseText;
            cell.style.textAlign = 'center';
            cell.style.paddingTop = '10px';
        }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
}

function AddClanRank(tablename, cells)
{
    let table = document.getElementById(tablename);
    let rowIndex = table.rows.length - 1;
    let row = table.insertRow(rowIndex);
    row.style.height = 25 + 'px';
    for (let i = 0; i < cells; i++)
    {
        SetCell(row.insertCell(i), "../pages/clanmanage.php?row="+rowIndex+"&cell="+i);
    }
    SetCell(row.insertCell(cells), "../pages/clanmanage.php?table="+tablename+"&row="+rowIndex+"&cell="+cells+"&delete");
}

function RemoveTableRow(elem)
{
    let table = elem.parentNode.parentNode.parentNode.parentNode;
    let rowCount = table.rows.length;

    if(rowCount === 1) {
        alert('Cannot delete the last row');
        return;
    }

    // get the "<tr>" that is the parent of the clicked button
    let row = elem.parentNode.parentNode.parentNode;
    row.parentNode.removeChild(row); // remove the row
}