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

* Ajoute et vérifie automatique un jeton de protection CSRF
* Ajoute automatiquement un attribut "id" correspondant au "name"
* Ajoute au type "text" aux champs input sans type
* TODO Convertit un attribut "label" en un élément html <label>
* Ajoute automatiquement un attribut "for" à l'élément <label>
* TODO Ajoute automatiquement un <div> englobant l'élément et son label

## Types des champs

```
<input type="text" />
<textarea>
<select>
<input type="email" />
<input type="url" />
<input type="tel" /> TODO
<input type="number" />
<input type="range" /> TODO
<input type="color" /> TODO
<input type="date" /> TODO
<input type="search" /> TODO
<input type="checkbox" /> TODO
<input type="radio" /> TODO
<input type="password" /> TODO
<button />
<button type="submit">
<select presentation="radio">
<select presentation="checkbox" multiple>
```

## Attributs

### Règles de validation

Nom de l'attribut | Type | Description
--- | --- | ---
required | tag | Champ obligatoire
maxlength | int | Taille maximale d'un champ texte
minlength TODO | int | Taille maximale d'un champ texte
maxoptions TODO | int | Nombre maximal d'options pour un champ select multiple
minoptions TODO | int | Nombre minimal d'options pour un champ select multiple
inarray | string | Liste des valeurs acceptées, séparées par des virgules. Défini automatiquement pour les éléments de type <select>
email | tag | Le champ doit contenir une adresse mail. Défini automatiquement pour les éléments <input type="email"/>
url | tag | Le champ doit contenir une URL. Défini automatiquement pour les éléments <input type="url"/>
regex TODO | string | Le champ texte doit valider une expression régulière
number | tag | Le champ doit contenir un nombre. Défini automatiquement pour les éléments <input type="number"/> et <input type="range"/>
number/min | number | Le champ doit contenir un nombre de valeur supérieure ou égale au nombre spécifié
number/max | number | Le champ doit contenir un nombre de valeur inférieure ou égale au nombre spécifié
number/step | number | L'attribut step est un nombre qui définit la granularité de la valeur ou le mot-clé any. Seule les valeurs qui sont des multiples de cet attribut depuis le seuil min sont valides. Lorsque la chaîne de caractères any est utilisée, cela indique qu'aucun incrément spécifique n'est défini et que toute valeur (comprise entre min et max) est valide. Pour chaque règle de validation, un attribut "rulename-message" peut être défini pour préciser un message d'erreur
en remplacement du message standard.

### Formateurs de contenu

Nom de l'attribut | Type | Description
--- | --- | ---
trim | tag | Supprimer les espaces en début et fin d'un champ texte
uppercase | tag | Texte mis en majuscules TODO
lowercase | tag | Texte mis en majuscules TODO
capitalize | tag | Première lettre de chaque mot mise en majuscules TODO
number | tag | La valeur sera convertie en type numérique (int ou float). Défini automatiquement pour les éléments <input type="number"/> et <input type="range"/>

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
cache | true | Mise en cache du parsing du formulaire

