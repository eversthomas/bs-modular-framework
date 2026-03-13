<?php

use BS\ModularFramework\Admin\AdminMenu;
use BS\ModularFramework\Admin\EntryListTable;
use BS\ModularFramework\Domain\Module;

defined( 'ABSPATH' ) || exit;

/** @var Module $current_module */
/** @var EntryListTable $table */

$back_url = add_query_arg(
	array(
		'page' => AdminMenu::MENU_SLUG_MODULES,
	),
	admin_url( 'admin.php' )
);

$add_url = add_query_arg(
	array(
		'page'      => 'bs-modular-framework-entries',
		'action'    => 'add',
		'module_id' => $current_module->id,
	),
	admin_url( 'admin.php' )
);
?>
<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php
		echo esc_html(
			sprintf(
				/* translators: %s: Modulname. */
				__( 'Einträge für Modul: %s', 'bs-modular-framework' ),
				$current_module->name
			)
		);
		?>
	</h1>

	<p class="description">
		<?php
		printf(
			/* translators: %s: Modulname. */
			esc_html__( 'Einträge sind konkrete Datensätze des Moduls „%s“, z. B. einzelne Inhalte, Personen oder Standorte.', 'bs-modular-framework' ),
			$current_module->name
		);
		?>
	</p>

	<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action">
		<?php esc_html_e( 'Neuen Eintrag hinzufügen', 'bs-modular-framework' ); ?>
	</a>

	<a href="<?php echo esc_url( $back_url ); ?>" class="page-title-action">
		<?php esc_html_e( 'Zurück zur Modulliste', 'bs-modular-framework' ); ?>
	</a>

	<hr class="wp-header-end" />

	<form method="post">
		<?php
		$table->display();
		?>
	</form>
</div>

