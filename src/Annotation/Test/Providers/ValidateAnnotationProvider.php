<?php
namespace WebStream\Annotation\Test\Providers;

use WebStream\Annotation\Test\Fixtures\ValidateFixture1;
use WebStream\Annotation\Test\Fixtures\ValidateFixture2;
use WebStream\Annotation\Test\Fixtures\ValidateFixture3;
use WebStream\Annotation\Test\Fixtures\ValidateFixture4;
use WebStream\Annotation\Test\Fixtures\ValidateFixture5;
use WebStream\Annotation\Test\Fixtures\ValidateFixture6;
use WebStream\Annotation\Test\Fixtures\ValidateFixture7;
use WebStream\Annotation\Test\Fixtures\ValidateFixture8;
use WebStream\Annotation\Test\Fixtures\ValidateFixture9;

/**
 * ValidateAnnotationProvider
 * @author Ryuichi TANAKA.
 * @since 2017/01/20
 * @version 0.7
 */
trait ValidateAnnotationProvider
{
    public function okProvider()
    {
        return [
            [ValidateFixture1::class, "required1", "get", ['test' => 'value']],
            [ValidateFixture1::class, "required2", "post", ['test' => 'value']],
            [ValidateFixture1::class, "required3", "put", ['test' => 'value']],
            [ValidateFixture1::class, "required4", "delete", ['test' => 'value']],
            [ValidateFixture2::class, "equal1", "get", ['test' => 'value']],
            [ValidateFixture2::class, "equal2", "post", ['test' => 'value']],
            [ValidateFixture2::class, "equal3", "put", ['test' => 'value']],
            [ValidateFixture2::class, "equal4", "delete", ['test' => 'value']],
            [ValidateFixture3::class, "length1", "get", ['test' => 'value']],
            [ValidateFixture3::class, "length2", "post", ['test' => 'value']],
            [ValidateFixture3::class, "length3", "put", ['test' => 'value']],
            [ValidateFixture3::class, "length4", "delete", ['test' => 'value']],
            [ValidateFixture4::class, "max1", "get", ['test' => 5]],
            [ValidateFixture4::class, "max2", "post", ['test' => 5]],
            [ValidateFixture4::class, "max3", "put", ['test' => 5]],
            [ValidateFixture4::class, "max4", "delete", ['test' => 5]],
            [ValidateFixture5::class, "min1", "get", ['test' => 5]],
            [ValidateFixture5::class, "min2", "post", ['test' => 5]],
            [ValidateFixture5::class, "min3", "put", ['test' => 5]],
            [ValidateFixture5::class, "min4", "delete", ['test' => 5]],
            [ValidateFixture6::class, "maxLength1", "get", ['test' => 'value']],
            [ValidateFixture6::class, "maxLength2", "post", ['test' => 'value']],
            [ValidateFixture6::class, "maxLength3", "put", ['test' => 'value']],
            [ValidateFixture6::class, "maxLength4", "delete", ['test' => 'value']],
            [ValidateFixture7::class, "minLength1", "get", ['test' => 'value']],
            [ValidateFixture7::class, "minLength2", "post", ['test' => 'value']],
            [ValidateFixture7::class, "minLength3", "put", ['test' => 'value']],
            [ValidateFixture7::class, "minLength4", "delete", ['test' => 'value']],
            [ValidateFixture8::class, "number1", "get", ['test' => 5]],
            [ValidateFixture8::class, "number2", "post", ['test' => 5]],
            [ValidateFixture8::class, "number3", "put", ['test' => 5]],
            [ValidateFixture8::class, "number4", "delete", ['test' => 5]],
            [ValidateFixture9::class, "range1", "get", ['test' => 3]],
            [ValidateFixture9::class, "range2", "post", ['test' => 3]],
            [ValidateFixture9::class, "range3", "put", ['test' => 3]],
            [ValidateFixture9::class, "range4", "delete", ['test' => 3]]
        ];
    }

