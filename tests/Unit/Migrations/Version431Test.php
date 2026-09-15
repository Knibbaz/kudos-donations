<?php
/**
 * Version431 migration tests.
 */

namespace IseardMedia\Kudos\Tests\Migrations;

use IseardMedia\Kudos\Migrations\Version431;
use IseardMedia\Kudos\Tests\BaseTestCase;

/**
 * @covers \IseardMedia\Kudos\Migrations\Version431
 */
class Version431Test extends BaseTestCase {

	private Version431 $migration;

	public function set_up(): void {
		parent::set_up();
		$this->migration = $this->get_from_container( Version431::class );
	}

	/**
	 * The migration must resolve from the container, otherwise it never runs.
	 */
	public function test_migration_is_registered(): void {
		$this->assertInstanceOf( Version431::class, $this->migration );
		$this->assertSame( '4.3.1', $this->migration->get_version() );
		$this->assertTrue( $this->migration->is_auto() );
	}

	/**
	 * The migration exposes the table preparation job.
	 */
	public function test_migration_has_prepare_tables_job(): void {
		$jobs = $this->migration->get_jobs();

		$this->assertArrayHasKey( 'prepare_tables', $jobs );
	}
}