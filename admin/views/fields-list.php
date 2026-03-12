<?php

use BS\ModularFramework\Admin\AdminMenu;
use BS\ModularFramework\Domain\FieldDefinition;
use BS\ModularFramework\Domain\Module;

defined( 'ABSPATH' ) || exit;

/** @var Module $current_module */
/** @var FieldDefinition[] $fields */

$back_url = add_query_arg(
	array(
		'page' => AdminMenu::MENU_SLUG_MODULES,
	),
	admin_url( 'admin.php' )
);

$add_url = add_query_arg(
	array(
		'page'      => 'bs-modular-framework-fields',
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
				__( 'Felder für Modul: %s', 'bs-modular-framework' ),
				$current_module->name
			)
		);
		?>
	</h1>

	<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action">
		<?php esc_html_e( 'Neues Feld hinzufügen', 'bs-modular-framework' ); ?>
	</a>

	<a href="<?php echo esc_url( $back_url ); ?>" class="page-title-action">
		<?php esc_html_e( 'Zurück zur Modulliste', 'bs-modular-framework' ); ?>
	</a>

	<hr class="wp-header-end" />

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Label', 'bs-modular-framework' ); ?></th>
				<th><?php esc_html_e( 'Key', 'bs-modular-framework' ); ?></th>
				<th><?php esc_html_e( 'Typ', 'bs-modular-framework' ); ?></th>
				<th><?php esc_html_e( 'Pflicht', 'bs-modular-framework' ); ?></th>
				<th><?php esc_html_e( 'Sortierung', 'bs-modular-framework' ); ?></th>
				<th><?php esc_html_e( 'Aktionen', 'bs-modular-framework' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if ( empty( $fields ) ) : ?>
			<tr>
				<td colspan="6">
					<?php esc_html_e( 'Für dieses Modul sind noch keine Felder definiert.', 'bs-modular-framework' ); ?>
				</td>
			</tr>
		<?php else : ?>
			<?php foreach ( $fields as $field ) : ?>
				<tr>
					<td><?php echo esc_html( $field->label ); ?></td>
					<td><?php echo esc_html( $field->field_key ); ?></td>
					<td><?php echo esc_html( $field->field_type ); ?></td>
					<td><?php echo $field->is_required ? esc_html__( 'Ja', 'bs-modular-framework' ) : esc_html__( 'Nein', 'bs-modular-framework' ); ?></td>
					<td><?php echo esc_html( (string) $field->sort_order ); ?></td>
					<td>
						<?php
						$edit_url = add_query_arg(
							array(
								'page'      => 'bs-modular-framework-fields',
								'action'    => 'edit',
								'module_id' => $current_module->id,
								'id'        => $field->id,
							),
							admin_url( 'admin.php' )
						);

						$delete_url = wp_nonce_url(
							add_query_arg(
								array(
									'page'      => 'bs-modular-framework-fields',
									'action'    => 'delete',
									'module_id' => $current_module->id,
									'id'        => $field->id,
								),
								admin_url( 'admin.php' )
							),
							'bs_mf_delete_field_' . $field->id
						);
						?>
						<a href="<?php echo esc_url( $edit_url ); ?>">
							<?php esc_html_e( 'Bearbeiten', 'bs-modular-framework' ); ?>
						</a>
						|
						<a href="<?php echo esc_url( $delete_url ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Feld wirklich löschen?', 'bs-modular-framework' ) ); ?>');">
							<?php esc_html_e( 'Löschen', 'bs-modular-framework' ); ?>
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
</div>

