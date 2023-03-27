<?php

namespace app\models\extended;

use app\models\Board;

class BoardWithSections extends Board
{
    public function fields()
    {
        $fields = parent::fields();

        $fields['sections'] = function () {
            return $this->sections;
        };

        return $fields;
    }
}
