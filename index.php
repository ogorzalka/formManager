<?php
require_once 'inc/methods.inc.php';
include 'tpl/head.inc.php';
// construction du formulaire
$form = new FormManager;
$form->openForm('post', '.');
$form->openSection(array('class'=>'section'));
$form->showErrors('%totalErrors% erreur(s) detectée(s) lors de la validation du formulaire :');
$form->insert('input', 'text', 'nom', array('label'=>'Votre nom', 'errorLabel' => 'votre nom', 'class'=>'text'), true);
$form->insert('input', 'text', 'prenom', array('label'=>'Votre prénom', 'errorLabel' => 'votre prénom', 'class'=>'text'), true);
$form->insert('input', 'email', 'email', array('label'=>'Votre adresse e-mail', 'errorLabel' => 'votre adresse e-mail', 'class'=>'text'), true);
$form->insert('select', array('1','2','3','4','5'), 'nbre_personne', array('label'=>'Nombre de personnes', 'errorLabel' => 'le nombre de personnes', 'class'=>'text'));
$form->insert('input', 'submit', 'Envoyer', array('p.class'=>'submit'));
$form->getErrors();
$form->closeSection();
$form->closeForm();

$confirm = false;
if($form->success()) {
	$confirm = '<p class="confirm"><strong>Votre demande d\'inscription a bien été prise en compte</p>';
}
?>
	<body>
		<div id="container">
			<?php include 'tpl/header.inc.php'; ?>
			<div class="formSection">
				<section id="form">
					<?php if($confirm != false): ?>
						<?php echo $confirm; ?>
					<?php else: ?>
					<?php echo $form->printForm(); ?>
					<?php endif; ?>
				</section>	
			</div>
			<?php include 'tpl/footer.inc.php'; ?>
		</div>
	</body>
</html>