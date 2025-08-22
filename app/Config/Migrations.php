<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Migrations extends BaseConfig
{
    /**
<<<<<<< HEAD
     * --------------------------------------------------------------------------
     * Enable/Disable Migrations
     * --------------------------------------------------------------------------
     *
     * Migrations are enabled by default.
     *
     * You should enable migrations whenever you intend to do a schema migration
     * and disable it back when you're done.
=======
     * Enable/Disable Migrations
>>>>>>> 96b2349 (Initial commit)
     */
    public bool $enabled = true;

    /**
<<<<<<< HEAD
     * --------------------------------------------------------------------------
     * Migrations Table
     * --------------------------------------------------------------------------
     *
     * This is the name of the table that will store the current migrations state.
     * When migrations runs it will store in a database table which migration
     * files have already been run.
=======
     * Filesystem path where migrations are located.
     */
    public string $path = APPPATH . 'Database/Migrations/';

    /**
     * Migration table name
>>>>>>> 96b2349 (Initial commit)
     */
    public string $table = 'migrations';

    /**
<<<<<<< HEAD
     * --------------------------------------------------------------------------
     * Timestamp Format
     * --------------------------------------------------------------------------
     *
     * This is the format that will be used when creating new migrations
     * using the CLI command:
     *   > php spark make:migration
     *
     * NOTE: if you set an unsupported format, migration runner will not find
     *       your migration files.
     *
     * Supported formats:
     * - YmdHis_
     * - Y-m-d-His_
     * - Y_m_d_His_
=======
     * Migration type
     */
    public string $type = 'timestamp';

    /**
     * The timestamp format for migration files.
>>>>>>> 96b2349 (Initial commit)
     */
    public string $timestampFormat = 'Y-m-d-His_';
}
