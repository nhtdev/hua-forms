# hua-forms

## Présentation

Librairie pour la gestion de formulaires en PHP.

Permet de valider automatiquement coté serveur les données selon les conditions de validation décrites dans le HTML.
Gestion automatique des bonnes pratiques sur le formulaire (accessibilité, html valide, sécurité...)

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

## Utilisation

TODO à expliquer avec des exemples

Expliquer la gestion des fichiers

## Fonctionnalités

### Correction automatique

* Ajoute et vérifie automatique un jeton de protection CSRF
* Ajoute automatiquement un attribut "id" correspondant au "name"
* Ajoute au type "text" aux champs input sans type
* Ajoute automatiquement un attribut "for" à l'élément `<label>`
* Ajoute automatiquement "[]" au name des `<select multiple>` et `<input type="file" multiple>`
* Définit automatiquement l'attribut "enctype" du formulaire s'il contient au moins un champ de type fichier

* TODO préfixe id
* TODO Convertit un attribut "label" en un élément html `<label>`
* TODO mettre des patterns pour les champs non gérés
* TODO optgroup
* TODO Gestion behaviours
* TODO Ajoute automatiquement un `<div>` englobant l'élément et son label
* TODO Bootstrap auto via behaviour, ajout div englobant
* TODO Validation du HTML et du contenu des attributs
* TODO Affichage conditionnel
* TODO options dynamiques
* TODO zones répétées ??
* TODO options coté code
* TODO upload asynchrone ?
* TODO option "frozen" (readonly, disabled, type hidden)
* TODO output, meter, progress
* TODO alertes sur l'utilisation d'attributs incorrects
* TODO mode debug
* TODO Patterns prédéfinis sur les mots de passe
* TODO captcha


## Types des champs

```
<input type="text" />
<textarea>
<select>
<select multiple>
<input type="checkbox" />
<input type="button" /> ou <button />
<input type="color" />
<input type="date" />
<input type="datetime-local" />
<input type="email" />
<input type="file" />
<input type="file" multiple/>
<input type="hidden" />
<input type="image" />
<input type="month" />
<input type="number" />
<input type="password" />
<input type="radio" />
<input type="range" />
<input type="reset" /> ou <button type="reset">
<input type="search" />
<input type="submit" /> ou <button type="submit">
<input type="tel" />
<input type="time" />
<input type="url" />
<input type="week" />

<select presentation="radio"> TODO
<select presentation="checkbox" multiple> TODO
<input type="date" presentation="text"> TODO
<input type="date" presentation="select"> TODO
```

## Attributs

### Règles de validation

Avancement : https://docs.google.com/spreadsheets/d/1QIE2pBO4QaZscYjnFgyP4sRwQ3k-Pv2CqI3gk062Jfg/edit?usp=sharing

