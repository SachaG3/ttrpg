{{-- resources/views/component/dice6.blade.php --}}
<div class="dice6-container">
    <style>
        .dice6-container {
            position: relative;
            width: 100%;  /* Ajustez pour la taille désirée ou mettez 100% si besoin */
            height: 50dvh; /* Ajustez la hauteur également */
            margin: 0 auto; /* Centre le composant si la largeur est fixe */
            border: 1px solid #ccc; /* Pour visualiser le conteneur (optionnel) */
            overflow: hidden; /* Évite les scrollbars si le contenu déborde */
        }

        /* Le canvas occupe tout l'espace de .dice6-canvas-wrapper */
        .dice6-canvas-wrapper {
            width: 100%;
            height: 100%;
        }

        /* Bouton de lancement du dé */
        .roll-button-dice6 {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 10px 20px;
            font-size: 16px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            z-index: 10; /* Au-dessus du canvas */
        }
        .roll-button-dice6:hover {
            background: #0056b3;
        }

        /* Zone d'affichage du résultat */
        .result-dice6 {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 18px;
            background: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
            z-index: 10; /* Au-dessus du canvas */
        }
    </style>

    {{-- Résultat et bouton --}}
    <div id="resultDice6" class="result-dice6">Résultat : -</div>
    <button id="rollButtonDice6" class="roll-button-dice6">Lancer le dé</button>

    {{-- Conteneur pour le canvas Three.js --}}
    <div class="dice6-canvas-wrapper" id="dice6CanvasWrapper"></div>
</div>

{{-- Inclusion de Three.js (version r128) --}}

