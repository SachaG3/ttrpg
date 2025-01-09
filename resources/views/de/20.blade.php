@extends('layouts.app-main')
@section('title', 'Game')

@section('content')
{{-- resources/views/component/de20.blade.php --}}
<div class="de20-container">
    <style>
        .de20-container {
            position: relative;
            width: 100%;  /* Largeur fixe ; tu peux l’adapter ou la rendre responsive */
            height: 50dvh; /* Hauteur fixe ; tu peux l’adapter ou la rendre responsive */
            margin: 0 auto; /* Centre le composant dans la page */
            background-color: #f0f0f0;
            overflow: hidden;
        }

        .roll-button {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 30px;
            font-size: 18px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background 0.3s;
            z-index: 10; /* Au-dessus du canvas */
        }
        .roll-button:hover {
            background: #218838;
        }

        .result-text {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 24px;
            background: rgba(255, 255, 255, 0.9);
            padding: 12px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 10; /* Au-dessus du canvas */
        }

        /* Pour t’assurer que le conteneur Three.js prenne tout l’espace du .de20-container */
        .threejs-canvas-wrapper {
            width: 100%;
            height: 100%;
        }
    </style>

    {{-- Zone d’affichage du résultat et bouton pour lancer le dé --}}
    <div id="resultDe20" class="result-text">Résultat : -</div>
    <button id="rollButtonDe20" class="roll-button">Lancer le dé</button>

    {{-- Conteneur qui accueillera le canvas Three.js --}}
    <div id="threejsContainer" class="threejs-canvas-wrapper"></div>
</div>

{{-- Inclusion de Three.js (depuis un CDN) --}}
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>

