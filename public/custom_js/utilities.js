function showElement(id) {
    var object = document.getElementById(id);
    object.classList.remove('hide');
}

function hideElement(id) {
    var object = document.getElementById(id);
    object.classList.add('hide');
}