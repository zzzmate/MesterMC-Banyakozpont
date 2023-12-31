function openPopup(text) {
    document.getElementById('popupOverlay').classList.add('active');
    document.getElementById('popupText').textContent = text;
}

function closePopup() {
    document.getElementById('popupOverlay').classList.remove('active');
}

function loadFile(data) {
    fetch('../assets/txt/' + data)
        .then(response => response.text())
        .then(textContent => { openPopup(textContent); })
}