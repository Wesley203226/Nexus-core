<template>
  <div ref="container" class="fixed top-0 left-0 w-full h-full -z-10 pointer-events-none"></div>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import * as THREE from 'three';

const container = ref(null);
let scene, camera, renderer, particles, starGeo;

const init = () => {
  scene = new THREE.Scene();
  
  camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 1, 1000);
  camera.position.z = 1;
  camera.rotation.x = Math.PI/2;

  renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
  renderer.setSize(window.innerWidth, window.innerHeight);
  container.value.appendChild(renderer.domElement);

  // Starfield Geometry
  starGeo = new THREE.BufferGeometry();
  const points = [];
  for(let i=0; i<6000; i++) {
    let star = new THREE.Vector3(
      Math.random() * 600 - 300,
      Math.random() * 600 - 300,
      Math.random() * 600 - 300
    );
    points.push(star);
  }
  starGeo.setFromPoints(points);

  let starMaterial = new THREE.PointsMaterial({
    color: 0x6366f1,
    size: 0.5,
    transparent: true,
    opacity: 0.3,
    blending: THREE.AdditiveBlending
  });

  particles = new THREE.Points(starGeo, starMaterial);
  scene.add(particles);

  // Ambient Light
  const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
  scene.add(ambientLight);

  // Animation
  const animate = () => {
    const positions = starGeo.attributes.position.array;
    for(let i=0; i<positions.length; i+=3) {
      positions[i+1] -= 0.1; // Move down (Y)
      if(positions[i+1] < -300) {
        positions[i+1] = 300;
      }
    }
    starGeo.attributes.position.needsUpdate = true;
    particles.rotation.y += 0.0005;

    renderer.render(scene, camera);
    requestAnimationFrame(animate);
  };
  animate();

  window.addEventListener('resize', handleResize);
};

const handleResize = () => {
  camera.aspect = window.innerWidth / window.innerHeight;
  camera.updateProjectionMatrix();
  renderer.setSize(window.innerWidth, window.innerHeight);
};

onMounted(() => {
  init();
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
  if (renderer) {
    renderer.dispose();
  }
});
</script>
