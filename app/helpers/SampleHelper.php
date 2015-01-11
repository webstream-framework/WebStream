<?php
namespace WebStream\Sample;

use WebStream\Core\CoreHelper;
use WebStream\Module\Security;

class SampleHelper extends CoreHelper
{
    public function showData($data)
    {
        $html = "";
        foreach ($data as $value) {
            $html .= "<h2>" . Security::safetyOut($value["title"]) . "</h2>";
            $html .= "<p>" . Security::safetyOut($value["description"]) . "</p>";
        }

        return $html;
    }
}
