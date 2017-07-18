<div class="login-box">
	<div class="login-box-logo">

	</div>
	<div class="login-box-body">
		<p align="center">
			<a href="/"><img src="{{ imageLogin }}" alt="{{ altImageLogin }}"></a>
		</p>
		<form id="formLogin" method="post" action="<?php echo $formAction; ?>" class="form">
			<p class="page-title">{{ titlePage }}</p>
			{% input_text name="email" model="email" label="Email" placeholder="Identifiant" %}
			{% input_password name="password" label="Password" model="password" value="" placeholder="Mot de passe" autocomplete="off" %}
			{% input_submit name="submit" value="login" formId="formLogin" label="Se connecter" class="btn-primary" %}
		</form>
		<p>
			Mot de passe oublié? <a href="forgotpassword">Cliquez ici</a>
		</p>
		<p>
			Pas encore de compte? {% link url="<?php echo $signupURL; ?>" content="S'inscrire" %}
		</p>
	</div>
</div>
