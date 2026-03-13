<?php

use BS\ModularFramework\Domain\FieldDefinition;
use BS\ModularFramework\Domain\Module;

defined( 'ABSPATH' ) || exit;

/** @var Module $current_module */
/** @var FieldDefinition|null $field */
/** @var array<string,string> $supported_types */

$is_edit = $field instanceof FieldDefinition;

$label       = $is_edit ? $field->label : '';
$field_key   = $is_edit ? $field->field_key : '';
$field_type  = $is_edit ? $field->field_type : 'text';
$is_required = $is_edit ? $field->is_required : false;
$sort_order  = $is_edit ? $field->sort_order : 0;

$select_options_raw = '';
if ( $is_edit && 'select' === $field_type && is_array( $field->config ) && ! empty( $field->config['options'] ) ) {
	$select_options_raw = implode( "\n", array_map( 'strval', (array) $field->config['options'] ) );
}

$action_url = add_query_arg(
	array(
		'page'      => 'bs-modular-framework-fields',
		'module_id' => $current_module->id,
	),
	admin_url( 'admin.php' )
);
?>
<div class="wrap">
	<h1>
		<?php
		echo esc_html(
			$is_edit
				? sprintf(
					/* translators: %s: Modulname. */
					__( 'Feld für Modul „%s“ bearbeiten', 'bs-modular-framework' ),
					$current_module->name
				)
				: sprintf(
					/* translators: %s: Modulname. */
					__( 'Neues Feld für Modul „%s“ anlegen', 'bs-modular-framework' ),
					$current_module->name
				)
		);
		?>
	</h1>

	<p class="description">
		<?php
		printf(
			/* translators: %s: Modulname. */
			esc_html__( 'Definiere hier ein Feld für das Modul „%s“. Der Feld-Key wird später verwendet, um den Wert programmatisch abzurufen.', 'bs-modular-framework' ),
			$current_module->name
		);
		?>
	</p>

	<form method="post" action="<?php echo esc_url( $action_url ); ?>">
		<?php wp_nonce_field( 'bs_mf_save_field' ); ?>
		<input type="hidden" name="bs_mf_field_action" value="save" />
		<?php if ( $is_edit ) : ?>
			<input type="hidden" name="id" value="<?php echo esc_attr( (string) $field->id ); ?>" />
		<?php endif; ?>

		<p class="description">
			<?php esc_html_e( 'Mit * markierte Felder sind Pflichtfelder.', 'bs-modular-framework' ); ?>
		</p>

		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">
						<label for="bs_mf_field_label"><?php esc_html_e( 'Label *', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<input name="label" type="text" id="bs_mf_field_label" value="<?php echo esc_attr( $label ); ?>" class="regular-text" required />
						<p class="description">
							<?php esc_html_e( 'Anzeigename des Feldes im Formular.', 'bs-modular-framework' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="bs_mf_field_key"><?php esc_html_e( 'Key *', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<input name="field_key" type="text" id="bs_mf_field_key" value="<?php echo esc_attr( $field_key ); ?>" class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'Interner Schlüssel (wird automatisch aus dem Label erzeugt, falls leer). Muss innerhalb eines Moduls eindeutig sein.', 'bs-modular-framework' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="bs_mf_field_type"><?php esc_html_e( 'Feldtyp', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<select name="field_type" id="bs_mf_field_type">
							<?php foreach ( $supported_types as $type_key => $type_label ) : ?>
								<option value="<?php echo esc_attr( $type_key ); ?>" <?php selected( $field_type, $type_key ); ?>>
									<?php echo esc_html( $type_label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="bs_mf_field_required"><?php esc_html_e( 'Pflichtfeld', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<label>
							<input name="is_required" type="checkbox" id="bs_mf_field_required" value="1" <?php checked( $is_required ); ?> />
							<?php esc_html_e( 'Dieses Feld ist erforderlich.', 'bs-modular-framework' ); ?>
						</label>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="bs_mf_field_sort_order"><?php esc_html_e( 'Sortierung', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<input name="sort_order" type="number" id="bs_mf_field_sort_order" value="<?php echo esc_attr( (string) $sort_order ); ?>" class="small-text" />
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="bs_mf_select_options"><?php esc_html_e( 'Select-Optionen', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<textarea name="select_options" id="bs_mf_select_options" rows="4" class="large-text"><?php echo esc_textarea( $select_options_raw ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Eine Option pro Zeile. Nur relevant für Feldtyp „Select“.', 'bs-modular-framework' ); ?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>

		<?php submit_button( $is_edit ? __( 'Feld aktualisieren', 'bs-modular-framework' ) : __( 'Feld anlegen', 'bs-modular-framework' ) ); ?>
	</form>
</div>

