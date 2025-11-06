window.addEventListener('load', function () {
    const preloader = document.getElementById('preloader');
    
    // Cambia el tiempo (en milisegundos) según lo que necesites
    const tiempoDeEspera = 500;

    setTimeout(() => {
        preloader.style.display = 'none';
    }, tiempoDeEspera);
});
