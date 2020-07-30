<?php

use yii\db\Query;
use humhub\components\Migration;

class m200730_005521_pageurl extends Migration
{

    public function up()
    {
        if (!class_exists('URLify')) {
            throw new Exception('URLify class not found - please run composer update!');
        }

        $this->addColumn('page', 'url', $this->string(45));
        $this->createIndex('url-unique', 'page', 'url', true);

        $rows = (new Query())
                ->select("*")
                ->from('page')
                ->all();
        foreach ($rows as $row) {
            $url = \humhub\modules\page\components\UrlValidator::autogenerateUniquePageUrl($row['name']);
            $this->updateSilent('page', ['url' => $url], ['id' => $row['id']]);
        }
    }

    public function down()
    {
        echo "m160509_214811_pageurl cannot be reverted.\n";

        return false;
    }

    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
