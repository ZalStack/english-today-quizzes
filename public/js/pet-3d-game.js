/**
 * English Today - 3D Virtual Pet & Egg Hatching WebGL Game Engine
 * Built with Three.js, Canvas Confetti & Web Audio API Synthesizer
 */

class Pet3DGame {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error(`Pet3DGame: Container #${containerId} not found.`);
            return;
        }

        this.options = Object.assign({
            apiUrl: '/employee/pet',
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            autoInit: true,
            interactive: true,
            soundEnabled: true,
            onStateChange: null,
            onHatched: null,
        }, options);

        this.state = {
            status: 'egg', // 'egg', 'ready_to_hatch', 'hatched'
            growthPoints: 0,
            maxGrowthPoints: 100,
            growthPercentage: 0,
            crackStage: 0,
            level: 1,
            experience: 0,
            maxExperience: 100,
            expPercentage: 0,
            happiness: 100,
            energy: 100,
            nickname: 'My Egg',
            pet: null,
            strikeCount: 0,
            maxStrikes: 3,
            isHatching: false,
        };

        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.clock = new THREE.Clock();
        this.audioCtx = null;
        this.soundEnabled = this.options.soundEnabled;

        // 3D Objects
        this.rootGroup = null;
        this.islandGroup = null;
        this.eggGroup = null;
        this.eggMesh = null;
        this.crackLinesGroup = null;
        this.shardsGroup = null;
        this.petGroup = null;
        this.petParts = {};
        this.particlesGroup = null;
        this.heartParticles = [];
        this.raycaster = new THREE.Raycaster();
        this.mouse = new THREE.Vector2();

        // Burst hearts (breeding / celebration) & animation clock reference
        this.heartsGroup = null;
        this.burstHearts = [];
        this.currentTime = 0;

        // Interaction & Physics
        this.isDragging = false;
        this.previousMousePosition = { x: 0, y: 0 };
        this.targetRotationY = 0;
        this.currentRotationY = 0;
        this.wobbleIntensity = 0;
        this.wobbleDecay = 0.92;
        this.basePetY = 0.1;
        this.jumpTime = 0;

        if (this.options.autoInit) {
            this.init();
        }
    }

    async init() {
        this.setupAudio();
        this.setupThree();
        this.buildEnvironment();
        this.setupEventListeners();
        this.animate();

        await this.fetchStatus();
    }

    /* -------------------------------------------------------------
     * 1. WEB AUDIO API SYNTHESIZER (No external MP3 files required)
     * -----------------------------------------------------------*/
    setupAudio() {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (AudioContext) {
            this.audioCtx = new AudioContext();
        }
    }

    ensureAudioReady() {
        if (this.audioCtx && this.audioCtx.state === 'suspended') {
            this.audioCtx.resume();
        }
    }

    playSound(type) {
        if (!this.soundEnabled || !this.audioCtx) return;
        this.ensureAudioReady();

        try {
            const ctx = this.audioCtx;
            const now = ctx.currentTime;

            if (type === 'tap' || type === 'wobble') {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(320 + Math.random() * 80, now);
                osc.frequency.exponentialRampToValueAtTime(140, now + 0.14);
                gain.gain.setValueAtTime(0.3, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.14);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.15);
            } else if (type === 'crack') {
                // Crunchy noise crack
                const bufferSize = ctx.sampleRate * 0.15;
                const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
                const data = buffer.getChannelData(0);
                for (let i = 0; i < bufferSize; i++) {
                    data[i] = (Math.random() * 2 - 1) * Math.exp(-i / (bufferSize * 0.25));
                }
                const noise = ctx.createBufferSource();
                noise.buffer = buffer;
                const filter = ctx.createBiquadFilter();
                filter.type = 'bandpass';
                filter.frequency.setValueAtTime(1200, now);
                filter.Q.setValueAtTime(3, now);
                const gain = ctx.createGain();
                gain.gain.setValueAtTime(0.6, now);
                gain.gain.exponentialRampToValueAtTime(0.01, now + 0.15);
                noise.connect(filter);
                filter.connect(gain);
                gain.connect(ctx.destination);
                noise.start(now);
            } else if (type === 'fanfare') {
                // Triumphant ascending arpeggio (C4, E4, G4, C5, E5, G5)
                const notes = [261.63, 329.63, 392.00, 523.25, 659.25, 783.99];
                notes.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.09);
                    gain.gain.setValueAtTime(0, now + idx * 0.09);
                    gain.gain.linearRampToValueAtTime(0.28, now + idx * 0.09 + 0.03);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.09 + 0.6);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + idx * 0.09);
                    osc.stop(now + idx * 0.09 + 0.65);
                });
            } else if (type === 'happy' || type === 'chirp') {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(580, now);
                osc.frequency.linearRampToValueAtTime(880, now + 0.08);
                osc.frequency.linearRampToValueAtTime(1100, now + 0.18);
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.exponentialRampToValueAtTime(0.01, now + 0.22);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.23);
            } else if (type === 'munch') {
                for (let i = 0; i < 3; i++) {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'square';
                    osc.frequency.setValueAtTime(280 - i * 30, now + i * 0.08);
                    gain.gain.setValueAtTime(0.2, now + i * 0.08);
                    gain.gain.exponentialRampToValueAtTime(0.01, now + i * 0.08 + 0.06);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + i * 0.08);
                    osc.stop(now + i * 0.08 + 0.07);
                }
            } else if (type === 'levelup') {
                const freqs = [440, 554.37, 659.25, 880];
                freqs.forEach((f, i) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(f, now + i * 0.08);
                    gain.gain.setValueAtTime(0.25, now + i * 0.08);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + i * 0.08 + 0.5);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + i * 0.08);
                    osc.stop(now + i * 0.08 + 0.55);
                });
            }
        } catch (e) {
            console.warn('Audio play error:', e);
        }
    }

    /* -------------------------------------------------------------
     * 2. THREE.JS SCENE SETUP
     * -----------------------------------------------------------*/
    setupThree() {
        const width = this.container.clientWidth || 400;
        const height = this.container.clientHeight || 360;

        this.scene = new THREE.Scene();
        this.scene.background = null; // Transparent background to blend nicely with card

        // Camera
        this.camera = new THREE.PerspectiveCamera(42, width / height, 0.1, 1000);
        this.camera.position.set(0, 2.2, 5.8);
        this.camera.lookAt(0, 0.8, 0);

        // Renderer
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' });
        this.renderer.setSize(width, height);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.shadowMap.enabled = true;
        this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
        this.renderer.toneMappingExposure = 1.1;

        this.container.innerHTML = '';
        this.container.appendChild(this.renderer.domElement);

        // Root Group
        this.rootGroup = new THREE.Group();
        this.scene.add(this.rootGroup);

        // Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.9);
        this.scene.add(ambientLight);

        const keyLight = new THREE.DirectionalLight(0xffffff, 1.4);
        keyLight.position.set(4, 8, 5);
        keyLight.castShadow = true;
        keyLight.shadow.mapSize.width = 1024;
        keyLight.shadow.mapSize.height = 1024;
        keyLight.shadow.bias = -0.0005;
        this.scene.add(keyLight);

        const fillLight = new THREE.DirectionalLight(0x90caf9, 0.6);
        fillLight.position.set(-4, 3, -3);
        this.scene.add(fillLight);

        const rimLight = new THREE.PointLight(0xffd54f, 1.2, 8);
        rimLight.position.set(0, 3, -2);
        this.scene.add(rimLight);
    }

    /* -------------------------------------------------------------
     * 3. 3D ENVIRONMENT (Lush Floating Island / Nest Platform)
     * -----------------------------------------------------------*/
    buildEnvironment() {
        this.islandGroup = new THREE.Group();
        this.rootGroup.add(this.islandGroup);

        // Stylized Grass Disc Platform
        const grassGeo = new THREE.CylinderGeometry(1.9, 1.6, 0.35, 32);
        const grassMat = new THREE.MeshStandardMaterial({
            color: 0x4caf50,
            roughness: 0.6,
            metalness: 0.1,
            flatShading: true,
        });
        const grassMesh = new THREE.Mesh(grassGeo, grassMat);
        grassMesh.position.y = -0.18;
        grassMesh.receiveShadow = true;
        this.islandGroup.add(grassMesh);

        // Earth Base
        const earthGeo = new THREE.CylinderGeometry(1.6, 0.9, 0.45, 16);
        const earthMat = new THREE.MeshStandardMaterial({
            color: 0x5d4037,
            roughness: 0.9,
            flatShading: true,
        });
        const earthMesh = new THREE.Mesh(earthGeo, earthMat);
        earthMesh.position.y = -0.55;
        earthMesh.receiveShadow = true;
        this.islandGroup.add(earthMesh);

        // Magical Rune Glow Ring
        const ringGeo = new THREE.RingGeometry(1.3, 1.55, 32);
        const ringMat = new THREE.MeshBasicMaterial({
            color: 0x00e5ff,
            side: THREE.DoubleSide,
            transparent: true,
            opacity: 0.45,
        });
        const ringMesh = new THREE.Mesh(ringGeo, ringMat);
        ringMesh.rotation.x = -Math.PI / 2;
        ringMesh.position.y = 0.01;
        this.islandGroup.add(ringMesh);

        // Soft Nest Leaves/Hay
        const hayMat = new THREE.MeshStandardMaterial({ color: 0xffca28, roughness: 0.8, flatShading: true });
        for (let i = 0; i < 14; i++) {
            const angle = (i / 14) * Math.PI * 2;
            const dist = 0.95 + Math.random() * 0.25;
            const leafGeo = new THREE.ConeGeometry(0.18, 0.5, 5);
            const leaf = new THREE.Mesh(leafGeo, hayMat);
            leaf.position.set(Math.cos(angle) * dist, 0.08, Math.sin(angle) * dist);
            leaf.rotation.x = Math.PI / 2 + 0.3;
            leaf.rotation.z = -angle;
            leaf.castShadow = true;
            this.islandGroup.add(leaf);
        }

        // Floating Magical Dust Particles
        const particleCount = 28;
        const pGeo = new THREE.BufferGeometry();
        const pPositions = new Float32Array(particleCount * 3);
        for (let i = 0; i < particleCount * 3; i += 3) {
            pPositions[i] = (Math.random() - 0.5) * 3.5;
            pPositions[i + 1] = Math.random() * 2.8 + 0.2;
            pPositions[i + 2] = (Math.random() - 0.5) * 3.5;
        }
        pGeo.setAttribute('position', new THREE.BufferAttribute(pPositions, 3));
        const pMat = new THREE.PointsMaterial({
            color: 0xffeb3b,
            size: 0.08,
            transparent: true,
            opacity: 0.75,
            blending: THREE.AdditiveBlending,
        });
        this.particlesGroup = new THREE.Points(pGeo, pMat);
        this.islandGroup.add(this.particlesGroup);
    }

    /* -------------------------------------------------------------
     * 4. 3D PROCEDURAL EGG BUILDER & CRACK VISUALS
     * -----------------------------------------------------------*/
    buildEgg(crackStage = 0) {
        if (this.eggGroup) {
            this.rootGroup.remove(this.eggGroup);
        }
        if (this.petGroup) {
            this.rootGroup.remove(this.petGroup);
            this.petGroup = null;
        }

        this.eggGroup = new THREE.Group();
        this.rootGroup.add(this.eggGroup);

        // Procedural Egg Geometry (Smooth Bezier Lathe curve)
        const points = [];
        for (let deg = 0; deg <= 180; deg += 4) {
            const rad = (Math.PI / 180) * deg;
            const x = Math.sin(rad) * (0.88 + 0.2 * Math.cos(rad));
            const y = -Math.cos(rad) * 1.25;
            points.push(new THREE.Vector2(x, y + 1.25));
        }
        const eggGeo = new THREE.LatheGeometry(points, 32);

        // Egg Material (Slightly warm cream with iridescent shine)
        // NOTE: clearcoat / clearcoatRoughness hanya valid di MeshPhysicalMaterial
        const eggMat = new THREE.MeshPhysicalMaterial({
            color: 0xfef9e7,
            roughness: 0.35,
            metalness: 0.15,
            clearcoat: 0.4,
            clearcoatRoughness: 0.2,
        });

        this.eggMesh = new THREE.Mesh(eggGeo, eggMat);
        this.eggMesh.position.y = 0.05;
        this.eggMesh.castShadow = true;
        this.eggMesh.receiveShadow = true;
        this.eggGroup.add(this.eggMesh);

        // Decorative Speckles / Pattern on Egg
        this.addEggSpeckles();

        // 3D Crack Lines
        this.buildCrackLines(crackStage);
    }

    addEggSpeckles() {
        const speckleCount = 18;
        const speckMat = new THREE.MeshBasicMaterial({ color: 0x8d6e63, transparent: true, opacity: 0.35 });
        for (let i = 0; i < speckleCount; i++) {
            const phi = Math.random() * Math.PI;
            const theta = Math.random() * Math.PI * 2;
            const r = 0.86;
            const sGeo = new THREE.CircleGeometry(0.04 + Math.random() * 0.05, 8);
            const spot = new THREE.Mesh(sGeo, speckMat);
            spot.position.set(
                r * Math.sin(phi) * Math.cos(theta),
                r * Math.cos(phi) + 1.2,
                r * Math.sin(phi) * Math.sin(theta)
            );
            spot.lookAt(0, 1.2, 0);
            spot.rotation.z = Math.random() * Math.PI;
            this.eggGroup.add(spot);
        }
    }

    buildCrackLines(stage) {
        if (this.crackLinesGroup) {
            this.eggGroup.remove(this.crackLinesGroup);
        }
        if (stage <= 0) return;

        this.crackLinesGroup = new THREE.Group();
        this.eggGroup.add(this.crackLinesGroup);

        const crackMat = new THREE.LineBasicMaterial({
            color: stage >= 3 ? 0xffd54f : 0x424242,
            linewidth: 3,
        });

        // Generate jagged crack paths based on stage
        const crackConfigs = [
            // Stage 1 (25%): 1 small hairline fracture
            [
                [0, 1.3, 0.95],
                [0.12, 1.15, 0.94],
                [-0.08, 1.0, 0.92],
                [0.05, 0.85, 0.89],
            ],
            // Stage 2 (50%): Branching cracks
            [
                [0.12, 1.15, 0.94],
                [0.35, 1.25, 0.88],
                [0.5, 1.1, 0.82],
            ],
            // Stage 3 (75%): Deep fissures across center
            [
                [-0.08, 1.0, 0.92],
                [-0.4, 0.95, 0.85],
                [-0.6, 1.15, 0.72],
                [-0.75, 1.0, 0.65],
            ],
            // Stage 4 (100%): Glowing break line around the perimeter
            [
                [0.05, 0.85, 0.89],
                [0.3, 0.7, 0.84],
                [0.6, 0.75, 0.68],
                [0.85, 0.8, 0.45],
            ],
        ];

        const activeConfigs = crackConfigs.slice(0, stage);
        activeConfigs.forEach(pathPoints => {
            const linePoints = pathPoints.map(p => new THREE.Vector3(p[0], p[1], p[2]));
            const lineGeo = new THREE.BufferGeometry().setFromPoints(linePoints);
            const line = new THREE.Line(lineGeo, crackMat);
            this.crackLinesGroup.add(line);
        });

        // Stage 4: Glowing core light inside cracks
        if (stage >= 3) {
            const innerGlow = new THREE.PointLight(0xffd54f, 2.5, 3.5);
            innerGlow.position.set(0, 1.2, 0);
            this.crackLinesGroup.add(innerGlow);
        }
    }

    /* -------------------------------------------------------------
     * 5. 3D CREATURE / PET PROCEDURAL BUILDER
     * -----------------------------------------------------------*/
    /**
     * Skala visual root group pet per tahap hidup (bayi → remaja → dewasa).
     */
    getStageScale(stage) {
        switch (stage) {
            case 'baby': return 0.55;
            case 'juvenile': return 0.78;
            case 'adult': return 1.0;
            default: return 1.0;
        }
    }

    /** Tahap hidup yang aman untuk visual (egg dianggap baby). */
    resolveLifeStage() {
        const stage = this.state.life_stage;
        return (stage && stage !== 'egg') ? stage : 'baby';
    }

    buildPet(petData) {
        if (this.eggGroup) {
            this.rootGroup.remove(this.eggGroup);
            this.eggGroup = null;
        }
        if (this.petGroup) {
            this.rootGroup.remove(this.petGroup);
        }

        this.petGroup = new THREE.Group();
        this.rootGroup.add(this.petGroup);
        this.petParts = {};

        const preset = petData?.model_preset || 'fox';
        const pColor = parseInt((petData?.color_primary || '#FF7043').replace('#', '0x'), 16);
        const sColor = parseInt((petData?.color_secondary || '#FFE082').replace('#', '0x'), 16);

        const primaryMat = new THREE.MeshStandardMaterial({
            color: pColor,
            roughness: 0.5,
            metalness: 0.1,
            flatShading: true,
        });

        const secondaryMat = new THREE.MeshStandardMaterial({
            color: sColor,
            roughness: 0.6,
            metalness: 0.05,
            flatShading: true,
        });

        const whiteMat = new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.3 });
        const blackMat = new THREE.MeshBasicMaterial({ color: 0x111111 });
        const glowMat = new THREE.MeshBasicMaterial({ color: sColor });

        if (preset === 'dragon') {
            this.buildDragonMesh(primaryMat, secondaryMat, whiteMat, blackMat, glowMat);
        } else if (preset === 'owl') {
            this.buildOwlMesh(primaryMat, secondaryMat, whiteMat, blackMat);
        } else if (preset === 'phoenix') {
            this.buildPhoenixMesh(primaryMat, secondaryMat, whiteMat, blackMat, glowMat);
        } else if (preset === 'cat') {
            this.buildCatMesh(primaryMat, secondaryMat, whiteMat, blackMat);
        } else if (preset === 'otter') {
            this.buildOtterMesh(primaryMat, secondaryMat, whiteMat, blackMat);
        } else {
            // Default: Fox
            this.buildFoxMesh(primaryMat, secondaryMat, whiteMat, blackMat);
        }

        this.petGroup.position.y = 0.1;

        // Skala root group pet mengikuti tahap hidup (bayi kecil → dewasa penuh)
        const stageScale = this.getStageScale(this.resolveLifeStage());
        this.petGroup.scale.set(stageScale, stageScale, stageScale);
    }

    buildFoxMesh(priMat, secMat, whiteMat, blackMat) {
        // Body (Smooth Rounded Body using Sphere with scale)
        const bodyGeo = new THREE.SphereGeometry(0.48, 16, 16);
        const body = new THREE.Mesh(bodyGeo, priMat);
        body.position.y = 0.75;
        body.scale.set(0.9, 1.25, 0.9);
        body.castShadow = true;
        this.petGroup.add(body);
        this.petParts.body = body;

        // Belly Patch
        const bellyGeo = new THREE.SphereGeometry(0.35, 12, 12);
        const belly = new THREE.Mesh(bellyGeo, secMat);
        belly.position.set(0, 0.75, 0.25);
        belly.scale.set(0.75, 1.1, 0.4);
        this.petGroup.add(belly);

        // Head Group (for tilting & nodding)
        const headGroup = new THREE.Group();
        headGroup.position.set(0, 1.35, 0.05);
        this.petGroup.add(headGroup);
        this.petParts.head = headGroup;

        const headGeo = new THREE.SphereGeometry(0.46, 16, 16);
        const head = new THREE.Mesh(headGeo, priMat);
        head.castShadow = true;
        headGroup.add(head);

        // Snout
        const snoutGeo = new THREE.ConeGeometry(0.22, 0.42, 8);
        const snout = new THREE.Mesh(snoutGeo, secMat);
        snout.rotation.x = Math.PI / 2;
        snout.position.set(0, -0.06, 0.45);
        snout.castShadow = true;
        headGroup.add(snout);

        // Nose
        const noseGeo = new THREE.SphereGeometry(0.06, 8, 8);
        const nose = new THREE.Mesh(noseGeo, blackMat);
        nose.position.set(0, -0.05, 0.68);
        headGroup.add(nose);

        // Eyes (Anime style big eyes with shiny pupils)
        const eyeGeo = new THREE.SphereGeometry(0.08, 10, 10);
        const leftEye = new THREE.Mesh(eyeGeo, blackMat);
        leftEye.position.set(-0.18, 0.08, 0.38);
        headGroup.add(leftEye);

        const rightEye = new THREE.Mesh(eyeGeo, blackMat);
        rightEye.position.set(0.18, 0.08, 0.38);
        headGroup.add(rightEye);
        this.petParts.leftEye = leftEye;
        this.petParts.rightEye = rightEye;

        // Big Pointy Fox Ears
        const earGeo = new THREE.ConeGeometry(0.2, 0.52, 6);
        const leftEar = new THREE.Mesh(earGeo, priMat);
        leftEar.position.set(-0.28, 0.45, 0);
        leftEar.rotation.z = 0.28;
        leftEar.castShadow = true;
        headGroup.add(leftEar);

        const rightEar = new THREE.Mesh(earGeo, priMat);
        rightEar.position.set(0.28, 0.45, 0);
        rightEar.rotation.z = -0.28;
        rightEar.castShadow = true;
        headGroup.add(rightEar);

        // Inner Ear Highlights
        const inEarGeo = new THREE.ConeGeometry(0.12, 0.38, 6);
        const leftInEar = new THREE.Mesh(inEarGeo, secMat);
        leftInEar.position.set(-0.27, 0.44, 0.04);
        leftInEar.rotation.z = 0.28;
        headGroup.add(leftInEar);

        const rightInEar = new THREE.Mesh(inEarGeo, secMat);
        rightInEar.position.set(0.27, 0.44, 0.04);
        rightInEar.rotation.z = -0.28;
        headGroup.add(rightInEar);

        // Fluffy Animated Tail
        const tailGroup = new THREE.Group();
        tailGroup.position.set(0, 0.5, -0.35);
        this.petGroup.add(tailGroup);
        this.petParts.tail = tailGroup;

        const tailGeo = new THREE.ConeGeometry(0.26, 0.9, 8);
        const tail = new THREE.Mesh(tailGeo, priMat);
        tail.rotation.x = -Math.PI / 3;
        tail.position.set(0, 0.35, -0.28);
        tail.castShadow = true;
        tailGroup.add(tail);

        const tailTipGeo = new THREE.ConeGeometry(0.16, 0.38, 8);
        const tailTip = new THREE.Mesh(tailTipGeo, secMat);
        tailTip.rotation.x = -Math.PI / 3;
        tailTip.position.set(0, 0.65, -0.5);
        tailGroup.add(tailTip);
    }

    buildDragonMesh(priMat, secMat, whiteMat, blackMat, glowMat) {
        // --- 1. TORSO & CHEST ---
        const torsoGroup = new THREE.Group();
        torsoGroup.position.set(0, 0.75, -0.05);
        this.petGroup.add(torsoGroup);
        this.petParts.body = torsoGroup;

        // Main Muscular Torso
        const bodyGeo = new THREE.CylinderGeometry(0.36, 0.44, 0.85, 12);
        const body = new THREE.Mesh(bodyGeo, priMat);
        body.rotation.x = 0.25;
        body.castShadow = true;
        torsoGroup.add(body);

        // Segmented Underbelly Golden Scale Plates
        for (let i = 0; i < 4; i++) {
            const plateGeo = new THREE.BoxGeometry(0.38 - i * 0.04, 0.14, 0.18);
            const plate = new THREE.Mesh(plateGeo, secMat);
            plate.position.set(0, 0.22 - i * 0.16, 0.24 + i * 0.02);
            plate.rotation.x = 0.25;
            plate.castShadow = true;
            torsoGroup.add(plate);
        }

        // --- 2. NECK & HEAD GROUP ---
        const neckGroup = new THREE.Group();
        neckGroup.position.set(0, 1.1, 0.1);
        this.petGroup.add(neckGroup);
        this.petParts.head = neckGroup;

        // Arched Dragon Neck
        const neckGeo = new THREE.CylinderGeometry(0.24, 0.32, 0.55, 10);
        const neck = new THREE.Mesh(neckGeo, priMat);
        neck.position.set(0, 0.2, 0.08);
        neck.rotation.x = 0.35;
        neck.castShadow = true;
        neckGroup.add(neck);

        // Dragon Head Base
        const headBaseGeo = new THREE.BoxGeometry(0.46, 0.34, 0.5);
        const headBase = new THREE.Mesh(headBaseGeo, priMat);
        headBase.position.set(0, 0.45, 0.22);
        headBase.castShadow = true;
        neckGroup.add(headBase);

        // Elongated Dragon Snout / Upper Muzzle
        const snoutGeo = new THREE.BoxGeometry(0.36, 0.22, 0.52);
        const snout = new THREE.Mesh(snoutGeo, priMat);
        snout.position.set(0, 0.38, 0.62);
        snout.rotation.x = 0.08;
        snout.castShadow = true;
        neckGroup.add(snout);

        // Nostril Ridges
        const nostrilMat = new THREE.MeshBasicMaterial({ color: 0x111111 });
        const leftNostril = new THREE.Mesh(new THREE.BoxGeometry(0.06, 0.05, 0.08), nostrilMat);
        leftNostril.position.set(-0.1, 0.46, 0.82);
        neckGroup.add(leftNostril);

        const rightNostril = new THREE.Mesh(new THREE.BoxGeometry(0.06, 0.05, 0.08), nostrilMat);
        rightNostril.position.set(0.1, 0.46, 0.82);
        neckGroup.add(rightNostril);

        // Lower Jaw with Teeth
        const jawGeo = new THREE.BoxGeometry(0.32, 0.12, 0.48);
        const jaw = new THREE.Mesh(jawGeo, secMat);
        jaw.position.set(0, 0.24, 0.6);
        jaw.castShadow = true;
        neckGroup.add(jaw);

        // Sharp Dragon Teeth (Fangs)
        const toothGeo = new THREE.ConeGeometry(0.03, 0.08, 4);
        const toothPos = [
            [-0.14, 0.28, 0.78], [0.14, 0.28, 0.78],
            [-0.15, 0.28, 0.65], [0.15, 0.28, 0.65],
            [-0.15, 0.28, 0.52], [0.15, 0.28, 0.52]
        ];
        toothPos.forEach(p => {
            const tooth = new THREE.Mesh(toothGeo, whiteMat);
            tooth.position.set(p[0], p[1], p[2]);
            tooth.rotation.x = Math.PI; // pointing down from upper jaw
            neckGroup.add(tooth);
        });

        // Glowing Dragon Slit Eyes
        const eyeBgGeo = new THREE.BoxGeometry(0.06, 0.14, 0.18);
        const leftEye = new THREE.Mesh(eyeBgGeo, glowMat);
        leftEye.position.set(-0.24, 0.5, 0.32);
        leftEye.rotation.y = -0.2;
        neckGroup.add(leftEye);

        const rightEye = new THREE.Mesh(eyeBgGeo, glowMat);
        rightEye.position.set(0.24, 0.5, 0.32);
        rightEye.rotation.y = 0.2;
        neckGroup.add(rightEye);
        this.petParts.leftEye = leftEye;
        this.petParts.rightEye = rightEye;

        // Fierce Eye Pupils
        const pupilGeo = new THREE.BoxGeometry(0.02, 0.12, 0.04);
        const leftPupil = new THREE.Mesh(pupilGeo, blackMat);
        leftPupil.position.set(-0.26, 0.5, 0.32);
        neckGroup.add(leftPupil);

        const rightPupil = new THREE.Mesh(pupilGeo, blackMat);
        rightPupil.position.set(0.26, 0.5, 0.32);
        neckGroup.add(rightPupil);

        // Dual Majestic Swept-Back Dragon Horns
        const hornGeo = new THREE.ConeGeometry(0.1, 0.75, 8);
        const leftHorn = new THREE.Mesh(hornGeo, glowMat);
        leftHorn.position.set(-0.22, 0.75, -0.05);
        leftHorn.rotation.set(-0.55, 0.15, 0.35);
        leftHorn.castShadow = true;
        neckGroup.add(leftHorn);

        const rightHorn = new THREE.Mesh(hornGeo, glowMat);
        rightHorn.position.set(0.22, 0.75, -0.05);
        rightHorn.rotation.set(-0.55, -0.15, -0.35);
        rightHorn.castShadow = true;
        neckGroup.add(rightHorn);

        // Cheek Horn Spikes
        const cheekSpikeGeo = new THREE.ConeGeometry(0.06, 0.3, 5);
        const leftCheek = new THREE.Mesh(cheekSpikeGeo, secMat);
        leftCheek.position.set(-0.26, 0.36, 0.15);
        leftCheek.rotation.set(0, 0, 1.2);
        neckGroup.add(leftCheek);

        const rightCheek = new THREE.Mesh(cheekSpikeGeo, secMat);
        rightCheek.position.set(0.26, 0.36, 0.15);
        rightCheek.rotation.set(0, 0, -1.2);
        neckGroup.add(rightCheek);

        // Head Spine Fins
        for (let i = 0; i < 3; i++) {
            const finGeo = new THREE.ConeGeometry(0.05, 0.18, 4);
            const fin = new THREE.Mesh(finGeo, secMat);
            fin.position.set(0, 0.62 - i * 0.12, 0.05 - i * 0.1);
            fin.rotation.x = -0.6;
            neckGroup.add(fin);
        }

        // Inner Fire Breath Throat Glow
        const throatGlow = new THREE.PointLight(secMat.color, 2.0, 2.5);
        throatGlow.position.set(0, 0.35, 0.5);
        neckGroup.add(throatGlow);

        // --- 3. DORSAL SPINE PLATES ON BACK ---
        for (let i = 0; i < 5; i++) {
            const spineGeo = new THREE.ConeGeometry(0.07, 0.22, 4);
            const spine = new THREE.Mesh(spineGeo, secMat);
            spine.position.set(0, 0.3 - i * 0.14, -0.22 - i * 0.02);
            spine.rotation.x = -0.4;
            torsoGroup.add(spine);
        }

        // --- 4. MAJESTIC ARTICULATED DRAGON WINGS ---
        const wingMat = new THREE.MeshStandardMaterial({
            color: secMat.color,
            side: THREE.DoubleSide,
            roughness: 0.35,
            metalness: 0.2,
        });

        // Left Wing Group
        const leftWingGroup = new THREE.Group();
        leftWingGroup.position.set(-0.32, 0.3, -0.05);
        torsoGroup.add(leftWingGroup);
        this.petParts.leftWing = leftWingGroup;

        // Upper Wing Bone/Arm
        const wingArmGeo = new THREE.CylinderGeometry(0.06, 0.08, 0.7, 8);
        const lWingArm = new THREE.Mesh(wingArmGeo, priMat);
        lWingArm.position.set(-0.35, 0.3, 0);
        lWingArm.rotation.z = 0.9;
        leftWingGroup.add(lWingArm);

        // Top Wing Apex Claw
        const wingClawGeo = new THREE.ConeGeometry(0.05, 0.2, 5);
        const lWingClaw = new THREE.Mesh(wingClawGeo, whiteMat);
        lWingClaw.position.set(-0.65, 0.62, 0);
        lWingClaw.rotation.z = -0.4;
        leftWingGroup.add(lWingClaw);

        // Wing Membrane Mesh
        const lMembraneGeo = new THREE.BufferGeometry();
        const lMemVertices = new Float32Array([
            0, 0, 0,
            -0.65, 0.62, 0,
            -1.1, 0.25, -0.1,

            0, 0, 0,
            -1.1, 0.25, -0.1,
            -0.85, -0.35, -0.05,

            0, 0, 0,
            -0.85, -0.35, -0.05,
            -0.4, -0.4, 0
        ]);
        lMembraneGeo.setAttribute('position', new THREE.BufferAttribute(lMemVertices, 3));
        lMembraneGeo.computeVertexNormals();
        const lWingMembrane = new THREE.Mesh(lMembraneGeo, wingMat);
        leftWingGroup.add(lWingMembrane);

        // Right Wing Group
        const rightWingGroup = new THREE.Group();
        rightWingGroup.position.set(0.32, 0.3, -0.05);
        torsoGroup.add(rightWingGroup);
        this.petParts.rightWing = rightWingGroup;

        // Right Upper Wing Bone/Arm
        const rWingArm = new THREE.Mesh(wingArmGeo, priMat);
        rWingArm.position.set(0.35, 0.3, 0);
        rWingArm.rotation.z = -0.9;
        rightWingGroup.add(rWingArm);

        // Right Top Wing Apex Claw
        const rWingClaw = new THREE.Mesh(wingClawGeo, whiteMat);
        rWingClaw.position.set(0.65, 0.62, 0);
        rWingClaw.rotation.z = 0.4;
        rightWingGroup.add(rWingClaw);

        // Right Wing Membrane Mesh
        const rMembraneGeo = new THREE.BufferGeometry();
        const rMemVertices = new Float32Array([
            0, 0, 0,
            0.65, 0.62, 0,
            1.1, 0.25, -0.1,

            0, 0, 0,
            1.1, 0.25, -0.1,
            0.85, -0.35, -0.05,

            0, 0, 0,
            0.85, -0.35, -0.05,
            0.4, -0.4, 0
        ]);
        rMembraneGeo.setAttribute('position', new THREE.BufferAttribute(rMemVertices, 3));
        rMembraneGeo.computeVertexNormals();
        const rWingMembrane = new THREE.Mesh(rMembraneGeo, wingMat);
        rightWingGroup.add(rWingMembrane);

        // --- 5. FOUR CLAWED DRAGON LEGS ---
        const legGeo = new THREE.CylinderGeometry(0.12, 0.1, 0.45, 8);
        const footGeo = new THREE.BoxGeometry(0.22, 0.1, 0.28);
        const clawGeo = new THREE.ConeGeometry(0.04, 0.12, 4);

        // Helper to build a clawed foot
        const buildClawedLeg = (x, y, z, rotX, rotZ) => {
            const legG = new THREE.Group();
            legG.position.set(x, y, z);
            legG.rotation.set(rotX, 0, rotZ);

            const legMesh = new THREE.Mesh(legGeo, priMat);
            legMesh.position.y = -0.2;
            legMesh.castShadow = true;
            legG.add(legMesh);

            const footMesh = new THREE.Mesh(footGeo, priMat);
            footMesh.position.set(0, -0.42, 0.08);
            footMesh.castShadow = true;
            legG.add(footMesh);

            // 3 Sharp White Claws
            for (let c = -1; c <= 1; c++) {
                const claw = new THREE.Mesh(clawGeo, whiteMat);
                claw.position.set(c * 0.07, -0.42, 0.22);
                claw.rotation.x = Math.PI / 2;
                legG.add(claw);
            }
            return legG;
        };

        // Front Legs
        this.petGroup.add(buildClawedLeg(-0.34, 0.5, 0.25, 0.1, 0.15));
        this.petGroup.add(buildClawedLeg(0.34, 0.5, 0.25, 0.1, -0.15));

        // Rear Strong Haunches
        const rearThighGeo = new THREE.SphereGeometry(0.22, 12, 12);
        const lThigh = new THREE.Mesh(rearThighGeo, priMat);
        lThigh.position.set(-0.38, 0.45, -0.2);
        lThigh.scale.set(1, 1.3, 1.2);
        this.petGroup.add(lThigh);

        const rThigh = new THREE.Mesh(rearThighGeo, priMat);
        rThigh.position.set(0.38, 0.45, -0.2);
        rThigh.scale.set(1, 1.3, 1.2);
        this.petGroup.add(rThigh);

        this.petGroup.add(buildClawedLeg(-0.38, 0.35, -0.15, -0.2, 0.1));
        this.petGroup.add(buildClawedLeg(0.38, 0.35, -0.15, -0.2, -0.1));

        // --- 6. LONG SWAYING DRAGON TAIL WITH SPADE BLADE ---
        const tailRoot = new THREE.Group();
        tailRoot.position.set(0, 0.45, -0.4);
        this.petGroup.add(tailRoot);
        this.petParts.tail = tailRoot;

        // Multi-segment curved tail
        const tail1 = new THREE.Mesh(new THREE.CylinderGeometry(0.2, 0.28, 0.5, 8), priMat);
        tail1.position.set(0, 0, -0.25);
        tail1.rotation.x = -1.1;
        tail1.castShadow = true;
        tailRoot.add(tail1);

        const tail2 = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.2, 0.55, 8), priMat);
        tail2.position.set(0, 0.2, -0.65);
        tail2.rotation.x = -0.55;
        tail2.castShadow = true;
        tailRoot.add(tail2);

        const tail3 = new THREE.Mesh(new THREE.CylinderGeometry(0.06, 0.12, 0.5, 8), priMat);
        tail3.position.set(0, 0.35, -1.05);
        tail3.rotation.x = -0.15;
        tail3.castShadow = true;
        tailRoot.add(tail3);

        // Tail Spines
        for (let t = 0; t < 3; t++) {
            const tSpine = new THREE.Mesh(new THREE.ConeGeometry(0.05, 0.16, 4), secMat);
            tSpine.position.set(0, 0.1 + t * 0.12, -0.3 - t * 0.35);
            tSpine.rotation.x = -0.7;
            tailRoot.add(tSpine);
        }

        // Dragon Spade / Spearhead Tip Blade
        const spadeGeo = new THREE.ConeGeometry(0.22, 0.45, 4);
        const spade = new THREE.Mesh(spadeGeo, glowMat);
        spade.position.set(0, 0.38, -1.35);
        spade.rotation.set(Math.PI / 2, 0, Math.PI / 4); // sharp arrowhead
        tailRoot.add(spade);
    }

    buildOwlMesh(priMat, secMat, whiteMat, blackMat) {
        // Round Owl Body
        const bodyGeo = new THREE.SphereGeometry(0.65, 16, 16);
        const body = new THREE.Mesh(bodyGeo, priMat);
        body.position.y = 0.85;
        body.scale.set(0.9, 1.1, 0.9);
        body.castShadow = true;
        this.petGroup.add(body);
        this.petParts.body = body;

        // Big Face Mask
        const faceGeo = new THREE.SphereGeometry(0.48, 12, 12);
        const face = new THREE.Mesh(faceGeo, secMat);
        face.position.set(0, 0.95, 0.22);
        face.scale.set(0.9, 0.8, 0.5);
        this.petGroup.add(face);

        // Big Owl Eyes
        const eyeRimGeo = new THREE.CircleGeometry(0.18, 16);
        const leftEyeRim = new THREE.Mesh(eyeRimGeo, whiteMat);
        leftEyeRim.position.set(-0.2, 1.05, 0.52);
        this.petGroup.add(leftEyeRim);

        const rightEyeRim = new THREE.Mesh(eyeRimGeo, whiteMat);
        rightEyeRim.position.set(0.2, 1.05, 0.52);
        this.petGroup.add(rightEyeRim);

        const eyePupilGeo = new THREE.CircleGeometry(0.1, 16);
        const leftPupil = new THREE.Mesh(eyePupilGeo, blackMat);
        leftPupil.position.set(-0.2, 1.05, 0.53);
        this.petGroup.add(leftPupil);

        const rightPupil = new THREE.Mesh(eyePupilGeo, blackMat);
        rightPupil.position.set(0.2, 1.05, 0.53);
        this.petGroup.add(rightPupil);

        // Beak
        const beakGeo = new THREE.ConeGeometry(0.08, 0.22, 6);
        const beakMat = new THREE.MeshStandardMaterial({ color: 0xffa000 });
        const beak = new THREE.Mesh(beakGeo, beakMat);
        beak.rotation.x = Math.PI / 2 + 0.3;
        beak.position.set(0, 0.9, 0.55);
        this.petGroup.add(beak);

        // Tuft Ears
        const tuftGeo = new THREE.ConeGeometry(0.12, 0.35, 5);
        const leftTuft = new THREE.Mesh(tuftGeo, priMat);
        leftTuft.position.set(-0.35, 1.5, 0.05);
        leftTuft.rotation.z = 0.35;
        this.petGroup.add(leftTuft);

        const rightTuft = new THREE.Mesh(tuftGeo, priMat);
        rightTuft.position.set(0.35, 1.5, 0.05);
        rightTuft.rotation.z = -0.35;
        this.petGroup.add(rightTuft);
    }

    buildPhoenixMesh(priMat, secMat, whiteMat, blackMat, glowMat) {
        this.buildOwlMesh(priMat, secMat, whiteMat, blackMat);

        // Phoenix Fiery Crest
        const crestGeo = new THREE.ConeGeometry(0.15, 0.7, 6);
        const crest = new THREE.Mesh(crestGeo, glowMat);
        crest.position.set(0, 1.6, -0.1);
        crest.rotation.x = -0.4;
        this.petGroup.add(crest);

        // Glowing Flame Trail
        const flameLight = new THREE.PointLight(0xff3d00, 3, 4);
        flameLight.position.set(0, 1.2, 0);
        this.petGroup.add(flameLight);
    }

    buildCatMesh(priMat, secMat, whiteMat, blackMat) {
        this.buildFoxMesh(priMat, secMat, whiteMat, blackMat);
    }

    buildOtterMesh(priMat, secMat, whiteMat, blackMat) {
        this.buildFoxMesh(priMat, secMat, whiteMat, blackMat);
    }

    /* -------------------------------------------------------------
     * 6. INTERACTION & HATCHING ANIMATION SEQUENCES
     * -----------------------------------------------------------*/
    triggerWobble() {
        this.wobbleIntensity = 0.35;
        this.playSound('tap');
        this.spawnSparks(6);
    }

    /**
     * Animasi pukulan alat (palu dsb) — dipanggil view setelah POST pet.use-tool sukses.
     * Getarkan kamera, percikan, mainkan suara tap, lalu gambar ulang garis retak
     * mengikuti crack_stage terbaru di state.
     */
    useToolStrike() {
        if (!this.scene || !this.camera) return;

        this.playSound('tap');
        this.spawnSparks(14);
        this.shakeCamera(0.16);
        this.wobbleIntensity = Math.max(this.wobbleIntensity, 0.5);

        const stage = (typeof this.state.crack_stage === 'number')
            ? this.state.crack_stage
            : ((typeof this.state.crackStage === 'number') ? this.state.crackStage : 0);

        if (this.eggGroup) {
            this.buildCrackLines(stage);
        }
    }

    /**
     * Semprotkan beberapa hati 3D yang melayang naik lalu menghilang (~2 detik).
     * Untuk momen bertelur / hati bahagia — dipanggil view via window.PetGame.heartBurst().
     */
    heartBurst(count = 8) {
        if (!this.scene) return;

        if (!this.heartsGroup) {
            this.heartsGroup = new THREE.Group();
            this.scene.add(this.heartsGroup);
        }

        const now = (typeof this.currentTime === 'number') ? this.currentTime : 0;

        for (let i = 0; i < count; i++) {
            const geometry = this.buildHeartGeometry();
            const material = new THREE.MeshBasicMaterial({
                color: (i % 2 === 0) ? 0xff4081 : 0xff80ab,
                transparent: true,
                opacity: 0.95,
                side: THREE.DoubleSide,
                depthWrite: false,
            });
            const mesh = new THREE.Mesh(geometry, material);

            const scale = 0.14 + Math.random() * 0.16;
            mesh.scale.setScalar(0.01);
            mesh.position.set(
                (Math.random() - 0.5) * 1.5,
                0.7 + Math.random() * 0.6,
                (Math.random() - 0.5) * 0.8
            );
            mesh.rotation.z = (Math.random() - 0.5) * 0.7;
            this.heartsGroup.add(mesh);

            this.burstHearts.push({
                mesh,
                born: now,
                life: 1.7 + Math.random() * 0.6,
                scale,
                vel: new THREE.Vector3(
                    (Math.random() - 0.5) * 0.012,
                    0.016 + Math.random() * 0.012,
                    (Math.random() - 0.5) * 0.006
                ),
                spin: (Math.random() - 0.5) * 0.04,
            });
        }

        this.playSound('happy');
    }

    /** Geometri hati 2D sederhana (bentuk bezier) untuk heartBurst(). */
    buildHeartGeometry() {
        const s = new THREE.Shape();
        s.moveTo(0, 0.22);
        s.bezierCurveTo(0.02, 0.34, 0.16, 0.44, 0.3, 0.44);
        s.bezierCurveTo(0.56, 0.44, 0.6, 0.14, 0.6, 0.02);
        s.bezierCurveTo(0.6, -0.2, 0.34, -0.42, 0, -0.62);
        s.bezierCurveTo(-0.34, -0.42, -0.6, -0.2, -0.6, 0.02);
        s.bezierCurveTo(-0.6, 0.14, -0.56, 0.44, -0.3, 0.44);
        s.bezierCurveTo(-0.16, 0.44, -0.02, 0.34, 0, 0.22);
        return new THREE.ShapeGeometry(s, 12);
    }

    /** Update loop untuk hati hasil heartBurst() — dipanggil tiap frame di animate(). */
    updateBurstHearts() {
        if (!this.burstHearts.length) return;

        for (let i = this.burstHearts.length - 1; i >= 0; i--) {
            const h = this.burstHearts[i];
            const age = this.currentTime - h.born;

            if (age >= h.life) {
                this.heartsGroup.remove(h.mesh);
                h.mesh.geometry.dispose();
                h.mesh.material.dispose();
                this.burstHearts.splice(i, 1);
                continue;
            }

            const t = Math.max(0, age / h.life);
            h.mesh.position.add(h.vel);
            h.vel.y *= 0.985;
            h.mesh.rotation.z += h.spin;
            h.mesh.scale.setScalar(h.scale * (t < 0.18 ? t / 0.18 : 1));
            h.mesh.material.opacity = (t < 0.6) ? 0.95 : 0.95 * (1 - (t - 0.6) / 0.4);
        }
    }

    async triggerHatchSequence() {
        if (this.state.isHatching) return;
        this.state.isHatching = true;

        this.playSound('crack');
        this.wobbleIntensity = 0.8;
        this.spawnSparks(16);
        this.shakeCamera(0.18);

        // Create 3D fractured shards explosion immediately
        this.createShellExplosion();
        this.playSound('fanfare');
        this.launchConfetti();

        // Call backend API to persist hatch & get rolled pet
        let pet = null;
        let hatchResult = null;
        try {
            const res = await fetch(`${this.options.apiUrl}/hatch`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.options.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({}),
            });
            hatchResult = await res.json();
            if (hatchResult && hatchResult.success) {
                pet = hatchResult.pet || hatchResult.user_pet?.pet;
            }
        } catch (err) {
            console.error('Hatch API Error:', err);
        }

        // Fallback default pet if offline or API delay
        if (!pet) {
            pet = {
                name: 'Sparky',
                species: 'Mystic Fox',
                rarity: 'epic',
                element: 'electric',
                color_primary: '#FF7043',
                color_secondary: '#FFE082',
                model_preset: 'fox',
                base_power: 50,
            };
        }

        // Update local state immediately
        this.state.status = 'hatched';
        this.state.life_stage = hatchResult?.life_stage || 'baby';
        this.state.pet = pet;
        this.state.nickname = hatchResult?.user_pet?.nickname || pet.name;
        this.state.level = hatchResult?.user_pet?.level || 1;
        this.state.experience = hatchResult?.user_pet?.experience || 0;
        this.state.maxExperience = hatchResult?.user_pet?.max_experience || 100;
        this.state.growthPercentage = 100;

        // Build the 3D Pet Creature immediately
        this.buildPet(pet);

        // Pop up emergence animation (Scale 0.01 -> skala tahap hidup)
        if (this.petGroup) {
            const targetScale = this.petGroup.scale.x || 1;
            this.petGroup.scale.set(0.01, 0.01, 0.01);
            let s = 0.01;
            const popInterval = setInterval(() => {
                s += 0.09;
                if (s >= 1.0) {
                    clearInterval(popInterval);
                    this.petGroup.scale.set(targetScale, targetScale, targetScale);
                    this.petJumpAnimation();
                } else {
                    const k = targetScale * s;
                    this.petGroup.scale.set(k, k, k);
                }
            }, 20);
        }

        if (typeof this.options.onHatched === 'function') {
            this.options.onHatched(hatchResult || { pet });
        }
        if (typeof this.options.onStateChange === 'function') {
            this.options.onStateChange(this.state);
        }

        this.state.isHatching = false;
    }

    createShellExplosion() {
        if (this.eggGroup) {
            this.rootGroup.remove(this.eggGroup);
            this.eggGroup = null;
        }

        this.shardsGroup = new THREE.Group();
        this.rootGroup.add(this.shardsGroup);

        const shardMat = new THREE.MeshStandardMaterial({
            color: 0xfef9e7,
            roughness: 0.4,
            flatShading: true,
            side: THREE.DoubleSide,
        });

        const shards = [];
        const shardCount = 18;

        for (let i = 0; i < shardCount; i++) {
            const sGeo = new THREE.TetrahedronGeometry(0.22 + Math.random() * 0.18, 0);
            const shard = new THREE.Mesh(sGeo, shardMat);
            shard.position.set(
                (Math.random() - 0.5) * 0.5,
                1.1 + (Math.random() - 0.5) * 0.6,
                (Math.random() - 0.5) * 0.5
            );
            this.shardsGroup.add(shard);

            shards.push({
                mesh: shard,
                vel: new THREE.Vector3(
                    (Math.random() - 0.5) * 0.18,
                    Math.random() * 0.16 + 0.08,
                    (Math.random() - 0.5) * 0.18
                ),
                rot: new THREE.Vector3(
                    (Math.random() - 0.5) * 0.3,
                    (Math.random() - 0.5) * 0.3,
                    (Math.random() - 0.5) * 0.3
                ),
            });
        }

        // Animate shards flying outward
        let shardFrames = 0;
        const animateShards = () => {
            shardFrames++;
            shards.forEach(s => {
                s.mesh.position.add(s.vel);
                s.mesh.rotation.x += s.rot.x;
                s.mesh.rotation.y += s.rot.y;
                s.vel.y -= 0.008; // Gravity
            });

            if (shardFrames < 60) {
                requestAnimationFrame(animateShards);
            } else {
                this.rootGroup.remove(this.shardsGroup);
                this.shardsGroup = null;
            }
        };
        animateShards();
    }

    petJumpAnimation() {
        if (!this.petGroup) return;
        this.playSound('happy');
        this.spawnHearts();
        this.jumpTime = Math.PI; // Trigger single safe jump cycle
    }

    feedAnimation() {
        this.playSound('munch');
        this.spawnSparks(10);
        this.petJumpAnimation();
    }

    spawnSparks(count = 8) {
        // Visual pop of sparks
        if (!this.particlesGroup) return;
        const positions = this.particlesGroup.geometry.attributes.position.array;
        for (let i = 0; i < count * 3; i += 3) {
            positions[i] = (Math.random() - 0.5) * 1.5;
            positions[i + 1] = 1.2 + (Math.random() - 0.5) * 0.8;
            positions[i + 2] = (Math.random() - 0.5) * 1.5;
        }
        this.particlesGroup.geometry.attributes.position.needsUpdate = true;
    }

    spawnHearts() {
        // Simple DOM floating hearts over canvas
        for (let i = 0; i < 5; i++) {
            const heart = document.createElement('div');
            heart.innerText = '💖';
            heart.style.position = 'absolute';
            heart.style.left = `${45 + (Math.random() - 0.5) * 30}%`;
            heart.style.top = `${40 + (Math.random() - 0.5) * 20}%`;
            heart.style.fontSize = '1.5rem';
            heart.style.pointerEvents = 'none';
            heart.style.transition = 'all 1s cubic-bezier(0.2, 0.8, 0.2, 1)';
            heart.style.opacity = '1';
            heart.style.transform = 'scale(0.5)';
            heart.style.zIndex = '30';
            this.container.appendChild(heart);

            setTimeout(() => {
                heart.style.transform = `translate(${(Math.random() - 0.5) * 80}px, -90px) scale(1.3)`;
                heart.style.opacity = '0';
            }, 50);

            setTimeout(() => {
                heart.remove();
            }, 1100);
        }
    }

    shakeCamera(intensity = 0.1) {
        const origX = this.camera.position.x;
        const origY = this.camera.position.y;
        let frames = 0;
        const shake = () => {
            frames++;
            this.camera.position.x = origX + (Math.random() - 0.5) * intensity;
            this.camera.position.y = origY + (Math.random() - 0.5) * intensity;
            if (frames < 12) {
                requestAnimationFrame(shake);
            } else {
                this.camera.position.x = origX;
                this.camera.position.y = origY;
            }
        };
        shake();
    }

    launchConfetti() {
        if (typeof confetti === 'function') {
            confetti({
                particleCount: 120,
                spread: 80,
                origin: { y: 0.6 },
                colors: ['#00E5FF', '#FFD700', '#FF4081', '#76FF03', '#7C4DFF'],
            });
        }
    }

    /* -------------------------------------------------------------
     * 7. EVENT LISTENERS & ORBIT CONTROLS
     * -----------------------------------------------------------*/
    setupEventListeners() {
        const dom = this.renderer.domElement;

        const onStart = (clientX, clientY) => {
            this.isDragging = true;
            this.previousMousePosition = { x: clientX, y: clientY };
            this.ensureAudioReady();
        };

        const onMove = (clientX, clientY) => {
            if (!this.isDragging) return;
            const deltaX = clientX - this.previousMousePosition.x;
            this.targetRotationY += deltaX * 0.012;
            this.previousMousePosition = { x: clientX, y: clientY };
        };

        const onEnd = () => {
            this.isDragging = false;
        };

        // Mouse Events
        dom.addEventListener('mousedown', e => onStart(e.clientX, e.clientY));
        window.addEventListener('mousemove', e => onMove(e.clientX, e.clientY));
        window.addEventListener('mouseup', onEnd);

        // Touch Events
        dom.addEventListener('touchstart', e => {
            if (e.touches.length === 1) {
                onStart(e.touches[0].clientX, e.touches[0].clientY);
            }
        }, { passive: true });

        window.addEventListener('touchmove', e => {
            if (e.touches.length === 1) {
                onMove(e.touches[0].clientX, e.touches[0].clientY);
            }
        }, { passive: true });

        window.addEventListener('touchend', onEnd);

        // Click / Tap on 3D Object
        dom.addEventListener('click', e => {
            const rect = dom.getBoundingClientRect();
            this.mouse.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
            this.mouse.y = -((e.clientY - rect.top) / rect.height) * 2 + 1;

            this.raycaster.setFromCamera(this.mouse, this.camera);
            const intersects = this.raycaster.intersectObjects(this.rootGroup.children, true);

            if (intersects.length > 0) {
                if (this.state.status === 'ready_to_hatch' || this.state.growthPercentage >= 100) {
                    this.triggerHatchSequence();
                } else if (this.state.status === 'egg') {
                    this.triggerWobble();
                } else if (this.state.status === 'hatched') {
                    this.petJumpAnimation();
                }
            }
        });

        // Window Resize
        window.addEventListener('resize', () => this.onWindowResize());
    }

    onWindowResize() {
        if (!this.container || !this.renderer || !this.camera) return;
        const width = this.container.clientWidth;
        const height = this.container.clientHeight;
        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(width, height);
    }

    /* -------------------------------------------------------------
     * 8. BACKEND API SYNC
     * -----------------------------------------------------------*/
    async fetchStatus() {
        try {
            const res = await fetch(`${this.options.apiUrl}/status`, {
                headers: { 'Accept': 'application/json' },
            });
            const data = await res.json();
            if (data.success) {
                this.updateState(data);
            }
        } catch (err) {
            console.warn('Pet status sync error:', err);
        }
    }

    updateState(data) {
        this.state = Object.assign(this.state, data);

        if (this.state.status === 'egg' || this.state.status === 'ready_to_hatch') {
            this.buildEgg(this.state.crack_stage);
        } else if (this.state.status === 'hatched') {
            this.buildPet(this.state.pet);
        }

        if (typeof this.options.onStateChange === 'function') {
            this.options.onStateChange(this.state);
        }
    }

    async feed() {
        try {
            const res = await fetch(`${this.options.apiUrl}/feed`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.options.csrfToken,
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();
            if (data.success) {
                this.feedAnimation();
                await this.fetchStatus();
                return data;
            }
        } catch (e) {
            console.error('Feed error:', e);
        }
    }

    async addPoints(amount = 25) {
        try {
            const res = await fetch(`${this.options.apiUrl}/add-points`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.options.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ amount }),
            });
            const data = await res.json();
            if (data.success) {
                this.triggerWobble();
                this.playSound('levelup');
                await this.fetchStatus();
                return data;
            }
        } catch (e) {
            console.error('Add points error:', e);
        }
    }

    async resetNewEgg() {
        try {
            const res = await fetch(`${this.options.apiUrl}/new-egg`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.options.csrfToken,
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();
            if (data.success) {
                this.state.strikeCount = 0;
                await this.fetchStatus();
                return data;
            }
        } catch (e) {
            console.error('Reset egg error:', e);
        }
    }

    /* -------------------------------------------------------------
     * 9. RENDER LOOP & REAL-TIME PROCEDURAL ANIMATION
     * -----------------------------------------------------------*/
    animate() {
        requestAnimationFrame(() => this.animate());

        const delta = this.clock.getDelta();
        const time = this.clock.getElapsedTime();
        this.currentTime = time;

        // Smooth Orbit Rotation
        this.currentRotationY += (this.targetRotationY - this.currentRotationY) * 0.1;
        this.rootGroup.rotation.y = this.currentRotationY;

        // Island Idle Float
        if (this.islandGroup) {
            this.islandGroup.position.y = Math.sin(time * 1.5) * 0.05;
        }

        // Egg Physics Wobble Animation
        if (this.eggGroup && this.wobbleIntensity > 0.001) {
            this.wobbleIntensity *= this.wobbleDecay;
            this.eggGroup.rotation.z = Math.sin(time * 24) * this.wobbleIntensity;
            this.eggGroup.rotation.x = Math.cos(time * 24) * this.wobbleIntensity * 0.6;
        }

        // Pet Procedural Idle, Flight & Jump Physics
        if (this.petGroup) {
            const preset = this.state.pet?.model_preset || 'dragon';
            const isFlyer = (preset === 'dragon' || preset === 'phoenix' || preset === 'owl');

            // 1. Natural Flight Hovering vs Ground Floating
            let hoverOffset = 0;
            if (isFlyer) {
                hoverOffset = Math.sin(time * 2.2) * 0.08;
            }

            // 2. Safe Bounded Jump Animation (cannot drift away into space)
            let jumpOffset = 0;
            if (this.jumpTime > 0) {
                this.jumpTime -= delta * 5.5;
                if (this.jumpTime < 0) this.jumpTime = 0;
                jumpOffset = Math.sin(this.jumpTime) * 0.38;
            }

            // Always assign strictly relative to basePetY
            this.petGroup.position.y = this.basePetY + hoverOffset + jumpOffset;

            // Breathing Motion (Chest expansion)
            if (this.petParts.body) {
                this.petParts.body.scale.y = 1 + Math.sin(time * 3) * 0.03;
                this.petParts.body.scale.x = 1 - Math.sin(time * 3) * 0.015;
            }

            // Head Idle Tilt
            if (this.petParts.head) {
                this.petParts.head.rotation.z = Math.sin(time * 1.8) * 0.06;
                this.petParts.head.rotation.x = Math.sin(time * 2.5) * 0.04;
            }

            // Tail Wagging
            if (this.petParts.tail) {
                this.petParts.tail.rotation.y = Math.sin(time * 4) * 0.35;
                this.petParts.tail.rotation.z = Math.sin(time * 2.8) * 0.12;
            }

            // Dragon / Phoenix Wing Flapping
            if (this.petParts.leftWing && this.petParts.rightWing) {
                const wingSpeed = isFlyer ? 5.5 : 3.5;
                const wingFlap = Math.sin(time * wingSpeed) * 0.45;
                this.petParts.leftWing.rotation.z = wingFlap;
                this.petParts.rightWing.rotation.z = -wingFlap;
            }
        }

        // Particles Floating
        if (this.particlesGroup) {
            this.particlesGroup.rotation.y = time * 0.1;
        }

        // Hati melayang hasil heartBurst()
        this.updateBurstHearts();

        this.renderer.render(this.scene, this.camera);
    }
}

window.Pet3DGame = Pet3DGame;

/**
 * Instance aktif untuk dipakai view (window.PetGame.useToolStrike() dsb).
 * Selalu ada agar aman dipanggil sekalipun instance belum siap.
 */
if (!window.PetGame) {
    window.PetGame = {
        useToolStrike() {},
        heartBurst() {},
    };
}
