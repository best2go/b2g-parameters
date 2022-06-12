<?php

declare(strict_types=1);

namespace Best2Go\Best2GoParameters\Component;

use Best2GoParameters\src\Component\ParameterCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class DbalDriver extends Driver
{
    private string $scheme;
    private string $config;
    private ?Connection $connection;

    public function __construct(string $scheme, string $config)
    {
        $this->scheme = $scheme;
        $this->config = $config;
    }

    public function setup(LoggerInterface $logger = null): void
    {
        $logger = $logger ?: new NullLogger();
        $log = function ($text, ...$args) use ($logger) {
            $logger->debug(sprintf('[B2gDbalDriver] ' . $text, ...$args));
        };

        $log('Creating database table: "%s"', $this->getTableName());
        $this->createDataBaseTable();
    }

    public function resolve(): ParameterCollection
    {
        try {
            return new ParameterCollection($this->fetchParameters());
        } finally {
            $this->closeConnection();
        }
    }

    protected function fetchParameters(): array
    {
        $select = $this->getConnection()
            ->createQueryBuilder()
            ->select('*')
            ->from($this->getTableName())
            ->addOrderBy('priority', 'asc')
            ->addOrderBy('name', 'asc');

        return array_map(function (array $row) {
            return new DbalParameter(
                $row['name'],
                $row['value'],
                (bool) $row['enabled'] ?? true,
                (bool) $row['override'] ?? false
            );
        }, $select->execute()->fetchAllAssociative());
    }

    protected function closeConnection(): void
    {
        $this->connection->close();
        $this->connection = null;
    }

    protected function getTableName(): string
    {
        return 'b2g_parameters';
    }

    protected function createDataBaseTable(): void
    {
        $sm = $this->getConnection()->getSchemaManager();

        if ($sm->tablesExist([$this->getTableName()])) {
            return;
        }

        $table = new Table($this->getTableName());

        $table->addColumn('id', Types::GUID, ['length' => 16, 'fixed' => true]);
        $table->addColumn('name', Types::STRING, ['notnull' => true]);
        $table->addColumn('value', Types::TEXT, ['notnull' => false]);
        $table->addColumn('enabled', Types::SMALLINT, ['default' => true]);
        $table->addColumn('override', Types::SMALLINT, ['default' => 0]);
        $table->addColumn('priority', Types::INTEGER, ['default' => null, 'notnull' => false]);

        $table->setPrimaryKey(['id']);
        //$table->addUniqueIndex(['name']);
        $table->addUniqueIndex(['priority', 'name', 'id']);

        $sm->createTable($table);
    }

    protected function getConnection(): Connection
    {
        if ($this->connection ?? null) {
            return $this->connection;
        }

        $connectionParameters = $this->resolveConnectionParameters();
        $this->connection = DriverManager::getConnection($connectionParameters);

        return $this->connection;
    }

    private function resolveConnectionParameters(): array
    {
        switch ($this->scheme) {
            case 'dbal+mysql':
                return [
                    'url' => 'mysql://' . $this->config,
                ];
            default:
                throw new InvalidArgumentException('Unknown scheme "' . $this->scheme . '"');
        }
    }
}