<script>
    // On va remplacer tous les anciens ID "rollButton", "result", etc.
    // par nos nouveaux ID : "rollButtonDe20", "resultDe20", etc.

    let scene, camera, renderer, dice;
    let isRolling = false;
    let startTime;
    const duration = 2; // Durée de l'animation en secondes
    let targetRotation = new THREE.Euler();
    let initialRotation = new THREE.Euler();
    const resultElement = document.getElementById('resultDe20');
    const faceLabels = []; // Tableau pour stocker les numéros de face

    // Initialisation de la scène
    init();
    animate();

    function init() {
        // Récupère le conteneur où le canvas sera injecté
        const container = document.getElementById('threejsContainer');
        const containerWidth = container.clientWidth;
        const containerHeight = container.clientHeight;

        // Création de la scène
        scene = new THREE.Scene();

        // Création de la caméra
        camera = new THREE.PerspectiveCamera(
            75,  // FOV
            containerWidth / containerHeight,  // Ratio d'aspect basé sur le conteneur
            0.1,  // Plan de coupe proche
            1000 // Plan de coupe éloigné
        );
        camera.position.set(0, 3, 6); // Position de la caméra
        camera.lookAt(0, 0, 0); // Orientation de la caméra vers le centre de la scène

        // Création du rendu
        renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(containerWidth, containerHeight);
        renderer.setPixelRatio(window.devicePixelRatio);
        // Injecte le canvas dans le conteneur au lieu du body
        container.appendChild(renderer.domElement);

        // Lumières
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6); // Lumière ambiante
        scene.add(ambientLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8); // Lumière directionnelle
        directionalLight.position.set(5, 10, 7.5); // Position de la lumière directionnelle
        scene.add(directionalLight);

        // Création du dé à 20 faces (icosaèdre)
        const geometry = new THREE.IcosahedronGeometry(1, 0); // Rayon 1, aucune subdivision
        const material = new THREE.MeshStandardMaterial({ color: 0x007BFF, flatShading: true });
        dice = new THREE.Mesh(geometry, material);
        scene.add(dice);

        // Assignation des numéros de face de 1 à 20
        for (let i = 0; i < 20; i++) {
            faceLabels.push(i + 1);
        }

        // Génération des normales des faces
        dice.faceNormals = calculateFaceNormals(geometry);

        // Ajouter les numéros sur les faces
        addFaceNumbers();

        // Événement du bouton pour lancer le dé
        document.getElementById('rollButtonDe20').addEventListener('click', rollDice);

        // Gestion de la taille du conteneur au redimensionnement de la fenêtre
        window.addEventListener('resize', onWindowResize, false);
    }

    function onWindowResize() {
        const container = document.getElementById('threejsContainer');
        const containerWidth = container.clientWidth;
        const containerHeight = container.clientHeight;

        camera.aspect = containerWidth / containerHeight;
        camera.updateProjectionMatrix();

        renderer.setSize(containerWidth, containerHeight);
    }

    // Fonction pour calculer les normales des faces pour BufferGeometry
    function calculateFaceNormals(geometry) {
        const faceNormals = [];
        const position = geometry.attributes.position;
        const index = geometry.index;

        const numFaces = index ? index.count / 3 : position.count / 3;

        for (let faceIndex = 0; faceIndex < numFaces; faceIndex++) {
            let vA, vB, vC;

            if (index) {
                const a = index.getX(faceIndex * 3);
                const b = index.getX(faceIndex * 3 + 1);
                const c = index.getX(faceIndex * 3 + 2);

                vA = new THREE.Vector3().fromBufferAttribute(position, a);
                vB = new THREE.Vector3().fromBufferAttribute(position, b);
                vC = new THREE.Vector3().fromBufferAttribute(position, c);
            } else {
                const i = faceIndex * 3;
                vA = new THREE.Vector3().fromBufferAttribute(position, i);
                vB = new THREE.Vector3().fromBufferAttribute(position, i + 1);
                vC = new THREE.Vector3().fromBufferAttribute(position, i + 2);
            }

            const edge1 = new THREE.Vector3().subVectors(vB, vA);
            const edge2 = new THREE.Vector3().subVectors(vC, vA);
            const normal = new THREE.Vector3().crossVectors(edge1, edge2).normalize();

            faceNormals.push(normal);
        }

        return faceNormals;
    }

    // Fonction pour ajouter les numéros sur chaque face
    function addFaceNumbers() {
        const geometry = dice.geometry;
        const positionAttribute = geometry.attributes.position;
        const index = geometry.index;

        const numFaces = index ? index.count / 3 : positionAttribute.count / 3;

        for (let faceIndex = 0; faceIndex < numFaces; faceIndex++) {
            const faceNumber = faceLabels[faceIndex];

            let vA, vB, vC;
            if (index) {
                const a = index.getX(faceIndex * 3);
                const b = index.getX(faceIndex * 3 + 1);
                const c = index.getX(faceIndex * 3 + 2);

                vA = new THREE.Vector3().fromBufferAttribute(positionAttribute, a);
                vB = new THREE.Vector3().fromBufferAttribute(positionAttribute, b);
                vC = new THREE.Vector3().fromBufferAttribute(positionAttribute, c);
            } else {
                const i = faceIndex * 3;
                vA = new THREE.Vector3().fromBufferAttribute(positionAttribute, i);
                vB = new THREE.Vector3().fromBufferAttribute(positionAttribute, i + 1);
                vC = new THREE.Vector3().fromBufferAttribute(positionAttribute, i + 2);
            }

            // Calcul du centre de la face
            const centroid = new THREE.Vector3().addVectors(vA, vB).add(vC).divideScalar(3);

            // Calcul de la normale de la face
            const edge1 = new THREE.Vector3().subVectors(vB, vA);
            const edge2 = new THREE.Vector3().subVectors(vC, vA);
            const normal = new THREE.Vector3().crossVectors(edge1, edge2).normalize();

            // Créer la texture avec le numéro
            const canvas = document.createElement('canvas');
            canvas.width = 256;
            canvas.height = 256;
            const context = canvas.getContext('2d');

            // Dessiner le fond transparent
            context.clearRect(0, 0, canvas.width, canvas.height);

            // Configurer le texte
            context.font = 'bold 105px Arial';
            context.textAlign = 'center';
            context.textBaseline = 'middle';

            // Dessiner le contour du numéro
            context.lineWidth = 8;
            context.strokeStyle = 'black';
            context.strokeText(faceNumber.toString(), canvas.width / 2, canvas.height / 2);

            // Remplir le numéro
            context.fillStyle = 'white';
            context.fillText(faceNumber.toString(), canvas.width / 2, canvas.height / 2);

            // Créer la texture
            const texture = new THREE.CanvasTexture(canvas);
            texture.needsUpdate = true;

            // Créer le matériau du Plan
            const planeMaterial = new THREE.MeshBasicMaterial({ map: texture, transparent: true, side: THREE.DoubleSide });
            const planeGeometry = new THREE.PlaneGeometry(0.8, 0.8);
            const planeMesh = new THREE.Mesh(planeGeometry, planeMaterial);

            // Orienter le Plan pour qu'il fasse face à l'extérieur
            planeMesh.lookAt(centroid.clone().add(normal));

            // Positionner le Plan au centre de la face, légèrement décalé le long de la normale
            const offset = 0.015;
            const position = centroid.clone().add(normal.clone().multiplyScalar(offset));
            planeMesh.position.copy(position);

            // Ajouter le Plan comme enfant du dé pour qu'il tourne avec lui
            dice.add(planeMesh);
        }
    }

    // Fonction pour lancer le dé
    function rollDice() {
        if (isRolling) return; // Empêche les lancers multiples simultanés

        // Génération d'une rotation aléatoire
        const randomRotation = new THREE.Euler(
            Math.random() * Math.PI * 4 + Math.PI * 2,
            Math.random() * Math.PI * 4 + Math.PI * 2,
            Math.random() * Math.PI * 4 + Math.PI * 2
        );

        // Stockage de la rotation initiale
        initialRotation = new THREE.Euler(
            dice.rotation.x,
            dice.rotation.y,
            dice.rotation.z
        );

        // Définition de la rotation cible
        targetRotation = new THREE.Euler(
            initialRotation.x + randomRotation.x,
            initialRotation.y + randomRotation.y,
            initialRotation.z + randomRotation.z
        );

        startTime = performance.now();
        isRolling = true;

        resultElement.innerText = 'Résultat : -'; // Réinitialise le résultat
    }

    // Fonction d'interpolation pour une animation fluide
    function easeOutCubic(t) {
        return 1 - Math.pow(1 - t, 3);
    }

    // Fonction d'animation
    function animate() {
        requestAnimationFrame(animate);
        const currentTime = performance.now();

        if (isRolling) {
            const elapsed = (currentTime - startTime) / 1000; // Temps écoulé en secondes
            if (elapsed < duration) {
                const progress = elapsed / duration;
                const easedProgress = easeOutCubic(progress);

                dice.rotation.x = initialRotation.x + (targetRotation.x - initialRotation.x) * easedProgress;
                dice.rotation.y = initialRotation.y + (targetRotation.y - initialRotation.y) * easedProgress;
                dice.rotation.z = initialRotation.z + (targetRotation.z - initialRotation.z) * easedProgress;
            } else {
                // Animation terminée
                isRolling = false;

                dice.rotation.x = targetRotation.x % (Math.PI * 2);
                dice.rotation.y = targetRotation.y % (Math.PI * 2);
                dice.rotation.z = targetRotation.z % (Math.PI * 2);

                // Détermination de la face en face du joueur
                const result = getFrontFaceIndex();
                resultElement.innerText = 'Résultat : ' + result;
            }
        }

        renderer.render(scene, camera);
    }

    // Fonction pour obtenir l'indice de la face en face du joueur
    function getFrontFaceIndex() {
        const diceMatrix = new THREE.Matrix4();
        diceMatrix.extractRotation(dice.matrixWorld);
        const diceMatrix3 = new THREE.Matrix3().setFromMatrix4(diceMatrix);

        // Récupérer les normales des faces transformées
        const transformedNormals = dice.faceNormals.map(normal => {
            return normal.clone().applyMatrix3(diceMatrix3).normalize();
        });

        // Calculer le vecteur de la position du dé vers la caméra
        const diePosition = new THREE.Vector3();
        dice.getWorldPosition(diePosition);
        const cameraPosition = camera.position.clone();
        const viewDirection = new THREE.Vector3().subVectors(cameraPosition, diePosition).normalize();

        // Trouver la face dont la normale est la plus alignée avec le vecteur de vue
        let maxDot = -Infinity;
        let frontFaceIndex = -1;

        for (let i = 0; i < transformedNormals.length; i++) {
            const dot = transformedNormals[i].dot(viewDirection);
            if (dot > maxDot) {
                maxDot = dot;
                frontFaceIndex = i;
            }
        }

        // Le résultat est le numéro de la face correspondante
        const faceNumber = faceLabels[frontFaceIndex] || 1;
        return faceNumber;
    }
</script>
@endsection
