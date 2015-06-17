/*
 * select all checkboxes if "all" checkbox is checked
 * @param source checkbox
 * @param target checkboxes name
 */
function selectAll(source, name) {
    checkboxes = document.getElementsByName(name);
    for(var i=0; i < checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
    }
}

/*
 * list selected checkboxes
 * @param target checkboxes name
 */
function listSelected(name) {
    var ret = "";
    checkboxes = document.getElementsByName(name);
    for(var i=0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            var separator = (i !== checkboxes.length - 1) ? ":" : "";
            ret += checkboxes[i].value + separator;
        }
    }
    return ret;
}
