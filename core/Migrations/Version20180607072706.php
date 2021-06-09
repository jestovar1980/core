<?php
namespace OC\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use OCP\Migration\ISchemaMigration;

/**
 * Add new table for persistent locks
 */
class Version20180607072706 implements ISchemaMigration {
	public function changeSchema(Schema $schema, array $options) {
		$prefix = $options['tablePrefix'];
		$table = $schema->createTable("{$prefix}persistent_locks");
		$table->addColumn('id', Types::BIGINT, [
			'autoincrement' => true,
			'unsigned' => true,
			'notnull' => true,
		]);
		$table->addColumn('file_id', Types::BIGINT, [
			'notnull' => true,
			'length' => 20,
			'comment' => 'FK to fileid in table oc_file_cache'
		]);
		$table->addColumn('owner', Types::STRING, [
			'notnull' => false,
			'length' => 100,
			'comment' => 'owner of the lock - just a human readable string'
		]);
		$table->addColumn('timeout', Types::INTEGER, [
			'notnull' => true,
			'unsigned' => true,
			'comment' => 'timestamp when the lock expires'
		]);
		$table->addColumn('created_at', Types::INTEGER, [
			'notnull' => true,
			'unsigned' => true,
			'comment' => 'timestamp when the lock was created'
		]);
		$table->addColumn('token', Types::STRING, [
			'notnull' => true,
			'length' => 1024,
			'comment' => 'uuid for webdav locks - 1024 random chars for WOPI locks'
		]);
		$table->addColumn('token_hash', Types::STRING, [
			'notnull' => true,
			'length' => 32,
			'comment' => 'md5(token)'
		]);
		// mysql specific
		$table->addColumn('scope', Types::SMALLINT, [
			'notnull' => true,
			'comment' => '1 - exclusive, 2 - shared'
		]);
		$table->addColumn('depth', Types::SMALLINT, [
			'notnull' => true,
			'comment' => '0, 1 or infinite'
		]);
		$table->addColumn('owner_account_id', Types::BIGINT, [
			'notnull' => false,
			'unsigned' => true,
			'length' => 20,
			'comment' => 'owner of the lock - FK to account table'
		]);

		$table->setPrimaryKey(['id']);
		$table->addUniqueIndex(['token_hash']);
		$table->addForeignKeyConstraint(
			"{$prefix}filecache",
			['file_id'],
			['fileid'],
			['onDelete' => 'CASCADE']
		);
		$table->addForeignKeyConstraint(
			"{$prefix}accounts",
			['owner_account_id'],
			['id'],
			['onDelete' => 'CASCADE']
		);
	}
}
