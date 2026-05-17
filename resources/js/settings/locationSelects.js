const countryEl = document.getElementById('inputCountry');
const cityEl = document.getElementById('inputCity');

if (countryEl && cityEl) {
    countryEl.addEventListener('change', () => {
        const country = countryEl.value;
        cityEl.innerHTML = '<option value="">Cargando ciudades...</option>';
        if (country) {
            fetch(`/api/locations/${encodeURIComponent(country)}/cities`)
                .then(res => res.json())
                .then(cities => {
                    cityEl.innerHTML = '<option value="">Selecciona una ciudad</option>';
                    cities.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c;
                        opt.textContent = c;
                        cityEl.appendChild(opt);
                    });
                    cityEl.disabled = false;
                });
        } else {
            cityEl.innerHTML = '<option value="">Selecciona una ciudad</option>';
            cityEl.disabled = true;
        }
    });
}
