<?php

use BS\ModularFramework\Admin\AdminMenu;
use BS\ModularFramework\Admin\ModuleListTable;

defined( 'ABSPATH' ) || exit;

/** @var ModuleListTable $table */

$add_url = add_query_arg(
	array(
		'page'   => AdminMenu::MENU_SLUG_MODULES,
		'action' => 'add',
	),
	admin_url( 'admin.php' )
);
?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo esc_html__( 'Module', 'bs-modular-framework' ); ?></h1>
	<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action">
		<?php echo esc_html__( 'Neues Modul hinzufügen', 'bs-modular-framework' ); ?>
	</a>
	<hr class="wp-header-end" />

	<p class="description">
		<?php esc_html_e( 'Module sind Sammlungen von Feldern und Einträgen. Lege ein Modul an, um wiederverwendbare Datentypen (z. B. Events, Personen oder Standorte) zu definieren.', 'bs-modular-framework' ); ?>
	</p>

	<form method="post">
		<?php
		$table->display();
		?>
	</form>
</div>

