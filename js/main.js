var popup = null;
var popupHeader = null;
var popupContent = null;

var popup2 = null;
var popupHeader2 = null;
var popupContent2 = null;

function LoadPopup() {
  popup = document.getElementById('popup');
  popupHeader = document.getElementById('popup-header');
  popupContent = document.getElementById('popup-content');

  popup2 = document.getElementById('popup2');
  popupHeader2 = document.getElementById('popup-header2');
  popupContent2 = document.getElementById('popup-content2');
}

function OpenPopupMessage(header, content) {
  popup.style.display = "block";
  popupHeader.textContent = header;
  popupContent.innerHTML = content;
  popup.addEventListener('click', ClosePopup);

}

function OpenPopupScript(content, params = '') {
  popup.removeEventListener('click', ClosePopup);
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var script = document.createElement('script');
      script.innerHTML = this.responseText;
      document.body.appendChild(script);
    }
  };
  var url = "pages/popup/scripts/" + content;
  if (params !== '') {
    url = url + '?' + params;
  }
  xhttp.open("GET", url, true);
  xhttp.send();
}

function OpenPopupPage(header, content, params = '') {
  popup.removeEventListener('click', ClosePopup);
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      popup.style.display = "block";
      popupHeader.textContent = header;
      OpenPopupScript(content, params);
      popupContent.innerHTML = this.responseText;
    }
  };
  var url = "pages/popup/" + content;
  if (params !== '') {
    url = url + '?' + params;
  }
  xhttp.open("GET", url, true);
  xhttp.send();
}

function OpenPopupPage2(header, content, params = '') {
  popup2.removeEventListener('click', ClosePopup2);
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      popup2.style.display = "block";
      popupHeader2.textContent = header;
      popupContent2.innerHTML = this.responseText;
      OpenPopupScript(content, params);
    }
  };
  var url = "pages/popup/" + content;
  if (params !== '') {
    url = url + '?' + params;
  }
  xhttp.open("GET", url, true);
  xhttp.send();
}

function OpenPopupMessage2(header, content) {
  popup2.style.display = "block";
  popupHeader2.textContent = header;
  popupContent2.textContent = content;
  popup2.addEventListener('click', ClosePopup2);

}

function ClosePopup2() {
  popup2.style.display = "none";
}

