document.addEventListener("DOMContentLoaded", () => {
    const screenWidth = window.innerWidth;
    const screenHeight = window.innerHeight;
    const gravity = 0.7; // Gravité
    const bounceFactor = 0.5; // Perte d'énergie au rebond
    const shapes = [];

    const size = 50;
    function createShape(x, y) {
        const shape = document.createElement("div");
        const isCircle = Math.random() > 0.5;

        if (isCircle) {
            shape.classList.add("shape", "circle");
            shape.style.backgroundColor = `hsl(${Math.random() * 360}, 100%, 50%)`;
        } else {
            shape.classList.add("shape", "square");
            shape.style.backgroundColor = `hsl(${Math.random() * 360}, 100%, 50%)`;
        }

        // Position initiale (utilise les coordonnées du clic ou une valeur par défaut)
        shape.style.left = `${x - size / 2}px`;
        shape.style.top = `${y - size / 2}px`;
        document.body.prepend(shape);

        // Retourne l'objet avec les propriétés physiques
        return {
            element: shape,
            x: x - size / 2,
            y: y - size / 2,
            size: size,
            velocityY: 0, // Vitesse verticale initiale
            isCircle: isCircle,
        };
    }

    function updateShape(shape) {
        // Appliquer la gravité
        shape.velocityY += gravity;
        shape.y += shape.velocityY;

        // Collision avec le sol
        if (shape.y + shape.size >= screenHeight) {
            shape.y = screenHeight - shape.size;
            shape.velocityY *= -bounceFactor; // Rebond

            // Si l'énergie est très faible, arrêter
            if (Math.abs(shape.velocityY) < 1) {
                shape.velocityY = 0;
            }
        }

        // Mise à jour de la position
        shape.element.style.top = `${shape.y}px`;
        shape.element.style.left = `${shape.x}px`;
    }

    function animate() {
        shapes.forEach(updateShape);
        requestAnimationFrame(animate);
    }

    // Ajout d'un événement de clic pour créer une nouvelle forme
    document.addEventListener("click", (event) => {
        const x = Math.random() * (screenWidth - size);
        const y = -50;
        const newShape = createShape(x, y);
        shapes.push(newShape);



    });

    // Démarrer l'animation
    animate();
});
