<?php

use BS\ModularFramework\Domain\Entry;
use BS\ModularFramework\Domain\FieldDefinition;
use BS\ModularFramework\Domain\Module;

defined( 'ABSPATH' ) || exit;

/** @var Module $current_module */
/** @var Entry|null $entry */
/** @var FieldDefinition[] $fields */
/** @var array<int, mixed> $field_values */

$is_edit = $entry instanceof Entry;

$title      = $is_edit ? $entry->title : '';
$status     = $is_edit ? $entry->status : 'draft';
$sort_order = $is_edit ? $entry->sort_order : 0;

$action_url = add_query_arg(
	array(
		'page'      => 'bs-modular-framework-entries',
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
					__( 'Eintrag für Modul „%s“ bearbeiten', 'bs-modular-framework' ),
					$current_module->name
				)
				: sprintf(
					/* translators: %s: Modulname. */
					__( 'Neuen Eintrag für Modul „%s“ anlegen', 'bs-modular-framework' ),
					$current_module->name
				)
		);
		?>
	</h1>

	<p class="description">
		<?php
		printf(
			/* translators: %s: Modulname. */
			esc_html__( 'Erfasse hier einen Eintrag für das Modul „%s“. Die verfügbaren Felder ergeben sich aus den Felddefinitionen des Moduls.', 'bs-modular-framework' ),
			$current_module->name
		);
		?>
	</p>

	<form method="post" action="<?php echo esc_url( $action_url ); ?>">
		<?php wp_nonce_field( 'bs_mf_save_entry' ); ?>
		<input type="hidden" name="bs_mf_entry_action" value="save" />
		<?php if ( $is_edit ) : ?>
			<input type="hidden" name="id" value="<?php echo esc_attr( (string) $entry->id ); ?>" />
		<?php endif; ?>

		<p class="description">
			<?php esc_html_e( 'Mit * markierte Felder sind Pflichtfelder.', 'bs-modular-framework' ); ?>
		</p>

		<h2 class="title"><?php esc_html_e( 'Allgemeine Angaben', 'bs-modular-framework' ); ?></h2>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">
						<label for="bs_mf_entry_title"><?php esc_html_e( 'Titel *', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<input name="title" type="text" id="bs_mf_entry_title" value="<?php echo esc_attr( $title ); ?>" class="regular-text" required />
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="bs_mf_entry_status"><?php esc_html_e( 'Status', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<select name="status" id="bs_mf_entry_status">
							<option value="draft" <?php selected( $status, 'draft' ); ?>>
								<?php esc_html_e( 'Entwurf', 'bs-modular-framework' ); ?>
							</option>
							<option value="published" <?php selected( $status, 'published' ); ?>>
								<?php esc_html_e( 'Veröffentlicht', 'bs-modular-framework' ); ?>
							</option>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="bs_mf_entry_sort_order"><?php esc_html_e( 'Sortierung', 'bs-modular-framework' ); ?></label>
					</th>
					<td>
						<input name="sort_order" type="number" id="bs_mf_entry_sort_order" value="<?php echo esc_attr( (string) $sort_order ); ?>" class="small-text" />
					</td>
				</tr>
			</tbody>
		</table>

		<h2 class="title"><?php esc_html_e( 'Feldwerte', 'bs-modular-framework' ); ?></h2>
		<table class="form-table" role="presentation">
			<tbody>
				<?php if ( empty( $fields ) ) : ?>
					<tr>
						<td colspan="2">
							<?php esc_html_e( 'Für dieses Modul sind noch keine Felder definiert.', 'bs-modular-framework' ); ?>
						</td>
					</tr>
				<?php else : ?>
					<?php foreach ( $fields as $field ) : ?>
						<?php
						$name      = 'field_' . $field->id;
						$value     = $field_values[ $field->id ] ?? null;
						$is_req    = $field->is_required;
						$label     = $field->label . ( $is_req ? ' *' : '' );
						$fieldtype = $field->field_type;
						?>
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $name ); ?>">
									<?php echo esc_html( $label ); ?>
								</label>
							</th>
							<td>
								<?php if ( 'text' === $fieldtype ) : ?>
									<input name="<?php echo esc_attr( $name ); ?>" type="text" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="regular-text" <?php disabled( false ); ?> />
								<?php elseif ( 'textarea' === $fieldtype ) : ?>
									<textarea name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" rows="4" class="large-text"><?php echo esc_textarea( (string) $value ); ?></textarea>
								<?php elseif ( 'number' === $fieldtype ) : ?>
									<input name="<?php echo esc_attr( $name ); ?>" type="number" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="small-text" />
								<?php elseif ( 'email' === $fieldtype ) : ?>
									<input name="<?php echo esc_attr( $name ); ?>" type="email" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="regular-text" />
								<?php elseif ( 'url' === $fieldtype ) : ?>
									<input name="<?php echo esc_attr( $name ); ?>" type="url" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="regular-text" />
								<?php elseif ( 'date' === $fieldtype ) : ?>
									<input name="<?php echo esc_attr( $name ); ?>" type="date" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" />
								<?php elseif ( 'select' === $fieldtype ) : ?>
									<?php
									$options = array();
									if ( is_array( $field->config ) && ! empty( $field->config['options'] ) ) {
										$options = (array) $field->config['options'];
									}
									?>
									<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>">
										<option value=""><?php esc_html_e( 'Bitte wählen', 'bs-modular-framework' ); ?></option>
										<?php foreach ( $options as $option ) : ?>
											<option value="<?php echo esc_attr( (string) $option ); ?>" <?php selected( (string) $value, (string) $option ); ?>>
												<?php echo esc_html( (string) $option ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								<?php elseif ( 'checkbox' === $fieldtype ) : ?>
									<label>
										<input name="<?php echo esc_attr( $name ); ?>" type="checkbox" id="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( (bool) $value ); ?> />
									</label>
								<?php elseif ( 'image' === $fieldtype ) : ?>
									<?php
									$attachment_id = $value ? (int) $value : 0;
									?>
									<div class="bs-mF-image-field" data-field-name="<?php echo esc_attr( $name ); ?>">
										<input type="hidden" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $attachment_id ? (string) $attachment_id : '' ); ?>" />
										<div class="bs-mF-image-preview">
											<?php
											if ( $attachment_id ) {
												echo wp_get_attachment_image( $attachment_id, 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											}
											?>
										</div>
										<p>
											<button type="button" class="button bs-mF-image-select">
												<?php esc_html_e( 'Bild auswählen', 'bs-modular-framework' ); ?>
											</button>
											<button type="button" class="button bs-mF-image-remove">
												<?php esc_html_e( 'Bild entfernen', 'bs-modular-framework' ); ?>
											</button>
										</p>
									</div>
								<?php else : ?>
									<input name="<?php echo esc_attr( $name ); ?>" type="text" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="regular-text" />
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>

		<?php submit_button( $is_edit ? __( 'Eintrag aktualisieren', 'bs-modular-framework' ) : __( 'Eintrag anlegen', 'bs-modular-framework' ) ); ?>
	</form>
</div>

