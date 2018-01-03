<?php
namespace WebStream\Annotation\Test\Providers;

use WebStream\Annotation\Test\Fixtures\CsrfProtectionFixture1;

/**
 * CsrfProtectionAnnotationProvider
 * @author Ryuichi TANAKA.
 * @since 2017/01/11
 * @version 0.7
 */
trait CsrfProtectionAnnotationProvider
{
    public function okProvider()
    {
        return [
            [CsrfProtectionFixture1::class, 'POST', ['__CSRF_TOKEN__' => 'abcde'], []],
            [CsrfProtectionFixture1::class, 'POST', [], ['X-CSRF-Token' => 'abcde']],
            [CsrfProtectionFixture1::class, 'GET', [], []]
        ];
    }

    public function ngProvider()
    {
        return [
            [CsrfProtectionFixture1::class, 'POST', ['__CSRF_TOKEN__' => 'fghij'], []],
            [CsrfProtectionFixture1::class, 'POST', [], ['X-CSRF-Token' => 'fghij']],
            [CsrfProtectionFixture1::class, 'POST', [], []]
        ];
    }
}
