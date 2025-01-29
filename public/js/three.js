import * as THREE from "three";
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import * as SkeletonUtils from 'three/addons/utils/SkeletonUtils.js';
import { FontLoader } from 'three/addons/loaders/FontLoader.js';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { TextGeometry } from 'three/addons/geometries/TextGeometry.js';
// import { _client } from './client.js'
let Font;

let scene,camera,renderer,clock;
let controls;
let container;
	


document.addEventListener("DOMContentLoaded", () => {
    if(THREE) start();
})
function start() {
    console.log(THREE)
    const loader = new FontLoader();
    loader.load('./node_modules_min/three/examples/fonts/helvetiker_regular.typeface.json', (font) => {
        Font = font;
        go();
    })
}

function go() {
    clock = new THREE.Clock();
    createScene();
    addAmbiance();
    addElements();
    addOrbitControls();
    addScene();
    //-------
    addPcs();
    addTimeline();
    //-------
    animate();
    window.addEventListener('resize', onWindowResize);
    function onWindowResize() {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    }
}
function animate() {
    
    const delta = clock.getDelta();

    requestAnimationFrame(animate);
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    
    controls.update();
    renderer.render(scene, camera);
}
//------------------------------------------
function createScene(){
    // Créer une nouvelle scène 3D
    scene = new THREE.Scene();

    // Créer une caméra perspective
    camera = new THREE.PerspectiveCamera(40, window.innerWidth / window.innerHeight, 1, 1000);
    camera.position.set(0,15,20);

    // Créer un rendu WebGL
    renderer = new THREE.WebGLRenderer({antialias: true});
    renderer.setPixelRatio( window.devicePixelRatio );
    renderer.setSize( window.innerWidth, window.innerHeight );
    renderer.shadowMap.enabled = true;

    // Configuration du rendu
    renderer.outputEncoding = THREE.sRGBEncoding;
    renderer.shadowMap.enabled = true;
}
function addElements(){
    
    const grid = new THREE.GridHelper( 11, 11, 0x000000, 0x000000 );
    grid.material.opacity = 0.7;
    grid.material.transparent = true;
    scene.add( grid );


    // Créer le sol
    const groundGeometry = new THREE.BoxGeometry(1000, 0.5, 1000);
    const groundMaterial = new THREE.MeshPhongMaterial({
        color: 0xfafafa
    });
    const groundMesh = new THREE.Mesh(groundGeometry, groundMaterial);
    groundMesh.receiveShadow = true;
    // groundMesh.castShadow = true;
    groundMesh.position.y = -0.25;
    scene.add(groundMesh);
}
function addAmbiance(){
    // Créer un brouilalrd d'ambiance
    scene.fog = new THREE.Fog(0x000020, 10, 120);
    scene.background = new THREE.Color( 0x000020  );
                
    // const hemiLight = new THREE.HemisphereLight( 0xffffff, 0x8d8d8d, 1 );
    // hemiLight.position.set( 0, 20, 0 );
    // scene.add( hemiLight );

				const dirLight = new THREE.DirectionalLight( 0xffffff, 1 );
				// dirLight.position.set( - 3, 10, - 10 );
                dirLight.position.set( 0, 30, -10 );
				dirLight.castShadow = true;
				dirLight.shadow.camera.top = 40;
				dirLight.shadow.camera.bottom = - 40;
				dirLight.shadow.camera.left = - 40;
				dirLight.shadow.camera.right = 40;
				dirLight.shadow.camera.near = 0.1;
				dirLight.shadow.camera.far = 50;
				scene.add( dirLight );


    // Créer une lumière ambiante
    const ambient = new THREE.AmbientLight(0xffffff, 1);
    scene.add(ambient);

    // const hemiLight = new THREE.HemisphereLight( 0xffffff, 0x8d8d8d, 1 );
    // hemiLight.position.set( 0, 20, 0 );
    // scene.add( hemiLight );

    // Créer une lampe projecteur (spotlight)
    // const spotLight = new THREE.SpotLight(0xffffff, .5);
    // spotLight.position.set(0, 0, 20);
    // spotLight.angle = Math.PI / 3;
    // spotLight.penumbra = 0.5;
    // spotLight.decay = 1;
    // spotLight.distance = 300;

    // // Activer les ombres pour la lampe projecteur
    // spotLight.castShadow = true;
    // spotLight.shadow.mapSize.width = 512;
    // spotLight.shadow.mapSize.height = 512;
    // spotLight.shadow.camera.near = 1;
    // spotLight.shadow.camera.far = 300;
    // spotLight.shadow.focus = 1;

    // // Ajouter la lampe projecteur, son assistant, et sa cible à la scène
    // scene.add(spotLight, spotLight.target);

    // // Créer un assistant visuel pour la lampe projecteur
    // const slHelper = new THREE.SpotLightHelper(spotLight);
    // scene.add(slHelper);
}
function addOrbitControls(){
    // Contrôles pour déplacer la caméra
    controls = new OrbitControls(camera, renderer.domElement);
}
function addScene(){
    // Ajouter le rendu au dom
    container = document.getElementById('container')
    renderer.domElement.style.position = 'absolute';
    renderer.domElement.style.top = '0';
    renderer.domElement.style.left = '0';
    container.appendChild(renderer.domElement);
}
//------------------------------------------
function getTextMesh(message) {
    const textGeometry = new TextGeometry(message, {
        font: Font,
        size: .3,
        height: 0.05,
        curveSegments: 12,
        bevelEnabled: true,
        bevelThickness: 0.01,
        bevelSize: 0.02,
        bevelSegments: 5,
    });

    const textMaterial = new THREE.MeshStandardMaterial({
        color: 0x000000
    });
    const textMesh = new THREE.Mesh(textGeometry, textMaterial);

    return textMesh;
}
//------------------------------------------
function addTimeline(){
    // La timeline
    let size = {x: 0.9,y: 0.9,z: 0.9};
    let gap = {x: 0.1};
    let max = 10;
    let length = timeline.length > max ? max : pcs.length;
    let start = 0 - (Math.floor(length/2) * size.x) - (Math.floor(length/2) * gap.x )
    let pos = {x: start, y: size.z / 2 , z: 2};
    let count = 0;
    timeline.forEach(element => {
        // Créer un cube (id,birth,typeaction,ideleves,idpc)
        let color = (element.typeaction === 'in') ? 0x00ff00 : 0xff0000;
        let pc = new THREE.BoxGeometry(size.x, size.y, size.z);
        let pcMate = new THREE.MeshPhongMaterial({color:color});
        let pcMesh = new THREE.Mesh(pc, pcMate);
        pcMesh.castShadow = true;
		pcMesh.receiveShadow = true;
        pcMesh.position.x = pos.x;
        pcMesh.position.y = pos.y;
        pcMesh.position.z = pos.z;
        pcMesh.rotation.x = -Math.PI/2;

        let texte = getTextMesh(element.idpc.toString());
        
        pcMesh.add(texte);
        texte.position.z = size.z / 2;
        pos.x += size.x + gap.x
        scene.add(pcMesh);
        count++;
        if(count>max) {count=0;pos.x=start;pos.z+=size.z+gap.x}
    });
}
function addPcs(){
    let size = {x: 0.9,y: 0.9,z: 0.9};
    let gap = {x: 0.1};
    let start = 0 - (Math.round(Math.sqrt(pcs.length)) * (size.x + gap.x ))
    console.log(start)
    let pos = {x: start/2, y: size.z / 2, z: start/2};

    pcs.forEach(element => {
        // Créer un cube
        // Object { 
        // id: 1, barrecode: "10000001", model: "Dell Inspiron", 
        // serialnum: "SN12345", birth: "2025-01-18 16:07:36", 
        // etat: "Disponible", typeasset_id: 1, position: "in",
        //  lasteleve_id: null }
        let color = element.position === 'in' ? 0x00ff00 : 0xff0000;
        let pc = new THREE.BoxGeometry(size.x, size.x, size.x);
        let pcMate = new THREE.MeshPhongMaterial({
            color: color
        });
        let pcMesh = new THREE.Mesh(pc, pcMate);
        pcMesh.castShadow = true;
		pcMesh.receiveShadow = true;
        pcMesh.position.x = pos.x + 0;
        pcMesh.position.y = pos.y + 0;
        pcMesh.position.z = pos.z + 0;
        pcMesh.rotation.x = -Math.PI/2;
        let texte = getTextMesh(element.id.toString());


        pcMesh.add(texte);
        // texte.position.set(pos.x+0, pos.y+0, pos.z+0);
        texte.position.z = size.z / 2;
        pos.x += size.x + gap.x
        scene.add(pcMesh);
    });
}