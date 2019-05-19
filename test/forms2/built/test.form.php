<form method="post" action="">

	<?php if ($this->hasErrors()): ?>
    <div class="alert alert-danger" role="alert">
       <?= nl2br($this->getErrorsAsString()); ?>
    </div>
    <?php endif; ?>

	<input type="hidden" name="<?= htmlentities($this->getCsrfKey()); ?>" value="<?= htmlentities($this->getCsrfValue()); ?>" />

	<div class="form-group">
		<label for="test_name">Nom</label>
		<input type="text" id="test_name" name="name" value="<?= htmlentities($this->getValue('name')); ?>" class="form-control" />
	</div>
	
	<div class="form-group">
		<label form="test_gender">Gender</label>
		<select id="test_gender" name="gender" class="form-control">
			<option value="">- Aucune sélection -</option>
			<option value="M"<?= $this->attrSelected('gender', 'M'); ?>>Male</option>
			<option value="F"<?= $this->attrSelected('gender', 'F'); ?>>Female</option>
		</select>
	</div>

	<button type="submit" class="btn btn-primary" name="ok">OK</button>

</form>