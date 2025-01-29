<script type="importmap">
	{
		"imports": {
		"three": "./node_modules_min/three/build/three.module.js",
		"three/addons/": "./node_modules_min/three/examples/jsm/"
		}
	}
</script>
<script>	
	const pcs = {{pcsJson}}; 
	const timeline = {{timelineJson}}; 
	const eleves = {}; 
</script>
<script defer type="module" src="./js/three.js"></script>