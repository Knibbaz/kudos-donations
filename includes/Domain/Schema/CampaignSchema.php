<?php
/**
 * Campaign Schema.
 *
 * @link https://github.com/mikey242/kudos-donations/
 *
 * @copyright 2026 Iseard Media
 */

declare(strict_types=1);

namespace IseardMedia\Kudos\Domain\Schema;

use IseardMedia\Kudos\Enum\FieldType;

class CampaignSchema extends BaseSchema {

	/**
	 * {@inheritDoc}
	 */
	public function get_additional_column_schema(): array {
		return [
			'wp_post_slug'               => $this->make_schema_field( FieldType::STRING, 'sanitize_title_with_dashes' ),
			'currency'                   => $this->make_schema_field( FieldType::STRING, 'sanitize_text_field' ),
			'goal'                       => $this->make_schema_field( FieldType::FLOAT, [ $this, 'sanitize_float' ] ),
			'show_goal'                  => $this->make_schema_field( FieldType::BOOLEAN, 'rest_sanitize_boolean' ),
			'additional_funds'           => $this->make_schema_field( FieldType::FLOAT, [ $this, 'sanitize_float' ] ),
			'amount_type'                => $this->make_schema_field( FieldType::STRING, 'sanitize_text_field' ),
			'fixed_amounts'              => $this->make_schema_field( FieldType::OBJECT, [ $this, 'rest_sanitize_json_field' ] ),
			'packages'                   => $this->make_schema_field( FieldType::OBJECT, [ $this, 'sanitize_packages' ] ),
			'minimum_donation'           => $this->make_schema_field( FieldType::FLOAT, [ $this, 'sanitize_float' ] ),
			'maximum_donation'           => $this->make_schema_field( FieldType::FLOAT, [ $this, 'sanitize_float' ] ),
			'donation_type'              => $this->make_schema_field( FieldType::STRING, 'sanitize_text_field' ),
			'frequency_options'          => $this->make_schema_field( FieldType::OBJECT, [ $this, 'sanitize_json_object_field' ] ),
			'duration_options'           => $this->make_schema_field( FieldType::OBJECT, [ $this, 'sanitize_json_object_field' ] ),
			'email_enabled'              => $this->make_schema_field( FieldType::BOOLEAN, 'rest_sanitize_boolean' ),
			'email_required'             => $this->make_schema_field( FieldType::BOOLEAN, 'rest_sanitize_boolean' ),
			'name_enabled'               => $this->make_schema_field( FieldType::BOOLEAN, 'rest_sanitize_boolean' ),
			'name_required'              => $this->make_schema_field( FieldType::BOOLEAN, 'rest_sanitize_boolean' ),
			'address_enabled'            => $this->make_schema_field( FieldType::BOOLEAN, 'rest_sanitize_boolean' ),
			'address_required'           => $this->make_schema_field( FieldType::BOOLEAN, 'rest_sanitize_boolean' ),
			'message_enabled'            => $this->make_schema_field( FieldType::BOOLEAN, 'rest_sanitize_boolean' ),
			'message_required'           => $this->make_schema_field( FieldType::BOOLEAN, 'rest_sanitize_boolean' ),
			'theme_color'                => $this->make_schema_field( FieldType::STRING, 'sanitize_hex_color' ),
			'terms_link'                 => $this->make_schema_field( FieldType::STRING, 'esc_url_raw' ),
			'privacy_link'               => $this->make_schema_field( FieldType::STRING, 'esc_url_raw' ),
			'show_return_message'        => $this->make_schema_field( FieldType::BOOLEAN, 'rest_sanitize_boolean' ),
			'use_custom_return_url'      => $this->make_schema_field( FieldType::BOOLEAN, 'rest_sanitize_boolean' ),
			'custom_return_url'          => $this->make_schema_field( FieldType::STRING, 'esc_url_raw' ),
			'payment_description_format' => $this->make_schema_field( FieldType::STRING, 'sanitize_text_field' ),
			'custom_styles'              => $this->make_schema_field( FieldType::STRING, 'sanitize_textarea_field' ),
			'initial_title'              => $this->make_schema_field( FieldType::STRING, 'sanitize_text_field' ),
			'initial_description'        => $this->make_schema_field( FieldType::STRING, 'sanitize_textarea_field' ),
			'subscription_title'         => $this->make_schema_field( FieldType::STRING, 'sanitize_text_field' ),
			'subscription_description'   => $this->make_schema_field( FieldType::STRING, 'sanitize_textarea_field' ),
			'address_title'              => $this->make_schema_field( FieldType::STRING, 'sanitize_text_field' ),
			'address_description'        => $this->make_schema_field( FieldType::STRING, 'sanitize_textarea_field' ),
			'message_title'              => $this->make_schema_field( FieldType::STRING, 'sanitize_text_field' ),
			'message_description'        => $this->make_schema_field( FieldType::STRING, 'sanitize_textarea_field' ),
			'payment_title'              => $this->make_schema_field( FieldType::STRING, 'sanitize_text_field' ),
			'payment_description'        => $this->make_schema_field( FieldType::STRING, 'sanitize_textarea_field' ),
			'return_message_title'       => $this->make_schema_field( FieldType::STRING, 'sanitize_text_field' ),
			'return_message_text'        => $this->make_schema_field( FieldType::STRING, 'sanitize_textarea_field' ),
		];
	}

	/**
	 * Sanitizes a list of donation packages.
	 *
	 * Accepts an array or a JSON-encoded string and returns a JSON-encoded
	 * string suitable for storage. Invalid entries are dropped.
	 *
	 * @param mixed $value The raw packages value.
	 * @return false|string|null The encoded packages, or null when invalid.
	 */
	public function sanitize_packages( $value ) {
		if ( \is_string( $value ) ) {
			if ( ! $this->is_valid_json( $value ) ) {
				return null;
			}
			$decoded = json_decode( $value, true );
			if ( ! \is_array( $decoded ) ) {
				return null;
			}
		} elseif ( \is_array( $value ) || \is_object( $value ) ) {
			$decoded = (array) $value;
		} else {
			return null;
		}

		$packages = [];
		foreach ( $decoded as $item ) {
			if ( ! \is_array( $item ) ) {
				continue;
			}
			$package = $this->sanitize_package( $item );
			if ( null !== $package ) {
				$packages[] = $package;
			}
		}

		return wp_json_encode( $packages );
	}

	/**
	 * Sanitizes a single package entry.
	 *
	 * @param array $item The raw package data.
	 * @return array{id: string, title: string, description: string, amount: float}|null
	 *         The sanitized package, or null when invalid.
	 */
	private function sanitize_package( array $item ): ?array {
		$id          = isset( $item['id'] ) ? sanitize_text_field( (string) $item['id'] ) : '';
		$title       = isset( $item['title'] ) ? sanitize_text_field( (string) $item['title'] ) : '';
		$description = isset( $item['description'] ) ? sanitize_textarea_field( (string) $item['description'] ) : '';
		$amount      = isset( $item['amount'] ) ? self::sanitize_float( $item['amount'] ) : null;

		if ( '' === $id || '' === $title || null === $amount || $amount <= 0 ) {
			return null;
		}

		return [
			'id'          => $id,
			'title'       => $title,
			'description' => $description,
			'amount'      => $amount,
		];
	}
}
