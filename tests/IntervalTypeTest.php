<?php

/*
 *  Copyright 2022 Michael Lucas
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at0
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Nasumilu\DBAL\Types\Tests;

use DateInterval;

/**
 * UnitTest working with directly with a Connection
 */
class IntervalTypeTest extends AbstractDbTest
{

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        //create a table to insert
        $sql = 'CREATE TABLE IF NOT EXISTS interval_type_test ( id int PRIMARY KEY NOT NULL, value interval);';
        self::$connection->executeQuery($sql);
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass(): void
    {
        $sql = 'DROP TABLE IF EXISTS interval_type_test;';
        self::$connection->executeQuery($sql);
        parent::tearDownAfterClass();
    }

    /**
     * Unit test inserting an interval from PHP \DateInterval object
     * 
     * @test
     * @return int The primary key (id) of the inserted value
     */
    public function insert(): int
    {
        $id = 1;
        $stmt = self::$connection->prepare('INSERT INTO interval_type_test (id, value) VALUES (:id, :value)');
        $value = new DateInterval('P2Y6MT5H');
        $stmt->bindValue('id', $id, 'integer');
        $stmt->bindValue('value', $value, 'interval');
        $this->assertEquals(1, $stmt->executeQuery()->rowCount());
        return $id;
    }

    /**
     * Unit test updating the previous inserted value
     *  
     * @test
     * @depends insert
     * @param int $id       The primary key (id) of the record (row) to update
     * @return DateInterval The expected updated value
     */
    public function update(int $id): array
    {
        $stmt = self::$connection->prepare('UPDATE interval_type_test SET value = :value WHERE id = :id');
        $value = new DateInterval('P1YT55S');
        $stmt->bindValue('value', $value, 'interval');
        $stmt->bindValue('id', $id);
        $this->assertEquals(1, $stmt->executeQuery()->rowCount());
        return ['id' => $id, 'expected' => $value];
    }

    /**
     * Unit test selecting the previous updated record (row) with an expected 
     * value.
     * 
     * @test
     * @depends update
     * @param array<mixed> An array with the expected value and id used to retrieve 
     *                     the previously updated value from the database
     * @return void
     */
    public function select(array $value): void
    {
        $stmt = self::$connection->prepare('SELECT value FROM interval_type_test WHERE id = :id');
        $stmt->bindValue('id', $value['id'], 'integer');
        $results = $stmt->executeQuery();

        $expected = $value['expected']->format('%y year %H:%I:%S');
        $this->assertEquals($expected, $results->fetchOne());
    }

    /**
     * Unit test that null values are inserted and selected correctly
     * 
     * @test
     * @return void
     */
    public function null(): void
    {
        $this->assertEquals(1, self::$connection->insert('interval_type_test',
                        ['id' => '2', 'value' => null],
                        ['integer', 'interval']));
        $stmt = self::$connection->prepare('SELECT value FROM interval_type_test WHERE id = :id');
        $stmt->bindValue('id', 2);
        $this->assertNull($stmt->executeQuery()->fetchOne());
    }

    /**
     * Unit test that ISO 8601 string values are inserted and selected correctly
     * 
     * @test
     * @return void
     */
    public function iso8601(): void
    {
        $expected = (new DateInterval('P100Y01DT01S'))->format('%y years %d day');
        $this->assertEquals(1, self::$connection->insert('interval_type_test',
                        ['id' => '3', 'value' => $expected],
                        ['integer', 'interval']));
        $stmt = self::$connection->prepare('SELECT value FROM interval_type_test WHERE id = :id');
        $stmt->bindValue('id', 3);
        $this->assertEquals($expected, $stmt->executeQuery()->fetchOne());
    }

}
