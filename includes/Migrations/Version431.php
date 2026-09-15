<?php
/**
 * Migration to add donation package support.
 *
 * Adds the `packages` column to the campaigns table and the `package_id`
 * column to the transactions table via dbDelta.
 *
 * @link https://github.com/mikey242/kudos-donations/
 *
 * @copyright 2026 Iseard Media
 */

declare(strict_types=1);

namespace IseardMedia\Kudos\Migrations;

use IseardMedia\Kudos\Domain\Table\BaseTable;
use IseardMedia\Kudos\Domain\Table\CampaignsTable;
use IseardMedia\Kudos\Domain\Table\DonorsTable;
use IseardMedia\Kudos\Domain\Table\SubscriptionsTable;
use IseardMedia\Kudos\Domain\Table\TransactionsTable;

class Version431 extends BaseMigration {

	protected string $version = '4.3.1';

	/**
	 * @var BaseTable[]
	 */
	private array $tables;

	/**
	 * Add the tables for creating/altering schema.
	 *
	 * @param CampaignsTable     $campaigns_table The campaigns table class.
	 * @param DonorsTable        $donors_table The donors table class.
	 * @param TransactionsTable  $transactions_table The transactions table class.
	 * @param SubscriptionsTable $subscriptions_table The subscriptions table class.
	 */
	public function __construct( CampaignsTable $campaigns_table, DonorsTable $donors_table, TransactionsTable $transactions_table, SubscriptionsTable $subscriptions_table ) {
		$this->tables = [
			$campaigns_table,
			$donors_table,
			$transactions_table,
			$subscriptions_table,
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_auto(): bool {
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_jobs(): array {
		return [
			'prepare_tables' => $this->job( [ $this, 'prepare_tables' ], 'Preparing tables', false ),
		];
	}

	/**
	 * Ensures the tables have the columns added in 4.3.1.
	 * dbDelta adds the missing `packages` and `package_id` columns.
	 */
	public function prepare_tables(): void {
		foreach ( $this->tables as $table ) {
			$table->create_table();
		}
	}
}