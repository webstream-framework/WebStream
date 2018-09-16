# DI
[![Build Status](https://travis-ci.org/webstream-framework/DI.svg?branch=master)](https://travis-ci.org/webstream-framework/DI)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webstream-framework/DI/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webstream-framework/DI/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/webstream-framework/DI/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/webstream-framework/DI/?branch=master)

This Library is module for dependency injection.

## Usage
Using `Injector#inject`:
```php

class Sample
{
    use Injector;

    public function getValue()
    {
        return $this->value;
    }
}

$obj = new Sample();
$obj->inject('value', 'test');
echo $obj->getValue(); // test
```
You can even set the undefined property value if using the `Injector#inject` method.

Using `Injector#strictInject`:
```php
class SampleValue {}

class Sample
{
    use Injector;

    /**
     * @var SampleValue
     */
    private $value;

    public function getValue()
    {
        return $this->value;
    }
}

$obj = new Sample();
$obj->strictInject('value', new SampleValue());
echo $obj->getValue(); // Return value is instance of SampleValue class.
```
Thrown `AnnotationException` exception when specified the difference value type.  
However, the primitive values (int, string, bool etc.) can not be strict value inject.

## License
Licensed under the MIT
http://www.opensource.org/licenses/mit-license.php
