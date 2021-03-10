<?php

namespace Web2PrintToolsBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Config;
use Pimcore\Db;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\Tool\SettingsStore;
use Web2PrintToolsBundle\FavoriteOutputDefinition\Dao;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20210305134111 extends AbstractPimcoreMigration
{
    public function doesSqlMigrations(): bool
    {
        return false;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $result = null;
        try{
            if(Config::getSystemConfig()) {
                $result = \Pimcore\Db::get()->fetchAll("SHOW TABLES LIKE '" . Dao::TABLE_NAME . "';");
            }
        } catch(\Exception $e){}
        $installed = !empty($result);

        SettingsStore::set('BUNDLE_INSTALLED__Web2PrintToolsBundle\\Web2PrintToolsBundle', $installed, 'bool', 'pimcore');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
