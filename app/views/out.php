	<h1>Empruntez un Pc !</h1>
	<div class="form-container">
		<form method="POST" action="">
			{{errors}}
			<div class="blocs">
				<!-- <label for="user">Pseudo Admin :</label> -->
				{{msgeleve}}
				<div class="input-container">
					<span class="icon">🤚</span>
					<input type="text" name="eleve" placeholder="codebarre élève" value="{{elevebarrecode}}">
				</div>
				<!-- <label for="pass">Mot de passe Admin :</label> -->
				{{msgpc}}
				<div class="input-container">
					<span class="icon">🔒</span>
					<input type="text" name="pc" placeholder="codebarre pc" value="{{pcbarrecode}}">
				</div>
			</div>
			<div class="blocs center">
					<button type="submit">Check</button>
			</div>
		</form>
	</div>