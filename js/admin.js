function SetCell(cell, url)
{
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() 
  {
    if (this.readyState == 4 && this.status == 200) 
    {
      cell.innerHTML = this.responseText;
      if(cell.children[0].className == 'select')
      {
        console.log(cell.children[0]);
        console.log('select2');
        $(cell.children[0]).select2();
      }
    }
  };
  xhttp.open("GET", url, true);
  xhttp.send();
}

function AddTableRow(tablename, cells)
{
  var table = document.getElementById(tablename);
  var rowIndex = table.rows.length - 1;
  var row = table.insertRow(-1);
  for (i = 0; i < cells; i++) 
  {
    var cell = row.insertCell(i);
    SetCell(cell, "../pages/adminJS.php?table="+tablename+"&row="+rowIndex+"&cell="+i);
  }
  SetCell(row.insertCell(cells), "../pages/adminJS.php?table="+tablename+"&row="+rowIndex+"&cell="+cells+"&delete");
}

function RemoveTableRow(elem)
{
  var table = elem.parentNode.parentNode.parentNode;
  var rowCount = table.rows.length;

  if(rowCount === 1) {
    alert('Cannot delete the last row');
    return;
  }

  // get the "<tr>" that is the parent of the clicked button
  var row = elem.parentNode.parentNode; 
  row.parentNode.removeChild(row); // remove the row
}

$(document).ready(function() {
  $('.select').select2();
});