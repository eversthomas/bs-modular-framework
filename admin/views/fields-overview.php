<?php

use BS\ModularFramework\Admin\AdminMenu;

defined( 'ABSPATH' ) || exit;

/** @var array<int,array<string,mixed>> $modules */

$current_module_id = isset( $_GET['module_id'] ) ? (int) wp_unslash( $_GET['module_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$action_url = add_query_arg(
	array(
		'page' => 'bs-modular-framework-fields',
	),
	admin_url( 'admin.php' )
);
?>
<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Felder verwalten', 'bs-modular-framework' ); ?>
	</h1>

	<p class="description">
		<?php esc_html_e( 'Felder gehören immer zu einem Modul. Wähle zunächst ein Modul aus, um dessen Felder zu definieren oder zu bearbeiten.', 'bs-modular-framework' ); ?>
	</p>

	<hr class="wp-header-end" />

	<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<input type="hidden" name="page" value="bs-modular-framework-fields" />

		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">
						<label for="bs_mf_fields_module_select">
							<?php esc_html_e( 'Modul auswählen', 'bs-modular-framework' ); ?>
						</label>
					</th>
					<td>
						<select name="module_id" id="bs_mf_fields_module_select">
							<option value="0">
								<?php esc_html_e( 'Bitte Modul wählen', 'bs-modular-framework' ); ?>
							</option>
							<?php foreach ( $modules as $module ) : ?>
								<option value="<?php echo esc_attr( (string) $module['id'] ); ?>" <?php selected( $current_module_id, (int) $module['id'] ); ?>>
									<?php echo esc_html( (string) $module['name'] ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if ( empty( $modules ) ) : ?>
							<p class="description">
								<?php
								printf(
									/* translators: %s: URL zur Modulliste. */
									esc_html__( 'Es sind noch keine Module vorhanden. Lege zuerst ein Modul an, um Felder zu definieren. Du findest die Modulliste unter „%s“.', 'bs-modular-framework' ),
									esc_html__( 'Module', 'bs-modular-framework' )
								);
								?>
							</p>
						<?php endif; ?>
					</td>
				</tr>
			</tbody>
		</table>

		<?php submit_button( esc_html__( 'Weiter zu den Feldern', 'bs-modular-framework' ) ); ?>
	</form>
</div>

