<?php
/**
 * TransactionEntity tests.
 */

namespace IseardMedia\Kudos\Tests\Domain\Entity;

use IseardMedia\Kudos\Domain\Entity\TransactionEntity;
use IseardMedia\Kudos\Tests\BaseTestCase;

/**
 * @covers \IseardMedia\Kudos\Domain\Entity\TransactionEntity
 */
class TransactionEntityTest extends BaseTestCase {

	public function test_description_uses_package_title_snapshot(): void {
		$transaction = new TransactionEntity( [ 'title' => 'Kippenmateriaal voor 1 gezin' ] );
		$this->assertSame( 'Kippenmateriaal voor 1 gezin', $transaction->get_description() );
	}

	public function test_description_falls_back_when_no_package(): void {
		$transaction = new TransactionEntity( [] );
		$this->assertSame( 'Donation', $transaction->get_description() );
	}
}
