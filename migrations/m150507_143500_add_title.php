<?php

use yii\db\Migration;
use yii\db\Schema;

class m150507_143500_add_title extends Migration
{
    public function up()
    {
        $this->addColumn('attach_file', 'additional_info', Schema::TYPE_TEXT . ' NULL');
    }

    public function down()
    {
        $this->dropColumn('attach_file', 'additional_info');
    }
}
