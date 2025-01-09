{{-- resources/views/component/tetraedre4faces.blade.php --}}

<div class="tetra-container">
    <style>
        .tetra-container {
            position: relative;
            width: 100%;  /* Largeur fixe OU 100% ou 50vw, selon besoin */
            height: 50dvh; /* Hauteur fixe OU 100% ou 50vh, selon besoin */
            margin: 0 auto;
            background-color: #f0f0f0;
            overflow: hidden; /* Évite les scrollbars ; on n’affecte plus le body */
            font-family: Arial, sans-serif; /* Police locale (optionnelle) */
        }

        /* Conteneur pour le canvas Three.js */
        .tetra-canvas-wrapper {
            width: 100%;
            height: 100%;
        }

        /* Bouton de lancement du dé */
        .roll-button-tetra {
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
        .roll-button-tetra:hover {
            background: #218838;
        }

        /* Zone affichant le résultat */
        .result-tetra {
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
    </style>

    {{-- Zone d’affichage du résultat + bouton --}}
    <div id="resultTetra" class="result-tetra">Résultat : -</div>
    <button id="rollButtonTetra" class="roll-button-tetra">Lancer le dé</button>

    {{-- Conteneur qui accueillera le canvas Three.js --}}
    <div class="tetra-canvas-wrapper" id="threejsContainerTetra"></div>
</div>

{{-- Inclusion de Three.js (ex. version 0.128.0) --}}


<script>
    // On renomme les variables pour éviter toute collision globale.
    let sceneTetra, cameraTetra, rendererTetra, diceTetra;
    let isRollingTetra = false;
    let startTimeTetra;
    const durationTetra = 2; // Durée animation en s
    let targetRotationTetra = new THREE.Euler();
    let initialRotationTetra = new THREE.Euler();
    const resultElementTetra = document.getElementById('resultTetra');
    const faceLabelsTetra = [];

    // Initialisation
    initTetra();
    animateTetra();

    function initTetra() {
        // On récupère le conteneur (au lieu de body)
        const container = document.getElementById('threejsContainerTetra');
        const containerWidth = container.clientWidth;
        const containerHeight = container.clientHeight;

        // Création de la scène
        sceneTetra = new THREE.Scene();

        // Caméra adaptée au conteneur
        cameraTetra = new THREE.PerspectiveCamera(
            75,
            containerWidth / containerHeight,
            0.1,
            1000
        );
        cameraTetra.position.set(0, 2, 4); // Position adaptée pour le tétraèdre
        cameraTetra.lookAt(0, 0, 0);

        // Rendu
        rendererTetra = new THREE.WebGLRenderer({ antialias: true });
        rendererTetra.setSize(containerWidth, containerHeight);
        rendererTetra.setPixelRatio(window.devicePixelRatio);

        // On attache le canvas au conteneur, pas au body
        container.appendChild(rendererTetra.domElement);

        // Lumières
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        sceneTetra.add(ambientLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
        directionalLight.position.set(5, 10, 7.5);
        sceneTetra.add(directionalLight);

        // Géométrie du tétraèdre (4 faces)
        const geometry = new THREE.TetrahedronGeometry(1, 0);
        const material = new THREE.MeshStandardMaterial({
            color: 0xE53935,
            flatShading: true
        });

        diceTetra = new THREE.Mesh(geometry, material);
        sceneTetra.add(diceTetra);

        // On crée les labels de face
        for (let i = 0; i < 4; i++) {
            faceLabelsTetra.push(i + 1);
        }

        // On calcule les normales des faces
        diceTetra.faceNormals = calculateFaceNormalsTetra(geometry);

        // On ajoute les numéros
        addFaceNumbersTetra();

        // Écouteur sur le bouton
        document.getElementById('rollButtonTetra')
            .addEventListener('click', rollDiceTetra);

        // Écouteur sur le resize
        window.addEventListener('resize', onWindowResizeTetra, false);
    }

    // Calcul des normales (BufferGeometry)
    function calculateFaceNormalsTetra(geometry) {
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

    // Ajout de panneaux (Planes) pour afficher les numéros
    function addFaceNumbersTetra() {
        const geometry = diceTetra.geometry;
        const positionAttribute = geometry.attributes.position;
        const index = geometry.index;

        const numFaces = index ? index.count / 3 : positionAttribute.count / 3;

        for (let faceIndex = 0; faceIndex < numFaces; faceIndex++) {
            const faceNumber = faceLabelsTetra[faceIndex];

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

            // Centre de la face
            const centroid = new THREE.Vector3().addVectors(vA, vB).add(vC).divideScalar(3);

            // Normale de la face
            const edge1 = new THREE.Vector3().subVectors(vB, vA);
            const edge2 = new THREE.Vector3().subVectors(vC, vA);
            const normal = new THREE.Vector3().crossVectors(edge1, edge2).normalize();

            // Canvas pour dessiner le numéro
            const canvas = document.createElement('canvas');
            canvas.width = 256;
            canvas.height = 256;
            const context = canvas.getContext('2d');

            context.clearRect(0, 0, canvas.width, canvas.height);
            context.font = 'bold 100px Arial';
            context.textAlign = 'center';
            context.textBaseline = 'middle';

            context.lineWidth = 6;
            context.strokeStyle = 'black';
            context.strokeText(faceNumber.toString(), canvas.width / 2, canvas.height / 2);
            context.fillStyle = 'white';
            context.fillText(faceNumber.toString(), canvas.width / 2, canvas.height / 2);

            const texture = new THREE.CanvasTexture(canvas);
            texture.needsUpdate = true;

            const planeMaterial = new THREE.MeshBasicMaterial({
                map: texture,
                transparent: true,
                side: THREE.DoubleSide
            });

            // Plane
            const planeGeometry = new THREE.PlaneGeometry(0.6, 0.6);
            const planeMesh = new THREE.Mesh(planeGeometry, planeMaterial);

            // On oriente le plane vers l’extérieur
            planeMesh.lookAt(centroid.clone().add(normal));

            // Décalage
            const offset = 0.015;
            const position = centroid.clone().add(normal.clone().multiplyScalar(offset));
            planeMesh.position.copy(position);

            // On l'attache au dé
            diceTetra.add(planeMesh);
        }
    }

    // Fonction pour lancer
    function rollDiceTetra() {
        if (isRollingTetra) return; // Empêche un double-clic simultané

        // Rotation aléatoire
        const randomRotation = new THREE.Euler(
            Math.random() * Math.PI * 4 + Math.PI * 2,
            Math.random() * Math.PI * 4 + Math.PI * 2,
            Math.random() * Math.PI * 4 + Math.PI * 2
        );

        // Rotation de départ
        initialRotationTetra.set(diceTetra.rotation.x, diceTetra.rotation.y, diceTetra.rotation.z);

        // Rotation cible
        targetRotationTetra.set(
            initialRotationTetra.x + randomRotation.x,
            initialRotationTetra.y + randomRotation.y,
            initialRotationTetra.z + randomRotation.z
        );

        startTimeTetra = performance.now();
        isRollingTetra = true;

        resultElementTetra.innerText = 'Résultat : -';
    }

    // Interpolation (easeOutCubic)
    function easeOutCubicTetra(t) {
        return 1 - Math.pow(1 - t, 3);
    }

    // Boucle d’animation
    function animateTetra() {
        requestAnimationFrame(animateTetra);

        if (isRollingTetra) {
            const elapsed = (performance.now() - startTimeTetra) / 1000;
            if (elapsed < durationTetra) {
                const progress = elapsed / durationTetra;
                const easedProgress = easeOutCubicTetra(progress);

                diceTetra.rotation.x = initialRotationTetra.x + (targetRotationTetra.x - initialRotationTetra.x) * easedProgress;
                diceTetra.rotation.y = initialRotationTetra.y + (targetRotationTetra.y - initialRotationTetra.y) * easedProgress;
                diceTetra.rotation.z = initialRotationTetra.z + (targetRotationTetra.z - initialRotationTetra.z) * easedProgress;
            } else {
                // Fin de l’animation
                isRollingTetra = false;

                // Rotation finale
                diceTetra.rotation.x = targetRotationTetra.x % (Math.PI * 2);
                diceTetra.rotation.y = targetRotationTetra.y % (Math.PI * 2);
                diceTetra.rotation.z = targetRotationTetra.z % (Math.PI * 2);

                // Face orientée vers la caméra
                const result = getFrontFaceIndexTetra();
                resultElementTetra.innerText = 'Résultat : ' + result;
            }
        }
        rendererTetra.render(sceneTetra, cameraTetra);
    }

    // Déterminer la face visible
    function getFrontFaceIndexTetra() {
        const diceMatrix = new THREE.Matrix4().extractRotation(diceTetra.matrixWorld);
        const diceMatrix3 = new THREE.Matrix3().setFromMatrix4(diceMatrix);

        // Normales transformées
        const transformedNormals = diceTetra.faceNormals.map(normal =>
            normal.clone().applyMatrix3(diceMatrix3).normalize()
        );

        // Vecteur vue
        const diePosition = new THREE.Vector3();
        diceTetra.getWorldPosition(diePosition);
        const cameraPosition = cameraTetra.position.clone();
        const viewDirection = new THREE.Vector3().subVectors(cameraPosition, diePosition).normalize();

        let maxDot = -Infinity;
        let frontFaceIndex = -1;

        for (let i = 0; i < transformedNormals.length; i++) {
            const dot = transformedNormals[i].dot(viewDirection);
            if (dot > maxDot) {
                maxDot = dot;
                frontFaceIndex = i;
            }
        }

        // faceLabelsTetra contient [1,2,3,4]
        const faceNumber = faceLabelsTetra[frontFaceIndex] || 1;
        return faceNumber;
    }

    // Gérer le redimensionnement
    function onWindowResizeTetra() {
        const container = document.getElementById('threejsContainerTetra');
        const containerWidth = container.clientWidth;
        const containerHeight = container.clientHeight;

        cameraTetra.aspect = containerWidth / containerHeight;
        cameraTetra.updateProjectionMatrix();
        rendererTetra.setSize(containerWidth, containerHeight);
    }
</script>
