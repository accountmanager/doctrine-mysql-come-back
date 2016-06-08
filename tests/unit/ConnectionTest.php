<?php

namespace Facile\DoctrineMySQLComeBack\Doctrine\DBAL;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    protected $connection;

    public function setUp()
    {
        $params = [
            'driverOptions' => [
                'x_reconnect_attempts' => 3
            ]
        ];

        $driver = $this->prophesize('Doctrine\DBAL\Driver')
            ->willImplement('Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Driver\ServerGoneAwayExceptionsAwareInterface');
        $configuration = $this->prophesize('Doctrine\DBAL\Configuration');
        $eventManager = $this->prophesize('Doctrine\Common\EventManager');

        $this->connection = new Connection(
            $params,
            $driver->reveal(),
            $configuration->reveal(),
            $eventManager->reveal()
        );
    }

    public function testContructor()
    {
        $params = [
            'driverOptions' => [
                'x_reconnect_attempts' => 999
            ]
        ];

        $driver = $this->prophesize('Doctrine\DBAL\Driver')
            ->willImplement('Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Driver\ServerGoneAwayExceptionsAwareInterface');
        $configuration = $this->prophesize('Doctrine\DBAL\Configuration');
        $eventManager = $this->prophesize('Doctrine\Common\EventManager');

        $connection = new Connection(
            $params,
            $driver->reveal(),
            $configuration->reveal(),
            $eventManager->reveal()
        );

        static::assertInstanceOf('Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Connection', $connection);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testContructorWithInvalidDriver()
    {
        $params = [
            'driverOptions' => [
                'x_reconnect_attempts' => 999
            ]
        ];

        $driver = $this->prophesize('Doctrine\DBAL\Driver');
        $configuration = $this->prophesize('Doctrine\DBAL\Configuration');
        $eventManager = $this->prophesize('Doctrine\Common\EventManager');

        $connection = new Connection(
            $params,
            $driver->reveal(),
            $configuration->reveal(),
            $eventManager->reveal()
        );

        static::assertInstanceOf('Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Connection', $connection);
    }

    /**
     * @dataProvider isUpdateQueryDataProvider
     *
     * @param string $query
     * @param boolean $expected
     */
    public function testIsUpdateQuery($query, $expected)
    {
        static::assertEquals($expected, $this->connection->isUpdateQuery($query));
    }

    public function isUpdateQueryDataProvider()
    {
        return [
            ['UPDATE ', true],
            ['DELETE ', true],
            ['DELETE ', true],
            ['SELECT ', false],
            ['select ', false],
            ['(select ', false],
            [' (select ', false],
            [' 
            (select ', false],
            [' UPDATE WHERE (SELECT ', true],
            [' UPDATE WHERE 
            (select ', true],
        ];
    }
}
