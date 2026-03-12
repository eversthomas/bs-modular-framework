<?php

use BS\ModularFramework\Admin\AdminMenu;
use BS\ModularFramework\Domain\Module;

defined( 'ABSPATH' ) || exit;

/** @var Module|null $module */

$is_edit = $module instanceof Module;

$action_url = add_query_arg(
	array(
		'page' => AdminMenu::MENU_SLUG_MODULES,
	),
	admin_url( 'admin.php' )
);

$name        = $is_edit ? $module->name : '';
$slug        = $is_edit ? $module->slug : '';
$description = $is_edit ? $module->description : '';
$status      = $is_edit ? $module->status : 'active';
$sort_order  = $is_edit ? $module->sort_order : 0;
?>
<div class="wrap">
	<h1>
		<?php echo esc_html( $is_edit ? __( 'Modul bearbeiten', 'bs-modular-framework' ) : __( 'Neues Modul anlegen', 'bs-modular-framework' ) ); ?>
	</h1>

	<form method="post" action="<?php echo esc_url( $action_url ); ?>">
		<?php wp_nonce_field( 'bs_mf_save_module' ); ?>
		<input type="hidden" name="bs_mf_module_action" value="save" />
		<?php if ( $is_edit ) : ?>
			<input type="hidden" name="id" value="<?php echo esc_attr( (string) $module->id ); ?>" />
		<?php endif; ?>

		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">
						<label for="bs_mf_name"><?php esc_html_e( 'Name', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<input name="name" type="text" id="bs_mf_name" value="<?php echo esc_attr( $name ); ?>" class="regular-text" required />
						<p class="description">
							<?php esc_html_e( 'Anzeigename des Moduls im Backend.', 'bs-modular-framework' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="bs_mf_slug"><?php esc_html_e( 'Slug', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<input name="slug" type="text" id="bs_mf_slug" value="<?php echo esc_attr( $slug ); ?>" class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'Eindeutiger Schlüssel (wird automatisch aus dem Namen erzeugt, falls leer).', 'bs-modular-framework' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="bs_mf_description"><?php esc_html_e( 'Beschreibung', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<textarea name="description" id="bs_mf_description" rows="4" class="large-text"><?php echo esc_textarea( (string) $description ); ?></textarea>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="bs_mf_status"><?php esc_html_e( 'Status', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<select name="status" id="bs_mf_status">
							<option value="active" <?php selected( $status, 'active' ); ?>>
								<?php esc_html_e( 'Aktiv', 'bs-modular-framework' ); ?>
							</option>
							<option value="inactive" <?php selected( $status, 'inactive' ); ?>>
								<?php esc_html_e( 'Inaktiv', 'bs-modular-framework' ); ?>
							</option>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="bs_mf_sort_order"><?php esc_html_e( 'Sortierung', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<input name="sort_order" type="number" id="bs_mf_sort_order" value="<?php echo esc_attr( (string) $sort_order ); ?>" class="small-text" />
					</td>
				</tr>
			</tbody>
		</table>

		<?php submit_button( $is_edit ? __( 'Modul aktualisieren', 'bs-modular-framework' ) : __( 'Modul anlegen', 'bs-modular-framework' ) ); ?>
	</form>
</div>

