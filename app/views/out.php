	<h1>Empruntez un Pc !</h1>
	<div class="form-container">
		<form method="POST" action="out">
			{{errors}}
			<div class="blocs">
				{{msgeleve}}
				<svg id="barcodeEleve"></svg>
				<div class="input-container">
					<span class="icon">🤚</span>
					<input type="text" id="codeeleve" name="eleve" placeholder="codebarre élève" value="{{elevebarrecode}}">
				</div>
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
	<script src="/js/out.js" defer></script>