function ClosePopup(id) {
  popup.style.display = "none";
  var xhttp = new XMLHttpRequest();
  var url = "pages/challengepopup.php";
  xhttp.target = '_blank';
  xhttp.open("POST", url, true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("id=" + id);
  setTimeout(function() {}, 50);
}

function onImageSelected(imageOption) {
  setRaceImage(imageOption.value);
}

function setRaceImage(imageName) {
  var img = document.getElementById("image");
  var imghead = document.getElementById("imageHead");

  img.src = 'img/races/' + imageName + '.png?003';
  imghead.src = 'img/races/' + imageName + 'Head.png?003';
}

function ToggleEquip(checkbox) {
  var cb = checkbox;
  var equips = document.getElementsByClassName("equip");
  for (let i = 0; i < equips.length; i++) {
    if (cb.checked) {
      equips[i].hidden = false;
    } else {
      equips[i].hidden = true;
    }
  }
}

function sortTable(wert) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("MarketContainer");
  switching = true;
  //Set the sorting direction to ascending:
  dir = "asc";
  /*Make a loop that will continue until
  no switching has been done:*/
  while (switching) {
    //start by saying: no switching is done:
    switching = false;
    rows = document.getElementsByClassName("MarketAnzeigenContainer");
    /*Loop through all table rows (except the
    first, which contains table headers):*/
    for (i = 0; i < (rows.length - 1); i++) {
      //start by saying there should be no switching:
      shouldSwitch = false;
      /*Get the two elements you want to compare,
      one from current row and one from the next:*/
      x = rows[i].getElementsByClassName(wert);

      y = rows[i + 1].getElementsByClassName(wert);

      /*check if the two rows should switch place,
      based on the direction, asc or desc:*/
      if (dir == "asc") {
        if (wert === "price") {
          if (Number(x[0].value) > Number(y[0].value)) {
            //if so, mark as a switch and break the loop:
            shouldSwitch = true;
            break;
          }
        } else {
          if (x[0].value.toUpperCase() > y[0].value.toUpperCase()) {
            //if so, mark as a switch and break the loop:
            shouldSwitch = true;
            break;
          }
        }
      } else if (dir == "desc") {
        if (wert === "price") {
          if (Number(x[0].value) < Number(y[0].value)) {
            //if so, mark as a switch and break the loop:
            shouldSwitch = true;
            break;
          }
        } else {
          if (x[0].value.toLowerCase() < y[0].value.toLowerCase()) {
            //if so, mark as a switch and break the loop:
            shouldSwitch = true;
            break;
          }
        }
      }
    }
    if (shouldSwitch) {
      /*If a switch has been marked, make the switch
      and mark that a switch has been done:*/
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      //Each time a switch is done, increase this count by 1:
      switchcount++;
    } else {
      /*If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again.*/
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}

function UpdatePrice(id) {
  const itemAmount = document.getElementById("amnt" + id);
  const itemPrice = document.getElementById("price" + id);
  const Amount = document.getElementById("amount" + id);
  const fruit = document.getElementById("fruit" + id);
  const berry = document.getElementById("berry" + id);

  if (id >= 52 && id <= 54 && fruit.value >= 52 && fruit.value <= 54 || id >= 55 && id <= 57 && fruit.value >= 55 && fruit.value <= 57) {
    const Price = (parseInt(Amount.value));
    itemPrice.value = Price;
    berry.textContent = (500 * Amount.value).toLocaleString() + " Berry";
    itemAmount.textContent = Price.toLocaleString() + "x";
  } else if (id >= 52 && id <= 54 && fruit.value >= 55 && fruit.value <= 57 || id >= 55 && id <= 57 && fruit.value >= 52 && fruit.value <= 54) {
    const Price = (parseInt(Amount.value));
    itemPrice.value = Price;
    berry.textContent = (500 * Amount.value).toLocaleString() + " Berry";
    itemAmount.textContent = Price.toLocaleString() + "x"
  }
}

function SelectionChange(id) {
  var itemimagecontainer = document.getElementById("itemimage");
  var itemnamecontainer = document.getElementById("itemContainerName");

  var itemimage = document.getElementById("itemimage" + id);
  var itemname = document.getElementById("itemname" + id);
  var itempremium = document.getElementById("itempremium" + id);
  var itemprice = document.getElementById("itemprice" + id);
  var itemtype = document.getElementById("itemtype" + id);
  var itemstatsid = document.getElementById("itemstatsid" + id);

  var berryGoldOfferImage = document.getElementById("offerSymbol");
  var berryGoldInstantImage = document.getElementById("instantSymbol");

  var offerInput = document.getElementById("offer");
  var buyInput = document.getElementById("priceinput");

  var offerRow = document.getElementById("offerRow");

  var inputField = document.getElementById("amount");
  inputField.value = '';

  itemimagecontainer.src = "img/items/" + itemimage.value + ".png";
  itemimagecontainer.title = itemname.value;
  itemimagecontainer.alt = itemname.value;
  itemnamecontainer.innerHTML = itemname.value;
  offerInput.placeholder = Math.round(Number(itemprice.value) / 2);
  if (itemstatsid.value >= 52 && itemstatsid.value <= 57)
    buyInput.placeholder = Math.round(Number(itemprice.value));
  else
    buyInput.placeholder = Math.round(Number(itemprice.value) / 2);

  if (itemtype.value != 3) {
    offerRow.hidden = true;
  } else {
    offerRow.hidden = false;
    itemtype.value != 2 ? inputField.value = '1' : inputField.value = '';
  }

  if (itempremium.value == 1) {
    berryGoldOfferImage.src = "img/offtopic/GoldSymbol.png";
    berryGoldInstantImage.src = "img/offtopic/GoldSymbol.png";
    berryGoldOfferImage.alt = "Gold";
    berryGoldOfferImage.title = "Gold";
    berryGoldInstantImage.alt = "Gold";
    berryGoldInstantImage.title = "Gold";
  } else {
    berryGoldOfferImage.src = "img/offtopic/BerrySymbol.png";
    berryGoldInstantImage.src = "img/offtopic/BerrySymbol.png";
    berryGoldOfferImage.alt = "Berry";
    berryGoldOfferImage.title = "Berry";
    berryGoldInstantImage.alt = "Berry";
    berryGoldInstantImage.title = "Berry";
  }
}

function FillInMax() {
  var itemSelect = document.getElementById("itemselect");
  var inputField = document.getElementById("amount");
  var itemtype = document.getElementById("itemtype" + itemSelect.value);
  var itemAmount = document.getElementById("itemamount" + itemSelect.value);
  if (itemtype.value != 3) {
    inputField.value = itemAmount.value;
  } else {
    inputField.value = '1';
  }
}

function Search() {
  var marketitems = document.getElementsByClassName("MarketAnzeigenContainer");
  var marketitemstypes = document.getElementsByClassName("type");
  var marketitemsnames = document.getElementsByClassName("name");
  var marketitemslevel = document.getElementsByClassName("level");
  var marketitemsbidding = document.getElementsByClassName("gebot");
  var marketitemsbuys = document.getElementsByClassName("sofortkauf");
  var marketitemsupgrades = document.getElementsByClassName("upgrade");
  var marketitemsseller = document.getElementsByClassName("seller");
  var marketitemsbuyer = document.getElementsByClassName("buyer");
  var marketitemgold = document.getElementsByClassName("gold");

  var searchfield = document.getElementById("itemname");
  var levelSelector = document.getElementById("neededitemlevel");
  var upgradeSelector = document.getElementById("itemlevel");
  var selector = document.getElementById("itemcategory");
  var onlyOwnItemsCheckbox = document.getElementById("eigeneItems");
  var onlyBiddingItemsCheckbox = document.getElementById("gebote");
  var onlyBuyingItemsCheckbox = document.getElementById("käufe");
  var showGoldItemsCheckbox = document.getElementById("goldItems");
  var playername = document.getElementById("playername");
  var playerid = document.getElementById("playerid");
  var category = selector.selectedIndex;

  if (parseInt(upgradeSelector.value) > 6) {
    upgradeSelector.value = 6;
  }

  if (parseInt(upgradeSelector.value) < 1) {
    upgradeSelector.value = 1;
  }

  for (let i = 0; i < marketitems.length; i++) {
    marketitems[i].style.display = 'flex';
  }

  for (let i = 0; i < marketitems.length; i++) {
    if (selector.selectedIndex == 4)
      category = 5;
    if (selector.selectedIndex == 5)
      category = 4;
    if (marketitemsnames[i].value.toUpperCase().includes(searchfield.value.toUpperCase()) && marketitemstypes[i].value == category || marketitemsnames[i].value.toUpperCase().includes(searchfield.value.toUpperCase()) && selector.selectedIndex == 0) {
      marketitems[i].style.display = 'flex';
    } else {
      marketitems[i].style.display = 'none';
    }
    if (parseInt(upgradeSelector.value) - 1 > parseInt(marketitemsupgrades[i].value)) {
      marketitems[i].style.display = 'none';
    }
    if (parseInt(levelSelector.value) > parseInt(marketitemslevel[i].value)) {
      marketitems[i].style.display = 'none';
    }
    if (onlyOwnItemsCheckbox.checked) {
      console.log(marketitemsseller)
      if (marketitemsseller[i].value !== playername.value && marketitemsbuyer[i].value !== playerid.value) {
        marketitems[i].style.display = 'none';
      }
    }
    if (!showGoldItemsCheckbox.checked) {
      if (marketitemgold[i].value == '1') {
        marketitems[i].style.display = 'none';
      }
    }
    if (onlyBiddingItemsCheckbox != null && onlyBiddingItemsCheckbox.checked) {
      if (marketitemsbidding[i].value == '0') {
        marketitems[i].style.display = 'none';
      }
    }
    if (onlyBuyingItemsCheckbox != null && onlyBuyingItemsCheckbox.checked) {
      if (marketitemsbuys[i].value != '0') {
        marketitems[i].style.display = 'none';
      }
    }
  }
}
var row;
var table;

function Start() {
  row = event.target;
  table = row.parentNode.parentNode;
}

function Dragover() {
  var e = event;
  e.preventDefault();

  let children = Array.from(e.target.parentNode.parentNode.children);

  if (e.target.parentNode.parentNode.parentElement === table) {
    if (children.indexOf(e.target.parentNode) > children.indexOf(row)) {
      e.target.parentNode.after(row);
    } else {
      e.target.parentNode.before(row);
    }
  }
}
if (window.history.replaceState) {
  window.history.replaceState(null, null, window.location.href);
}