// Exécuter la fonction après que la page est complètement chargée
document.addEventListener('DOMContentLoaded', () => {
	barcode()
});
// Fonction qui met à jour la date et l'heure
function barcode() {
    let codepc = document.getElementById('codepc');
    let svgpc = document.getElementById('barcodePC');
    if (codepc && codepc.value != ""){
        console.log('codepc',codepc.value)
        JsBarcode("#barcodePC", codepc.value, {
            format: "CODE128", // Format du code-barres
            lineColor: "#000", // Couleur des lignes
            width: 2,          // Largeur de chaque barre
            height: 30,       // Hauteur du code-barres
            displayValue: true // Afficher la valeur sous le code-barres
        });
    }
    else {svgpc.style.display = 'none';
    }
}