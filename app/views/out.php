	<h1>Empruntez un Pc !</h1>
	<div class="form-container">
                <form method="POST" action="out">
                        {{errors}}
                        <input type="hidden" name="csrf_token" value="{{csrf_token}}">
                        <div class="blocs">
			{{msgeleve}}
				<svg id="barcodeEleve"></svg>
				<div class="input-container">
					<span class="icon">🤚</span>
					<input type="text" id="codeeleve" name="eleve" placeholder="codebarre élève" value="{{elevebarrecode}}" required>
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
	<script src="/vendor/JsBarcode/JsBarcode.all.min.js"></script>
	<script defer type="module" src="/js/out.js"></script>