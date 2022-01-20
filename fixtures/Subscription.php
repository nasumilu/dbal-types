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

namespace Nasumilu\DBAL\Types\Tests\Fixture;

use Doctrine\ORM\Mapping as ORM;
use DateInterval;

#[
    ORM\Entity,
    ORM\Table(name: 'subscription')
]
class Subscription
{

    #[
        ORM\Id,
        ORM\Column(name: 'id', type: 'integer'),
        ORM\GeneratedValue(strategy: 'IDENTITY')
    ]
    private ?int $id = null;
    
    #[ORM\Column(name: 'period', type: 'interval', nullable: true)]
    private ?DateInterval $period = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPeriod(): ?DateInterval
    {
        return $this->period;
    }

    public function setPeriod(DateInterval $period): self
    {
        $this->period = $period;
        return $this;
    }

}
