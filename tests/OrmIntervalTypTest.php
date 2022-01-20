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
use Nasumilu\DBAL\Types\Tests\Fixture\Subscription;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Description of OrmIntervalTypTest
 *
 * @author mlucas
 */
class OrmIntervalTypeTest extends AbstractDbTest
{

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $tool = new SchemaTool(self::$em);
        $tool->createSchema(self::$em->getMetadataFactory()->getAllMetadata());
    }

    public static function tearDownAfterClass(): void
    {
        $tool = new SchemaTool(self::$em);
        $tool->dropSchema(self::$em->getMetadataFactory()->getAllMetadata());
        parent::tearDownAfterClass();
    }

    /**
     * @test
     * @return void
     */
    public function insert(): int
    {
        $subscription = new Subscription();
        $subscriptions = [$subscription];
        self::$em->persist($subscription);
        foreach (range(0, 9) as $value) {
            $subscription = (new Subscription())
                    ->setPeriod(new DateInterval("P100YT{$value}H"));
            self::$em->persist($subscription);
            $subscriptions[] = $subscription;
        }
        self::$em->flush();
        foreach ($subscriptions as $subscription) {
            $this->assertNotNull($subscription->getId());
        }
        return count($subscriptions);
    }

    /**
     * @test
     * @depends insert
     * @param int $count the number of values inserted into the database
     * @return array<Subscription>
     */
    public function select(int $count): array
    {
        $subscriptions = self::$em->getRepository(Subscription::class)->findAll();
        $this->assertCount($count, $subscriptions);

        foreach ($subscriptions as $subscription) {
            echo "\n" . $subscription->getId() . "; "
            . $subscription->getPeriod()
                    ?->format("%y years %m months %d days %H:%I:%S");
        }
        return $subscriptions;
    }

    /**
     * @test
     * @depends select
     * @param array<Subscription> $subscriptions
     * @return void
     */
    public function update(array $subscriptions): void
    {
        echo "\n Add 10 minutes to each none null peroid.";
        foreach ($subscriptions as $subscription) {
            $period = $subscription->getPeriod();
            if (null !== $period) {
                $period->i = 10;
            }
        }
        self::$em->flush();
        $this->select(count($subscriptions));
    }

}
