const openBtn = document.getElementById('openMenu');
const closeBtn = document.getElementById('closeMenu');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
const themeToggle = document.getElementById('themeToggle');
const localTemp = document.getElementById('localTemp');

function openSidebar() {
    sidebar.classList.add('open');
    overlay.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.style.display = 'none';
    document.body.style.overflow = '';
}

function setTheme(theme) {
    const isLight = theme === 'light';
    document.body.classList.toggle('light-theme', isLight);
    if (themeToggle) {
        themeToggle.textContent = isLight ? '🌙' : '☀️';
        themeToggle.title = isLight ? 'Mudar para modo escuro' : 'Mudar para modo claro';
    }
    localStorage.setItem('theme', theme);
}

function toggleTheme() {
    const current = document.body.classList.contains('light-theme') ? 'light' : 'dark';
    setTheme(current === 'light' ? 'dark' : 'light');
}

async function fetchLocalTemperature(lat, lon) {
    try {
        const response = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&temperature_unit=celsius`);
        if (!response.ok) throw new Error('Falha na API');
        const data = await response.json();
        if (data.current_weather && localTemp) {
            localTemp.textContent = `${Math.round(data.current_weather.temperature)}°C`;
            return true;
        }
    } catch (error) {
        // fallback handled no matter o motivo da falha
    }
    if (localTemp) localTemp.textContent = 'Temp indisponível';
    return false;
}

async function fetchLocationByIp() {
    try {
        const response = await fetch('https://ipapi.co/json/');
        if (!response.ok) throw new Error('IP fallback falhou');
        const data = await response.json();
        if (data.latitude && data.longitude) {
            return { lat: data.latitude, lon: data.longitude };
        }
    } catch (error) {
        // ignore, pois só é fallback
    }
    return null;
}

async function loadLocalTemperature() {
    if (!localTemp) return;
    localTemp.textContent = 'Buscando...';

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            async position => {
                const ok = await fetchLocalTemperature(position.coords.latitude, position.coords.longitude);
                if (!ok) {
                    const ipLocation = await fetchLocationByIp();
                    if (ipLocation) await fetchLocalTemperature(ipLocation.lat, ipLocation.lon);
                }
            },
            async () => {
                const ipLocation = await fetchLocationByIp();
                if (ipLocation) {
                    await fetchLocalTemperature(ipLocation.lat, ipLocation.lon);
                } else {
                    localTemp.textContent = 'Permissão negada';
                }
            },
            { timeout: 10000 }
        );
    } else {
        const ipLocation = await fetchLocationByIp();
        if (ipLocation) {
            await fetchLocalTemperature(ipLocation.lat, ipLocation.lon);
        } else {
            localTemp.textContent = 'GPS indisponível';
        }
    }
}

if (openBtn) openBtn.addEventListener('click', openSidebar);
if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
if (overlay) overlay.addEventListener('click', closeSidebar);
if (themeToggle) themeToggle.addEventListener('click', toggleTheme);

const savedTheme = localStorage.getItem('theme') || 'dark';
setTheme(savedTheme);
loadLocalTemperature();

// Auto-hide alerts
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 4000);
});
