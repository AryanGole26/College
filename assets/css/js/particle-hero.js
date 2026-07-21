// assets/js/particle-hero.js
// Renders a floating medical-cross particle network into any element with id="particle-canvas"
(function () {
  const container = document.getElementById('particle-canvas');
  if (!container || typeof THREE === 'undefined') return;

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 1000);
  camera.position.z = 30;

  const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
  renderer.setSize(container.clientWidth, container.clientHeight);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  container.appendChild(renderer.domElement);

  // Particle field
  const COUNT = 220;
  const positions = new Float32Array(COUNT * 3);
  const speeds = new Float32Array(COUNT);
  for (let i = 0; i < COUNT; i++) {
    positions[i * 3] = (Math.random() - 0.5) * 60;
    positions[i * 3 + 1] = (Math.random() - 0.5) * 35;
    positions[i * 3 + 2] = (Math.random() - 0.5) * 30;
    speeds[i] = 0.05 + Math.random() * 0.15;
  }
  const geometry = new THREE.BufferGeometry();
  geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

  const material = new THREE.PointsMaterial({
    color: 0x93c5fd,
    size: 0.55,
    transparent: true,
    opacity: 0.9,
    blending: THREE.AdditiveBlending,
    depthWrite: false
  });
  const points = new THREE.Points(geometry, material);
  scene.add(points);

  // Connective lines between nearby particles (subtle network effect)
  const lineGeo = new THREE.BufferGeometry();
  const lineMat = new THREE.LineBasicMaterial({ color: 0x8b5cf6, transparent: true, opacity: 0.12 });
  const lineSegments = new THREE.LineSegments(lineGeo, lineMat);
  scene.add(lineSegments);

  function updateLines() {
    const pos = geometry.attributes.position.array;
    const linePositions = [];
    const maxDist = 8;
    for (let i = 0; i < COUNT; i++) {
      for (let j = i + 1; j < COUNT; j++) {
        const dx = pos[i * 3] - pos[j * 3];
        const dy = pos[i * 3 + 1] - pos[j * 3 + 1];
        const dz = pos[i * 3 + 2] - pos[j * 3 + 2];
        const dist = Math.sqrt(dx * dx + dy * dy + dz * dz);
        if (dist < maxDist) {
          linePositions.push(pos[i * 3], pos[i * 3 + 1], pos[i * 3 + 2]);
          linePositions.push(pos[j * 3], pos[j * 3 + 1], pos[j * 3 + 2]);
        }
      }
    }
    lineGeo.setAttribute('position', new THREE.Float32BufferAttribute(linePositions, 3));
  }

  let mouseX = 0, mouseY = 0;
  container.addEventListener('mousemove', (e) => {
    const rect = container.getBoundingClientRect();
    mouseX = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
    mouseY = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
  });

  let frame = 0;
  function animate() {
    requestAnimationFrame(animate);
    frame++;
    const pos = geometry.attributes.position.array;
    for (let i = 0; i < COUNT; i++) {
      pos[i * 3 + 1] += Math.sin(frame * 0.01 + i) * speeds[i] * 0.02;
    }
    geometry.attributes.position.needsUpdate = true;
    if (frame % 6 === 0) updateLines(); // throttle expensive line recompute

    camera.position.x += (mouseX * 4 - camera.position.x) * 0.03;
    camera.position.y += (-mouseY * 3 - camera.position.y) * 0.03;
    camera.lookAt(scene.position);

    points.rotation.y += 0.0006;
    lineSegments.rotation.y += 0.0006;

    renderer.render(scene, camera);
  }
  animate();

  window.addEventListener('resize', () => {
    camera.aspect = container.clientWidth / container.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.clientWidth, container.clientHeight);
  });
})();