    public function ngProvider()
    {
        return [
            [ValidateFixture1::class, "required1", "get", [], "Validation rule error. Rule is 'required', value is empty"],
            [ValidateFixture1::class, "required1", "post", [], "Validation rule error. Rule is 'required', value is empty"],
            [ValidateFixture1::class, "required1", "put", [], "Validation rule error. Rule is 'required', value is empty"],
            [ValidateFixture1::class, "required1", "delete", [], "Validation rule error. Rule is 'required', value is empty"],
            [ValidateFixture2::class, "equal1", "get", ['test' => 'invalid'], "Validation rule error. Rule is 'equal[value]', value is 'invalid'"],
            [ValidateFixture2::class, "equal2", "post", ['test' => 'invalid'], "Validation rule error. Rule is 'equal[value]', value is 'invalid'"],
            [ValidateFixture2::class, "equal3", "put", ['test' => 'invalid'], "Validation rule error. Rule is 'equal[value]', value is 'invalid'"],
            [ValidateFixture2::class, "equal4", "delete", ['test' => 'invalid'], "Validation rule error. Rule is 'equal[value]', value is 'invalid'"],
            [ValidateFixture3::class, "length1", "get", ['test' => 'invalid'], "Validation rule error. Rule is 'length[5]', value is 'invalid'"],
            [ValidateFixture3::class, "length2", "post", ['test' => 'invalid'], "Validation rule error. Rule is 'length[5]', value is 'invalid'"],
            [ValidateFixture3::class, "length3", "put", ['test' => 'invalid'], "Validation rule error. Rule is 'length[5]', value is 'invalid'"],
            [ValidateFixture3::class, "length4", "delete", ['test' => 'invalid'], "Validation rule error. Rule is 'length[5]', value is 'invalid'"],
            [ValidateFixture4::class, "max1", "get", ['test' => 6], "Validation rule error. Rule is 'max[5]', value is '6'"],
            [ValidateFixture4::class, "max2", "post", ['test' => 6], "Validation rule error. Rule is 'max[5]', value is '6'"],
            [ValidateFixture4::class, "max3", "put", ['test' => 6], "Validation rule error. Rule is 'max[5]', value is '6'"],
            [ValidateFixture4::class, "max4", "delete", ['test' => 6], "Validation rule error. Rule is 'max[5]', value is '6'"],
            [ValidateFixture5::class, "min1", "get", ['test' => 2], "Validation rule error. Rule is 'min[3]', value is '2'"],
            [ValidateFixture5::class, "min2", "post", ['test' => 2], "Validation rule error. Rule is 'min[3]', value is '2'"],
            [ValidateFixture5::class, "min3", "put", ['test' => 2], "Validation rule error. Rule is 'min[3]', value is '2'"],
            [ValidateFixture5::class, "min4", "delete", ['test' => 2], "Validation rule error. Rule is 'min[3]', value is '2'"],
            [ValidateFixture6::class, "maxLength1", "get", ['test' => 'invalid'], "Validation rule error. Rule is 'max_length[5]', value is 'invalid'"],
            [ValidateFixture6::class, "maxLength2", "post", ['test' => 'invalid'], "Validation rule error. Rule is 'max_length[5]', value is 'invalid'"],
            [ValidateFixture6::class, "maxLength3", "put", ['test' => 'invalid'], "Validation rule error. Rule is 'max_length[5]', value is 'invalid'"],
            [ValidateFixture6::class, "maxLength4", "delete", ['test' => 'invalid'], "Validation rule error. Rule is 'max_length[5]', value is 'invalid'"],
            [ValidateFixture7::class, "minLength1", "get", ['test' => 'val'], "Validation rule error. Rule is 'min_length[5]', value is 'val'"],
            [ValidateFixture7::class, "minLength2", "post", ['test' => 'val'], "Validation rule error. Rule is 'min_length[5]', value is 'val'"],
            [ValidateFixture7::class, "minLength3", "put", ['test' => 'val'], "Validation rule error. Rule is 'min_length[5]', value is 'val'"],
            [ValidateFixture7::class, "minLength4", "delete", ['test' => 'val'], "Validation rule error. Rule is 'min_length[5]', value is 'val'"],
            [ValidateFixture8::class, "number1", "get", ['test' => 'a'], "Validation rule error. Rule is 'number', value is 'a'"],
            [ValidateFixture8::class, "number2", "post", ['test' => 'a'], "Validation rule error. Rule is 'number', value is 'a'"],
            [ValidateFixture8::class, "number3", "put", ['test' => 'a'], "Validation rule error. Rule is 'number', value is 'a'"],
            [ValidateFixture8::class, "number4", "delete", ['test' => 'a'], "Validation rule error. Rule is 'number', value is 'a'"],
            [ValidateFixture9::class, "range1", "get", ['test' => 0], "Validation rule error. Rule is 'range[1..5]', value is '0'"],
            [ValidateFixture9::class, "range2", "post", ['test' => 0], "Validation rule error. Rule is 'range[1..5]', value is '0'"],
            [ValidateFixture9::class, "range3", "put", ['test' => 0], "Validation rule error. Rule is 'range[1..5]', value is '0'"],
            [ValidateFixture9::class, "range4", "delete", ['test' => 0], "Validation rule error. Rule is 'range[1..5]', value is '0'"]
        ];
    }
}
