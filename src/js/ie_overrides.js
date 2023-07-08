// Special functions for Internet Explorer (6-11)

// file loaded at the end of the body element after the page load event

var dropDowns = getElementsByClassName("button", "dropdown-toggle");
var i;

// Replacement function for getElementsByClassName (not available on IE 6-8)
function getElementsByClassName(tagName, className) {
    var found = [];
    var j;
    var names;
    var k;
    var elements = document.getElementsByTagName(tagName);
    for (j = 0; j < elements.length; j++) {
        names = elements[j].className.split(" ");
        for (k = 0; k < names.length; k++) {
            if (names[k] === className) {found.push(elements[j]);}
        }
    }
    return found;
}

function showDropDown(event) {
    var menu = event.target.nextSibling;

    while (menu !== null && menu.tagName !== "DIV") {
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

for (i = 0; i < dropDowns.length; i++) {
    if(document.addEventListener) {
        dropDowns[i].addEventListener("click", showDropDown, false);
    } else {
        dropDowns[i].attachEvent("onclick", showDropDown);
    }
}