Nom de l'attribut | Type | Description
--- | --- | ---
required | tag | Champ obligatoire
maxlength | int | Taille maximale d'un champ texte
minlength | int | Taille minimale d'un champ texte
pattern | string | Expression régulière à laquelle la valeur doit correspondre. Par exemple "[a-z]{4,8}" pour 4 à 8 lettres en minuscule
maxoptions TODO | int | Nombre maximal d'options pour un champ select multiple
minoptions TODO | int | Nombre minimal d'options pour un champ select multiple
minsize TODO | string | Taille minimale d'un champ fichier. Exemple : 1024, 1024B, 100KB, 1MB, 1GB...
maxsize TODO | string | Taille maximale d'un champ fichier. Exemple : 1024, 1024B, 100KB, 1MB, 1GB...
inarray | string | Liste des valeurs acceptées, séparées par des virgules. Défini automatiquement pour les éléments de type `<select>`
email | tag | Le champ doit contenir une adresse mail. Défini automatiquement pour les éléments `<input type="email"/>`
url | tag | Le champ doit contenir une URL. Défini automatiquement pour les éléments `<input type="url"/>`
color | tag | Le champ doit contenir une couleur au format #1234ab. Défini automatiquement pour les éléments `<input type="color"/>`
month | tag | Le champ doit contenir un mois + année. Défini automatiquement pour les éléments `<input type="month"/>`
month/min | string | Mois minimum au format "yyyy-mm"
month/max | string | Mois maximum au format "yyyy-mm"
month/step | string | Non géré
week | tag | Le champ doit contenir un numéro de semaine + année. Défini automatiquement pour les éléments `<input type="week"/>`
week/min | string | Numéro de semaine minimum au format "yyyy-Wnn"
week/max | string | Numéro de semaine maximum au format "yyyy-Wnn"
week/step | string | Non géré
date | tag | Le champ doit contenir une date (yyyy-mm-dd). Défini automatiquement pour les éléments `<input type="date"/>`
date/min | string | Date minimum au format "yyyy-mm-dd"
date/max | string | Date maximum au format "yyyy-mm-dd"
date/step | string | Non géré
time | tag | Le champ doit contenir une heure (hh:mm). Défini automatiquement pour les éléments `<input type="time"/>`
time/min | string | Heure minimum au format "hh:mm"
time/max | string | Heure maximum au format "hh:mm"
time/step | string | L'attribut step est un nombre qui définit la granularité de la valeur ou le mot-clé any. Seule les valeurs qui sont des multiples de cet attribut depuis le seuil min sont valides. Lorsque la chaîne de caractères any est utilisée, cela indique qu'aucun incrément spécifique n'est défini et que toute valeur (comprise entre min et max) est valide. Pour les champs de type time, la valeur de l'attribut step est exprimée en secondes (avec un facteur de multiplication de 1000). Par défaut, la valeur de l'incrément est 60, ce qui correspond à 1 minute.
datetime-local | tag | Le champ doit contenir une date + heure (yyyy-mm-ddThh:mm). Défini automatiquement pour les éléments `<input type="datetime-local"/>`
datetime-local/min | string | Date et heure minimum au format "yyyy-mm-ddThh:mm"
datetime-local/max | string | Date et heure maximum au format "yyyy-mm-ddThh:mm"
datetime-local/step | string | L'attribut step est un nombre qui définit la granularité de la valeur ou le mot-clé any. Seule les valeurs qui sont des multiples de cet attribut depuis le seuil min sont valides. Lorsque la chaîne de caractères any est utilisée, cela indique qu'aucun incrément spécifique n'est défini et que toute valeur (comprise entre min et max) est valide. Pour les champs datetime-local, la valeur de l'attribut step est exprimée en secondes avec un facteur d'amplification de 1000 (pour passer des millisecondes aux secondes). La valeur par défaut de step est 60 (soit 1 minute ou 60 000 millisecondes). Géré pour les heures seulement
number | tag | Le champ doit contenir un nombre. Défini automatiquement pour les éléments `<input type="number"/>` et `<input type="range"/>`
number/min | number | Le champ doit contenir un nombre de valeur supérieure ou égale au nombre spécifié
number/max | number | Le champ doit contenir un nombre de valeur inférieure ou égale au nombre spécifié
number/step | number | L'attribut step est un nombre qui définit la granularité de la valeur ou le mot-clé any. Seule les valeurs qui sont des multiples de cet attribut depuis le seuil min sont valides. Lorsque la chaîne de caractères any est utilisée, cela indique qu'aucun incrément spécifique n'est défini et que toute valeur (comprise entre min et max) est valide. 
accept | string | Pour les champs de type fichier, types de fichiers acceptés. Voir https://developer.mozilla.org/fr/docs/Web/HTML/Element/input/file#accept
upload-error-message | string | Pour les champs de type fichier, message d'erreur à afficher si erreur lors de l'upload

Pour chaque règle de validation, un attribut "rulename-message" peut être défini pour préciser un message d'erreur en remplacement du message standard.

### Formateurs de contenu

Nom de l'attribut | Type | Description
--- | --- | ---
trim | tag | Supprimer les espaces en début et fin d'un champ texte
uppercase | tag | Texte mis en majuscules TODO
lowercase | tag | Texte mis en majuscules TODO
capitalize | tag | Première lettre de chaque mot mise en majuscules TODO
number | tag | La valeur sera convertie en type numérique (int ou float). Défini automatiquement pour les éléments `<input type="number"/> et <input type="range"/>`

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

