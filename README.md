# hua-forms

## Présentation

Librairie pour la gestion de formulaires en PHP.
Le formulaire est écrit en HTML et analysé pour être validé et traité en PHP.

*test.form.html*

```html
<form method="post" action="">

	<div>
		<label>Name</label>
		<input type="text" name="name" required maxlength="60" trim>
	</div>
	
	<div>
		<label>Gender</label>
		<select name="gender" required>
			<option value="" selected>- No selection -</option>
			<option value="M">Male</option>
			<option value="F">Female</option>
		</select>
	</div>

	<button type="submit" name="ok">OK</button>

</form>
```

*index.php*

```php
$form = \HuaForms\Factory::form('test');
if ($form->isSubmitted() && $form->validate()) {
    echo '<pre>';
    var_dump($form->exportValues());
    echo '</pre>';
} else {
	echo $form->render();
}
```

## Fonctionnalités

### Correction automatique

## Types des champs

## Attributs

## Options de configuration

Option | Valeur par défaut | Description
--- | --- | ---
formPath | forms/ | Chemin vers les fichiers sources des formulaires
srcExtension | form.html | Extension des fichiers sources des formulaires
builtPath | forms/built | Répertoire de compilation des formulaires
builtTplExtension | form.php | Extension pour les fichiers compilés Template PHP
builtJsonExtension | form.json | extension pour les fichiers compilés JSON
csrfKey | csrf | Clé des tokens CSRF
csrfClass | \HuaForms\Csrf\PhpSession | Classe utilisée pour le stockage serveur des tokens CSRF
csrfOptions | [] | Options pour le stockage des tokens CSRF

