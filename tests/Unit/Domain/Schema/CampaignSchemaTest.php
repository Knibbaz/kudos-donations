<?php
/**
 * CampaignSchema tests.
 */

namespace IseardMedia\Kudos\Tests\Domain\Schema;

use IseardMedia\Kudos\Domain\Schema\CampaignSchema;
use IseardMedia\Kudos\Tests\BaseTestCase;

/**
 * @covers \IseardMedia\Kudos\Domain\Schema\CampaignSchema
 */
class CampaignSchemaTest extends BaseTestCase {

	private CampaignSchema $schema;

	public function set_up(): void {
		parent::set_up();
		$this->schema = $this->get_from_container( CampaignSchema::class );
	}

	public function test_sanitize_packages_accepts_array(): void {
		$result = $this->schema->sanitize_packages(
			[
				[
					'id'          => 'pkg-kippen',
					'title'       => 'Kippenmateriaal voor 1 gezin',
					'description' => 'Zelfvoorzienend materiaal.',
					'amount'      => 15.0,
				],
			]
		);

		$this->assertIsString( $result );
		$this->assertEquals(
			[
				[
					'id'          => 'pkg-kippen',
					'title'       => 'Kippenmateriaal voor 1 gezin',
					'description' => 'Zelfvoorzienend materiaal.',
					'amount'      => 15.0,
				],
			],
			json_decode( $result, true )
		);
	}

	public function test_sanitize_packages_accepts_json_string(): void {
		$json   = '[{"id":"pkg-water","title":"Waterleiding","description":"","amount":50}]';
		$result = $this->schema->sanitize_packages( $json );

		$this->assertSame( $json, $result );
	}

	public function test_sanitize_packages_drops_invalid_entries(): void {
		$result = $this->schema->sanitize_packages(
			[
				[
					'id'          => 'pkg-valid',
					'title'       => 'Valid',
					'description' => '',
					'amount'      => 10,
				],
				[
					'id'          => '',
					'title'       => 'Missing id',
					'description' => '',
					'amount'      => 10,
				],
				[
					'id'          => 'pkg-empty-title',
					'title'       => '   ',
					'description' => '',
					'amount'      => 10,
				],
				[
					'id'          => 'pkg-zero-amount',
					'title'       => 'Zero amount',
					'description' => '',
					'amount'      => 0,
				],
				'not-an-array',
			]
		);

		$this->assertEquals(
			[
				[
					'id'          => 'pkg-valid',
					'title'       => 'Valid',
					'description' => '',
					'amount'      => 10.0,
				],
			],
			json_decode( $result, true )
		);
	}

	public function test_sanitize_packages_returns_null_for_invalid_json(): void {
		$this->assertNull( $this->schema->sanitize_packages( 'not json{' ) );
	}

	public function test_sanitize_packages_returns_null_for_non_array_value(): void {
		$this->assertNull( $this->schema->sanitize_packages( 42 ) );
	}

	public function test_sanitize_packages_empty_list_encodes_to_empty_array(): void {
		$this->assertSame( '[]', $this->schema->sanitize_packages( [] ) );
	}

	public function test_packages_casts_from_json_string_to_array(): void {
		$json   = '[{"id":"pkg-a","title":"A","description":"","amount":5}]';
		$result = $this->schema->cast_types( [ 'packages' => $json ] );

		$this->assertSame(
			[
				[
					'id'          => 'pkg-a',
					'title'       => 'A',
					'description' => '',
					'amount'      => 5,
				],
			],
			$result['packages']
		);
	}
}