<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m210925_212418_create_oauth2_tables
 */
class m210925_212418_create_oauth2_tables extends Migration
{
    public function mysql($yes,$no='') {
        return $this->db->driverName === 'mysql' ? $yes : $no;
    }

    public function primaryKeyDefinition($columns) {
        return 'PRIMARY KEY (' . $this->db->getQueryBuilder()->buildColumns($columns) . ')';
    }

    public function foreignKeyDefinition($columns,$refTable,$refColumns,$onDelete = null,$onUpdate = null) {
        $builder = $this->db->getQueryBuilder();
        $sql = ' FOREIGN KEY (' . $builder->buildColumns($columns) . ')'
            . ' REFERENCES ' . $this->db->quoteTableName($refTable)
            . ' (' . $builder->buildColumns($refColumns) . ')';
        if ($onDelete !== null) {
            $sql .= ' ON DELETE ' . $onDelete;
        }
        if ($onUpdate !== null) {
            $sql .= ' ON UPDATE ' . $onUpdate;
        }
        return $sql;
    }

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $now            = $this->mysql('CURRENT_TIMESTAMP',"'now'");
        $on_update_now  = $this->mysql("ON UPDATE $now");

        $transaction = $this->db->beginTransaction();
        try {
            $this->createTable('{{%oauth_clients}}', [
                'client_id' => Schema::TYPE_STRING . '(32) NOT NULL',
                'client_secret' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
                'redirect_uri' => Schema::TYPE_STRING . '(1000) NOT NULL',
                'grant_types' => Schema::TYPE_STRING . '(100) NOT NULL',
                'scope' => Schema::TYPE_STRING . '(2000) DEFAULT NULL',
                'user_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
                $this->primaryKeyDefinition('client_id'),
            ], $tableOptions);

            $this->createTable('{{%oauth_access_tokens}}', [
                'access_token' => Schema::TYPE_STRING . '(255) NOT NULL',
                'client_id' => Schema::TYPE_STRING . '(32) NOT NULL',
                'user_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
                'expires' => Schema::TYPE_TIMESTAMP . " NOT NULL DEFAULT $now $on_update_now",
                'scope' => Schema::TYPE_STRING . '(2000) DEFAULT NULL',
                $this->primaryKeyDefinition('access_token'),
                $this->foreignKeyDefinition('client_id','{{%oauth_clients}}','client_id','CASCADE','CASCADE'),
            ], $tableOptions);

            $this->createTable('{{%oauth_refresh_tokens}}', [
                'refresh_token' => Schema::TYPE_STRING . '(255) NOT NULL',
                'client_id' => Schema::TYPE_STRING . '(32) NOT NULL',
                'user_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
                'expires' => Schema::TYPE_TIMESTAMP . " NOT NULL DEFAULT $now $on_update_now",
                'scope' => Schema::TYPE_STRING . '(2000) DEFAULT NULL',
                $this->primaryKeyDefinition('refresh_token'),
                $this->foreignKeyDefinition('client_id','{{%oauth_clients}}','client_id','CASCADE','CASCADE'),
            ], $tableOptions);

            $this->createTable('{{%oauth_authorization_codes}}', [
                'authorization_code' => Schema::TYPE_STRING . '(255) NOT NULL',
                'client_id' => Schema::TYPE_STRING . '(32) NOT NULL',
                'user_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
                'redirect_uri' => Schema::TYPE_STRING . '(1000) NOT NULL',
                'expires' => Schema::TYPE_TIMESTAMP . " NOT NULL DEFAULT $now $on_update_now",
                'scope' => Schema::TYPE_STRING . '(2000) DEFAULT NULL',
                $this->primaryKeyDefinition('authorization_code'),
                $this->foreignKeyDefinition('client_id','{{%oauth_clients}}','client_id','CASCADE','CASCADE'),
            ], $tableOptions);

            $this->createTable('{{%oauth_scopes}}', [
                'scope' => Schema::TYPE_STRING . '(2000) NOT NULL',
                'is_default' => Schema::TYPE_BOOLEAN . ' NOT NULL',
            ], $tableOptions);

            $this->createTable('{{%oauth_jwt}}', [
                'client_id' => Schema::TYPE_STRING . '(32) NOT NULL',
                'subject' => Schema::TYPE_STRING . '(80) DEFAULT NULL',
                'public_key' => Schema::TYPE_STRING . '(2000) DEFAULT NULL',
                $this->primaryKeyDefinition('client_id'),
            ], $tableOptions);

            $this->createTable('{{%oauth_users}}', [
                'username' => Schema::TYPE_STRING . '(255) NOT NULL',
                'password' => Schema::TYPE_STRING . '(2000) DEFAULT NULL',
                'first_name' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
                'last_name' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
                $this->primaryKeyDefinition('username'),
            ], $tableOptions);

            $this->createTable('{{%oauth_public_keys}}', [
                'client_id' => Schema::TYPE_STRING . '(255) NOT NULL',
                'public_key' => Schema::TYPE_STRING . '(2000) DEFAULT NULL',
                'private_key' => Schema::TYPE_STRING . '(2000) DEFAULT NULL',
                'encryption_algorithm' => Schema::TYPE_STRING . '(100) DEFAULT \'RS256\'',
            ], $tableOptions);

            $this->batchInsert('{{%oauth_clients}}', ['client_id', 'client_secret', 'redirect_uri', 'grant_types'], [
                ['client', '$2y$13$1idZWPL6jRz88zAJt0rDJOBsnVAGbfyFl3U7h0c.lZ/MqGkaoNAPy', '', 'client_credentials authorization_code password implicit refresh_token'],
            ]);

            $transaction->commit();
        } catch (Exception $e) {
            echo 'Exception: ' . $e->getMessage() . '\n';
            $transaction->rollback();

            return false;
        }

        return true;
    }

    public function down()
    {
        $transaction = $this->db->beginTransaction();
        try {
            $this->dropTable('{{%oauth_users}}');
            $this->dropTable('{{%oauth_jwt}}');
            $this->dropTable('{{%oauth_scopes}}');
            $this->dropTable('{{%oauth_authorization_codes}}');
            $this->dropTable('{{%oauth_refresh_tokens}}');
            $this->dropTable('{{%oauth_access_tokens}}');
            $this->dropTable('{{%oauth_public_keys}}');
            $this->dropTable('{{%oauth_clients}}');

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            echo $e->getMessage();
            echo "\n";
            echo get_called_class() . ' cannot be reverted.';
            echo "\n";

            return false;
        }

        return true;
    }
}
