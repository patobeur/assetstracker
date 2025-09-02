<script type="importmap">
	{
		"imports": {
		"three": "./node_modules_min/three/build/three.module.js",
		"three/addons/": "./node_modules_min/three/examples/jsm/"
		}
	}
</script>
<script>
        const pcs = JSON.parse('{{pcsJson}}');
        const timeline = JSON.parse('{{timelineJson}}');
        const eleves = {};
</script>
<script defer type="module" src="./js/three.js"></script>
