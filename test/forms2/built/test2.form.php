<form method="post" action="">
<input type="hidden" name="<?php echo htmlentities($this->getCsrfKey());  ?>" value="<?php echo htmlentities($this->getCsrfValue());  ?>"/>
	
	<?php if ($this->hasErrors()):  ?><div class="alert"><?php echo nl2br(htmlentities($this->getErrorsAsString()));  ?></div><?php endif; ?>
	
	<div>
		<label>Le Nom</label>
		<input type="text" name="name" required maxlength="60" trim="" value="<?php echo htmlentities($this->getValue('name'));  ?>"/>
	</div>
	
	<div>
		<label>Le Gender</label>
		<select name="gender" required>
			<option value="" selected="selected" <?php echo $this->attrSelected('gender', '');  ?>>- Aucune sÃ©lection -</option>
			<option value="M" <?php echo $this->attrSelected('gender', 'M');  ?>>Male</option>
			<option value="F" <?php echo $this->attrSelected('gender', 'F');  ?>>Female</option>
		</select>
	</div>

	<button type="submit" name="ok">OK</button>

</form>