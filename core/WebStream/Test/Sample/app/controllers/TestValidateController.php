<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Validate;
use WebStream\Annotation\ExceptionHandler;

class TestValidateController extends CoreController
{
    /**
     * @Inject
     * @Validate(key="test", rule="required", method="get")
     */
    public function getRequired()
    {
        echo $this->request->get("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="required", method="post")
     */
    public function postRequired()
    {
        echo $this->request->post("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="required", method="put")
     */
    public function putRequired()
    {
        echo $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="required")
     */
    public function allRequired()
    {
        echo $this->request->get("test") ?: $this->request->post("test") ?: $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="equal[honoka]", method="get")
     */
    public function getEqual()
    {
        echo $this->request->get("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="equal[honoka]", method="post")
     */
    public function postEqual()
    {
        echo $this->request->post("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="equal[honoka]", method="put")
     */
    public function putEqual()
    {
        echo $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="equal[honoka]")
     */
    public function allEqual()
    {
        echo $this->request->get("test") ?: $this->request->post("test") ?: $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="length[6]", method="get")
     */
    public function getLength()
    {
        echo $this->request->get("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="length[6]", method="post")
     */
    public function postLength()
    {
        echo $this->request->post("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="length[6]", method="put")
     */
    public function putLength()
    {
        echo $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="length[6]")
     */
    public function allLength()
    {
        echo $this->request->get("test") ?: $this->request->post("test") ?: $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="max[3]", method="get")
     */
    public function getMax()
    {
        echo $this->request->get("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="max[3]", method="post")
     */
    public function postMax()
    {
        echo $this->request->post("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="max[3]", method="put")
     */
    public function putMax()
    {
        echo $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="max[3]")
     */
    public function allMax()
    {
        echo $this->request->get("test") ?: $this->request->post("test") ?: $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="min[1]", method="get")
     */
    public function getMin()
    {
        echo $this->request->get("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="min[1]", method="post")
     */
    public function postMin()
    {
        echo $this->request->post("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="min[1]", method="put")
     */
    public function putMin()
    {
        echo $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="min[1]")
     */
    public function allMin()
    {
        echo $this->request->get("test") ?: $this->request->post("test") ?: $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="max_length[6]", method="get")
     */
    public function getMaxLength()
    {
        echo $this->request->get("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="max_length[6]", method="post")
     */
    public function postMaxLength()
    {
        echo $this->request->post("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="max_length[6]", method="put")
     */
    public function putMaxLength()
    {
        echo $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="max_length[6]")
     */
    public function allMaxLength()
    {
        echo $this->request->get("test") ?: $this->request->post("test") ?: $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="min_length[6]", method="get")
     */
    public function getMinLength()
    {
        echo $this->request->get("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="min_length[6]", method="post")
     */
    public function postMinLength()
    {
        echo $this->request->post("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="min_length[6]", method="put")
     */
    public function putMinLength()
    {
        echo $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="min_length[6]")
     */
    public function allMinLength()
    {
        echo $this->request->get("test") ?: $this->request->post("test") ?: $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="number", method="get")
     */
    public function getNumber()
    {
        echo $this->request->get("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="number", method="post")
     */
    public function postNumber()
    {
        echo $this->request->post("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="number", method="put")
     */
    public function putNumber()
    {
        echo $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="number")
     */
    public function allNumber()
    {
        echo $this->request->get("test") ?: $this->request->post("test") ?: $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="range[1..10]", method="get")
     */
    public function getRange()
    {
        echo $this->request->get("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="range[1..10]", method="post")
     */
    public function postRange()
    {
        echo $this->request->post("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="range[1..10]", method="put")
     */
    public function putRange()
    {
        echo $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="range[1..10]")
     */
    public function allRange()
    {
        echo $this->request->get("test") ?: $this->request->post("test") ?: $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="regexp[/^\d+$/]", method="get")
     */
    public function getRegexp()
    {
        echo $this->request->get("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="regexp[/^\d+$/]", method="post")
     */
    public function postRegexp()
    {
        echo $this->request->post("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="regexp[/^\d+$/]", method="put")
     */
    public function putRegexp()
    {
        echo $this->request->put("test");
    }

    /**
     * @Inject
     * @Validate(key="test", rule="regexp[/^\d+$/]")
     */
    public function allRegexp()
    {
        echo $this->request->get("test") ?: $this->request->post("test") ?: $this->request->put("test");
    }

    // 異常系

    /**
     * @Inject
     * @Validate(key="test", rule="unknown")
     */
    public function invalidRuleUnknown()
    {
    }

    /**
     * @Inject
     * @Validate(key="test", rule="required[]")
     */
    public function invalidRuleRequired()
    {
    }

    /**
     * @Inject
     * @Validate(key="test", rule="equal[]")
     */
    public function invalidRuleEqual()
    {
    }

    /**
     * @Inject
     * @Validate(key="test", rule="length[-1]")
     */
    public function invalidRuleLength1()
    {
    }

    /**
     * @Inject
     * @Validate(key="test", rule="length[hoge]")
     */
    public function invalidRuleLength2()
    {
    }

    /**
     * @Inject
     * @Validate(key="test", rule="length")
     */
    public function invalidRuleLength3()
    {
    }

    /**
     * @Inject
     * @Validate(key="test", rule="max[hoge]")
     */
    public function invalidRuleMax()
    {
    }

    /**
     * @Inject
     * @Validate(key="test", rule="min[hoge]")
     */
    public function invalidRuleMin()
    {
    }

    /**
     * @Inject
     * @Validate(key="test", rule="max_length[hoge]")
     */
    public function invalidRuleMaxLength()
    {
    }

    /**
     * @Inject
     * @Validate(key="test", rule="min_length[hoge]")
     */
    public function invalidRuleMinLength()
    {
    }

    /**
     * @Inject
     * @Validate(key="test", rule="number[]")
     */
    public function invalidRuleNumber()
    {
    }

    /**
     * @Inject
     * @Validate(key="test", rule="range[hoge..hoge]")
     */
    public function invalidRuleRange1()
    {
    }

    /**
     * @Inject
     * @Validate(key="test", rule="range[1...10]")
     */
    public function invalidRuleRange2()
    {
    }

    /**
     * @Inject
     * @Validate(key="test", rule="regexp[hoge]")
     */
    public function invalidRuleRegexp()
    {
    }

    /**
     * @Inject
     * @Validate(rule="required")
     */
    public function invalidValidateAnnotation1()
    {
        // key属性指定なし
    }

    /**
     * @Inject
     * @Validate(key="test")
     */
    public function invalidValidateAnnotation2()
    {
        // rule属性指定なし
    }

    /**
     * @Inject
     * @ExceptionHandler("\Exception")
     */
    public function error($params)
    {
        echo get_class($params["exception"]);
    }
}
