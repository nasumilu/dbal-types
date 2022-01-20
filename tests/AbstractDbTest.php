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

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Nasumilu\DBAL\Types\IntervalType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\{
    Connection,
    DriverManager
};

/**
 *
 */
abstract class AbstractDbTest extends TestCase
{

    protected static ?Connection $connection = null;
    protected static ?EntityManager $em = null;

    public static function setUpBeforeClass(): void
    {
        if (!Type::hasType('interval')) {
            Type::addType('interval', IntervalType::class);
        }

        if (null === self::$connection || !self::$connection->isConnected()) {
            self::$connection = DriverManager::getConnection(['url' => $_ENV['DATABASE_URL']]);
            $config = Setup::createAttributeMetadataConfiguration([__DIR__ . '/../fixtures'], true);
            self::$em = EntityManager::create(self::$connection, $config);
        }
    }

    public static function tearDownAfterClass(): void
    {

        if (null !== self::$connection && self::$connection->isConnected()) {
            self::$connection->close();
        }

        self::$em = null;
        self::$connection = null;
    }

}
