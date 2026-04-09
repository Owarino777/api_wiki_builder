<div class="catalog-toolbar catalog-toolbar-main">
    <input id="search" type="search" placeholder="Rechercher un item, une catégorie ou un slot">
    <select id="slot-filter"></select>
    <select id="tier-filter"></select>
    <select id="sort-filter"></select>
    <details class="filters-drawer">
        <summary class="btn ghost">Filtres avancés</summary>
        <div class="filters-drawer-panel">
            <div class="build-form-grid compact-filters">
                <div class="field">
                    <label for="score-filter">Score minimum</label>
                    <input id="score-filter" type="number" min="0" max="100" placeholder="70">
                </div>
                <div class="field">
                    <label for="modifier-filter">Type de mod</label>
                    <select id="modifier-filter"></select>
                </div>
                <div class="field">
                    <label>Filtres</label>
                    <div id="filter-note" class="summary">Catalogue complet</div>
                </div>
            </div>

            <div class="field">
                <label>Catégories</label>
                <div id="category-list" class="chips"></div>
            </div>
        </div>
    </details>
</div>
