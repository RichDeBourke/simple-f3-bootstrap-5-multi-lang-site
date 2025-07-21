// Special functions for Internet Explorer (9-11)

// file loaded at the end of the body element after the page load event

var dropDowns = document.getElementsByClassName("dropdown-toggle");
var i;

function showDropDown(event) {
    var menu = event.target.nextSibling;

    while (menu !== null && menu.tagName !== "UL") {
        menu = menu.nextSibling;
    }

    if (menu.className.match( /(?:^|\s)show(?!\S)/) ) {
        // remove
        menu.className = menu.className.replace( /(?:^|\s)show(?!\S)/g , "" );
    } else {
        // add
        menu.className = menu.className + " show";
    }
}

if (dropDowns) {
    for (i = 0; i < dropDowns.length; i++) {
        if(document.addEventListener) {
            dropDowns[i].addEventListener("click", showDropDown, false);
        } else {
            dropDowns[i].attachEvent("onclick", showDropDown);
        }
    }
}