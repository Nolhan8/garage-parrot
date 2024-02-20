document.addEventListener('DOMContentLoaded', function() {
    // Fonction de filtrage des voitures
    function filterCars() {
        // Récupération des valeurs des filtres
        const minPrice = parseFloat(document.getElementById('minPriceInput').value);
        const maxPrice = parseFloat(document.getElementById('maxPriceInput').value);
        const minKilometer = parseFloat(document.getElementById('minMileageInput').value);
        const maxKilometer = parseFloat(document.getElementById('maxMileageInput').value);
        const minYear = parseInt(document.getElementById('minYearInput').value);
        const maxYear = parseInt(document.getElementById('maxYearInput').value);

        // Sélection de toutes les cartes de voitures
        const carCards = document.querySelectorAll('.card');

        // Parcours des cartes de voitures pour les filtrer
        carCards.forEach(function(card) {
            const price = parseFloat(card.getAttribute('data-price'));
            const kilometer = parseFloat(card.getAttribute('data-kilometer'));
            const year = parseInt(card.getAttribute('data-year'));

            // Vérification des critères de filtrage
            const isVisible = price >= minPrice && price <= maxPrice &&
                              kilometer >= minKilometer && kilometer <= maxKilometer &&
                              year >= minYear && year <= maxYear;

            // Affichage ou masquage de la carte en fonction du résultat du filtrage
            if (isVisible) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Écouteurs d'événements pour les changements dans les filtres
    document.getElementById('minPriceInput').addEventListener('input', filterCars);
    document.getElementById('maxPriceInput').addEventListener('input', filterCars);
    document.getElementById('minMileageInput').addEventListener('input', filterCars);
    document.getElementById('maxMileageInput').addEventListener('input', filterCars);
    document.getElementById('minYearInput').addEventListener('input', filterCars);
    document.getElementById('maxYearInput').addEventListener('input', filterCars);

    // Appel initial de la fonction de filtrage
    filterCars();

    const filterToggleButton = document.getElementById('filter-toggle-btn');
    const filtersContent = document.querySelector('.filters-content');
    
    // Ajouter un écouteur d'événements sur le bouton de filtre
    filterToggleButton.addEventListener('click', function() {
        // Basculer l'affichage des filtres mobiles
        if (filtersContent.classList.contains('show-filters')) {
            filtersContent.classList.remove('show-filters');
        } else {
            filtersContent.classList.add('show-filters');
        }
    });
});
