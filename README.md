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