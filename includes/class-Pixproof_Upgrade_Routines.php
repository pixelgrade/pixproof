<?php

/**
 * Class Pixproof_Upgrade_Routines
 *
 * This class takes care of loading migration files from the specified migrations directory.
 *
 * Migration files should only use default WP functions and NOT use code which might not be there in the future.
 * Using such code would defeat the purpose.
 *
 * @ignore
 */
class Pixproof_Upgrade_Routines {

    /**
     * @var string
     */
    protected $version_from = '0.0.1';

    /**
     * @var string
     */
    protected $version_to = '0.0.1';

    /**
     * @var string
     */
    protected $migrations_dir = '';

    /**
     * @param string $from Version string to upgrade from.
     * @param string $to Version string to upgrade to.
     * @param string $migrations_dir Absolute path to migrations directory holding the migration files.
     */
    public function __construct( $from, $to, $migrations_dir ) {
        $this->version_from = $from;
        $this->version_to = $to;
        $this->migrations_dir = $migrations_dir;
    }

    /**
     * Run the various upgrade routines, all the way up to the latest version
     */
    public function run() {
        $migrations = $this->find_migrations();

        // Run in sub-function for the scope to be in this class.
        array_map( array( $this, 'run_migration' ), $migrations );
    }

    /**
     * Return all migrations files absolute paths that should be run.
     *
     * @return array
     */
    public function find_migrations() {
        $files = glob(rtrim( $this->migrations_dir, '/' ) . '/*.php');
        $migrations =  array();

        // Return empty array when glob returns non-array value.
        if ( ! is_array( $files ) ) {
            return $migrations;
        }

        foreach ( $files as $file ) {
	        // The first part of the file is aways the version the migration file applies to.
	        // The rest of the file name is not important apart from easily identify what that migration logic is about.
            $migration = basename($file);
            $parts = explode('-', $migration);
            $version = $parts[0];

            // The migration file version must be greater than the "from version", and smaller or equal than the "to version".
            if ( version_compare( $this->version_from, $version, '<' ) && version_compare( $version, $this->version_to, '<=' ) ) {
                $migrations[] = $file;
            }
        }

        return $migrations;
    }

    /**
     * Include a migration file and run it.
     *
     * @param string $file The absolute path to the migration file.
     */
    protected function run_migration( $file ) {
        include $file;
    }
}
