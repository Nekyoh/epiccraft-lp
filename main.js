const routes = {
    "/": "pages/home.html",
    "/download": "pages/download.html",
    "404": "pages/404.html"
};

const handleLocation = async () => {
    const hash = window.location.hash.replace("#", "") || "/";
    const targetFile = routes[hash] || routes["404"];
    const finalUrl = `./${targetFile}`;
    const app = document.getElementById("app");

    try {
        const response = await fetch(finalUrl);
        if (!response.ok) throw new Error("Página não encontrada");

        const html = await response.text();

        // Reset da animação de FadeIn
        app.style.animation = 'none';
        app.offsetHeight; // trigger reflow
        app.style.animation = null; 

        app.innerHTML = html;

        // Scroll Suave para o topo
        window.scrollTo({ top: 0, behavior: 'smooth' });

    } catch (error) {
        console.error("Routing error:", error);
        // Fallback: Tenta carregar o arquivo 404 físico
        const errorResponse = await fetch(`./${routes["404"]}`).catch(() => null);
        if (errorResponse) {
            app.innerHTML = await errorResponse.text();
        } else {
            app.innerHTML = `<div class="text-center py-5"><h1>404</h1><p>Dimension not found.</p></div>`;
        }
    }
};

window.addEventListener("hashchange", handleLocation);
window.addEventListener("load", handleLocation);