<script>
    // Sélections et variables "globales" pour ce composant
    const resultElementDice6 = document.getElementById('resultDice6');
    const rollButtonDice6    = document.getElementById('rollButtonDice6');
    const containerDice6     = document.getElementById('dice6CanvasWrapper');

    // Dimensions du conteneur
    const containerWidthDice6  = containerDice6.clientWidth;
    const containerHeightDice6 = containerDice6.clientHeight;

    // Scène, caméra, rendu
    const sceneDice6 = new THREE.Scene();
    const cameraDice6 = new THREE.PerspectiveCamera(
        75,
        containerWidthDice6 / containerHeightDice6,
        0.1,
        1000
    );
    const rendererDice6 = new THREE.WebGLRenderer({ antialias: true });

    rendererDice6.setSize(containerWidthDice6, containerHeightDice6);
    rendererDice6.setPixelRatio(window.devicePixelRatio);
    containerDice6.appendChild(rendererDice6.domElement);

    // Lumières
    const lightDice6 = new THREE.AmbientLight(0xffffff, 0.7);
    sceneDice6.add(lightDice6);
    const directionalLightDice6 = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLightDice6.position.set(5, 5, 5);
    sceneDice6.add(directionalLightDice6);

    // Création du dé (Cube)
    const geometryDice6 = new THREE.BoxGeometry(1, 1, 1);
    const loaderDice6 = new THREE.TextureLoader();

    // Matériaux (faces)
    const materialsDice6 = [
        new THREE.MeshBasicMaterial({ map: loaderDice6.load('/img/5c.png') }), // Face +X → 5
        new THREE.MeshBasicMaterial({ map: loaderDice6.load('/img/6c.png') }), // Face -X → 6
        new THREE.MeshBasicMaterial({ map: loaderDice6.load('/img/4c.png') }), // Face +Y → 4
        new THREE.MeshBasicMaterial({ map: loaderDice6.load('/img/3c.png') }), // Face -Y → 3
        new THREE.MeshBasicMaterial({ map: loaderDice6.load('/img/1c.png') }), // Face +Z → 1
        new THREE.MeshBasicMaterial({ map: loaderDice6.load('/img/2c.png') })  // Face -Z → 2
    ];

    const dice6 = new THREE.Mesh(geometryDice6, materialsDice6);
    sceneDice6.add(dice6);

    // Position de la caméra
    cameraDice6.position.z = 3;

    // Mapping des faces & rotations
    const faceRotationsDice6 = {
        1: { x: 0,             y: 0,              z: 0 },
        2: { x: 0,             y: Math.PI,        z: 0 },
        3: { x: -Math.PI / 2,  y: 0,              z: 0 },
        4: { x:  Math.PI / 2,  y: 0,              z: 0 },
        5: { x:  0,            y: -Math.PI / 2,   z: 0 },
        6: { x:  0,            y:  Math.PI / 2,   z: 0 }
    };

    // Animation
    let isRollingDice6 = false;
    let startTimeDice6;
    const durationDice6 = 2; // en secondes
    let targetRotationDice6 = new THREE.Vector3();
    let initialRotationDice6 = new THREE.Vector3();
    let randomFaceDice6;

    // Fonction lancer le dé
    function rollDice6() {
        if (isRollingDice6) return;

        randomFaceDice6 = Math.floor(Math.random() * 6) + 1;
        const faceRotation = faceRotationsDice6[randomFaceDice6];

        // Rotations aléatoires supplémentaires
        const randomTurnsX = Math.floor(Math.random() * 4) + 2;
        const randomTurnsY = Math.floor(Math.random() * 4) + 2;
        const randomTurnsZ = Math.floor(Math.random() * 4) + 2;

        targetRotationDice6 = new THREE.Vector3(
            faceRotation.x + randomTurnsX * Math.PI * 2,
            faceRotation.y + randomTurnsY * Math.PI * 2,
            faceRotation.z + randomTurnsZ * Math.PI * 2
        );

        initialRotationDice6 = new THREE.Vector3(
            dice6.rotation.x % (Math.PI * 2),
            dice6.rotation.y % (Math.PI * 2),
            dice6.rotation.z % (Math.PI * 2)
        );

        startTimeDice6 = performance.now();
        isRollingDice6 = true;

        resultElementDice6.innerText = 'Résultat : -';
    }

    // Fonction d'interpolation (fluidité)
    function easeOutCubicDice6(t) {
        return 1 - Math.pow(1 - t, 3);
    }

    // Animation
    function animateDice6() {
        requestAnimationFrame(animateDice6);

        if (isRollingDice6) {
            const elapsed = (performance.now() - startTimeDice6) / 1000;
            if (elapsed < durationDice6) {
                const progress = elapsed / durationDice6;
                const easedProgress = easeOutCubicDice6(progress);

                dice6.rotation.x = initialRotationDice6.x
                    + (targetRotationDice6.x - initialRotationDice6.x) * easedProgress;
                dice6.rotation.y = initialRotationDice6.y
                    + (targetRotationDice6.y - initialRotationDice6.y) * easedProgress;
                dice6.rotation.z = initialRotationDice6.z
                    + (targetRotationDice6.z - initialRotationDice6.z) * easedProgress;
            } else {
                isRollingDice6 = false;
                dice6.rotation.x = targetRotationDice6.x % (Math.PI * 2);
                dice6.rotation.y = targetRotationDice6.y % (Math.PI * 2);
                dice6.rotation.z = targetRotationDice6.z % (Math.PI * 2);

                resultElementDice6.innerText = `Résultat : ${randomFaceDice6}`;
            }
        }
        rendererDice6.render(sceneDice6, cameraDice6);
    }
    animateDice6();

    // Écouteur sur le bouton
    rollButtonDice6.addEventListener('click', rollDice6);

    // Redimensionnement
    window.addEventListener('resize', () => {
        const containerWidth = containerDice6.clientWidth;
        const containerHeight = containerDice6.clientHeight;

        cameraDice6.aspect = containerWidth / containerHeight;
        cameraDice6.updateProjectionMatrix();
        rendererDice6.setSize(containerWidth, containerHeight);
    });
</script>
