# nasumilu/dbal-types

A package of custom Doctrin mapping types. 


## Install

```sh
$ composer require nasumilu/dbal-types
```

## Usage

### Add the custom type

#### Standalone Application 

```php

use Nasumilu\DBAL\Types\IntervalType;
use Doctrine\DBAL\Types\Type;

if (!Type::hasType('interval')) {
    Type::addType('interval', IntervalType::class);
}
```

#### Symfony 5|6 Application

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            interval:  Nasumilu\DBAL\Types\IntervalType
```

#### Entity Class Example
```php
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
    
    // getter; setter;

}
```

### More

[Custom Types & Supported Platforms](docs/types.md)