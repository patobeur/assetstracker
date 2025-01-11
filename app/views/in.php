	<h1>Rendez un Pc !</h1>
	<div class="form-container">
		<form method="POST" action="in">
			{{errors}}
			<div class="blocs">
				{{msgpc}}
				<svg id="barcodePC"></svg>
				<div class="input-container">
					<span class="icon">🔒</span>
					<input type="text" id="codepc" name="pc" placeholder="codebarre pc" value="{{pcbarrecode}}">
				</div>
			</div>
			<div class="blocs center">
				<button type="submit">Check</button>
			</div>
		</form>
	</div>
	<script src="/js/in.js" defer></script>