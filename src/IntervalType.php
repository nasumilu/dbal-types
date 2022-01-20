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

namespace Nasumilu\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use DateInterval;

/**
 * Doctrine type used with PostgreSQL interval data type.
 */
class IntervalType extends Type
{

    /** @var string the name of the datatype */
    public const INTERVAL = 'interval';

    /**
     * {@inheritDoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {

        return 'interval';
    }

    /**
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null !== $value) {

            // ISO 8601 format
            if ('p' === strtolower(substr($value, 0, 1))) {
                return new DateInterval($value);
            } else {
                // Date string format
                return DateInterval::createFromDateString($value);
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        // DateInterval convert to ISO 8601
        if ($value instanceof DateInterval) {
            $value = $value->format('P%yY%mM%dDT%hH%iM%sS');
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::INTERVAL;
    }

    /**
     * {@inheritDoc}
     */
    public function canRequireSQLConversion()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        return "$sqlExpr::character varying";
    }

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return "$sqlExpr::interval";
    }

}
