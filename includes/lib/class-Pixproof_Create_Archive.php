<?php
/**
 * Create zip Archive
 *
 * Borrowed from the BackWPup plugin @link https://wordpress.org/plugins/backwpup/
 * and modified to suit our needs.
 */

/**
 * Create Archive Exception
 */

/**
 * Pixproof_Create_Archive_Exception
 */
class Pixproof_Create_Archive_Exception extends Exception {

}

/**
 * Class for creating File Archives
 */
class Pixproof_Create_Archive {

	/**
	 * Achieve file with full path
	 *
	 * @var string
	 */
	private $file = '';

	/**
	 * Compression method
	 *
	 * @var string Method off compression Methods are ZipArchive, PclZip.
	 */
	private $method = '';

	/**
	 * File Handel
	 *
	 * Open handel for files.
	 */
	private $filehandler = null;

	/**
	 * ZipArchive
	 *
	 * @var ZipArchive
	 */
	private $ziparchive = null;

	/**
	 * PclZip
	 *
	 * @var PclZip
	 */
	private $pclzip = null;

	/**
	 * PclZip File List
	 *
	 * @var array()
	 */
	private $pclzip_file_list = array();

	/**
	 * File Count
	 *
	 * File cont off added files to handel somethings that depends on it
	 *
	 * @var int number of files added
	 */
	private $file_count = 0;

	/**
	 * Pixproof_Create_Archive constructor
	 *
	 * @param string $file File with full path of the archive.
	 *
	 * @throws Pixproof_Create_Archive_Exception If the file is empty or not a valid string.
	 */
	public function __construct( $file ) {

		if ( ! is_string( $file ) || empty( $file ) ) {
			throw new Pixproof_Create_Archive_Exception(
				__( 'The file name of an archive cannot be empty.', 'pixproof' )
			);
		}

		// Check folder can used.
		if ( ! is_dir( dirname( $file ) ) || ! is_writable( dirname( $file ) ) ) {
			throw new Pixproof_Create_Archive_Exception(
				sprintf(
				/* translators: $1 is the file path */
					esc_html_x( 'Folder %s for archive not found', '%s = Folder name', 'pixproof' ),
					dirname( $file )
				)
			);
		}

		$this->file = trim( $file );

		// .ZIP
		if ( ! $this->filehandler && '.zip' === strtolower( substr( $this->file, - 4 ) ) ) {
			$this->method = 'ZipArchive';

			// Switch to PclZip if ZipArchive isn't supported.
			if ( ! class_exists( 'ZipArchive' ) ) {
				$this->method = 'PclZip';
			}

			if ( 'ZipArchive' === $this->method ) {
				$this->ziparchive = new ZipArchive();
				$ziparchive_open  = $this->ziparchive->open( $this->file, ZipArchive::CREATE );

				if ( $ziparchive_open !== true ) {
					$this->ziparchive_status();

					throw new Pixproof_Create_Archive_Exception(
						sprintf(
						/* translators: $1 is a directory name */
							esc_html_x( 'Cannot create zip archive: %d', 'ZipArchive open() result', 'pixproof' ),
							$ziparchive_open
						)
					);
				}
			}

			if ( 'PclZip' === $this->method ) {
				$this->method = 'PclZip';

				if ( ! defined( 'PCLZIP_TEMPORARY_DIR' ) ) {
					define( 'PCLZIP_TEMPORARY_DIR', get_temp_dir() );
				}

				require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';

				$this->pclzip = new PclZip( $this->file );
			}

			// Must be set to true to prevent issues. Monkey patch.
			$this->filehandler = true;
		}

		if ( '' === $this->method ) {
			throw new Pixproof_Create_Archive_Exception(
				sprintf(
				/* translators: the $1 is the type of the archive file */
					esc_html_x( 'Method to archive file %s not detected', '%s = file name', 'pixproof' ),
					basename( $this->file )
				)
			);
		}

		if ( null === $this->filehandler ) {
			throw new Pixproof_Create_Archive_Exception( __( 'Cannot open archive file', 'pixproof' ) );
		}
	}

	/**
	 * Destruct
	 *
	 * Closes open archive on shutdown.
	 */
	public function __destruct() {

		// Close PclZip.
		if ( is_object( $this->pclzip ) ) {
			if ( count( $this->pclzip_file_list ) > 0 ) {
				if ( 0 == $this->pclzip->add( $this->pclzip_file_list ) ) {
					trigger_error(
						sprintf(
						/* translatores: $1 is the error string */
							esc_html__( 'PclZip archive add error: %s', 'pixproof' ),
							$this->pclzip->errorInfo( true )
						),
						E_USER_ERROR
					);
				}
			}
			unset( $this->pclzip );
		}

		// Close ZipArchive.
		if ( null !== $this->ziparchive ) {
			if ( ! $this->ziparchive->close() ) {
				$this->ziparchive_status();

				sleep( 1 );
			}
			$this->ziparchive = null;
		}

		// Close file if open.
		if ( is_resource( $this->filehandler ) ) {
			$this->fclose();
		}
	}

	/**
	 * Close
	 *
	 * Closing the archive
	 *
	 * @return void
	 */
	public function close() {

		if ( $this->ziparchive instanceof \ZipArchive ) {
			$this->ziparchive->close();
			$this->ziparchive = null;
		}

		if ( ! is_resource( $this->filehandler ) ) {
			return;
		}

		$this->fclose();
	}

	/**
	 * Get Method
	 *
	 * Get method that the archive uses
	 *
	 * @return string The compression method
	 */
	public function get_method() {

		return $this->method;
	}

	/**
	 * Adds a file to Archive
	 *
	 * @param string $file_name       The file name path.
	 * @param string $name_in_archive The name of the file to use within the archive.
	 *
	 * @return bool True on success, false on error.
	 */
	public function add_file( $file_name, $name_in_archive = '' ) {

		$file_name = trim( $file_name );

		if ( ! is_string( $file_name ) || empty( $file_name ) ) {
			trigger_error(
				esc_html__( 'File name cannot be empty.', 'pixproof' ),
				E_USER_WARNING
			);

			return false;
		}

		clearstatcache( true, $file_name );

		if ( ! is_readable( $file_name ) ) {
			trigger_error(
				sprintf(
				/* translators: The $1 is the name of the file to add to the archive. */
					esc_html_x( 'File %s does not exist or is not readable', 'File to add to archive', 'pixproof' ),
					$file_name
				),
				E_USER_WARNING
			);

			return true;
		}

		if ( empty( $name_in_archive ) ) {
			$name_in_archive = $file_name;
		}

		switch ( $this->method ) {
			case 'ZipArchive':
				// Convert chars for archives file names.
				if ( function_exists( 'iconv' ) && stripos( PHP_OS, 'win' ) === 0 ) {
					$test = @iconv( 'UTF-8', 'CP437', $name_in_archive );
					if ( $test ) {
						$name_in_archive = $test;
					}
				}

				$file_size = filesize( $file_name );
				if ( false === $file_size ) {
					return false;
				}

				$zip_file_stat = $this->ziparchive->statName( $name_in_archive );
				// If the file is already in the archive doing anything else.
				if ( isset( $zip_file_stat['size'] ) && $zip_file_stat['size'] === $file_size ) {
					return true;
				}

				// The file is in the archive but the size is different than the one we
				// want to store. So delete the old and store the new one.
				if ( $zip_file_stat ) {
					$this->ziparchive->deleteName( $name_in_archive );
					// Reopen on deletion.
					$this->file_count = 21;
				}

				// Close and reopen, all added files are open on fs.
				// 35 works with PHP 5.2.4 on win.
				if ( $this->file_count > 20 ) {
					if ( ! $this->ziparchive->close() ) {
						$this->ziparchive_status();
						trigger_error(
							esc_html__( 'ZIP archive cannot be closed correctly', 'pixproof' ),
							E_USER_ERROR
						);

						sleep( 1 );
					}

					$this->ziparchive = null;

					if ( ! $this->check_archive_filesize() ) {
						return false;
					}

					$this->ziparchive = new ZipArchive();
					$ziparchive_open  = $this->ziparchive->open( $this->file, ZipArchive::CREATE );

					if ( $ziparchive_open !== true ) {
						$this->ziparchive_status();

						return false;
					}

					$this->file_count = 0;
				}

				if ( $file_size < ( 1024 * 1024 * 2 ) ) {
					if ( ! $this->ziparchive->addFromString( $name_in_archive, file_get_contents( $file_name ) ) ) {
						$this->ziparchive_status();
						trigger_error(
							sprintf(
							/* translators: the $1 is the name of the archive. */
								esc_html__( 'Cannot add "%s" to zip archive!', 'pixproof' ),
								$name_in_archive
							),
							E_USER_ERROR
						);

						return false;
					} else {
						$file_factor      = round( $file_size / ( 1024 * 1024 ), 4 ) * 2;
						$this->file_count = $this->file_count + $file_factor;
					}
				} else {
					if ( ! $this->ziparchive->addFile( $file_name, $name_in_archive ) ) {
						$this->ziparchive_status();
						trigger_error(
							sprintf(
							/* translators: the $1 is the name of the archive. */
								esc_html__( 'Cannot add "%s" to zip archive!', 'pixproof' ),
								$name_in_archive
							),
							E_USER_ERROR
						);

						return false;
					} else {
						$this->file_count ++;
					}
				}
				break;

			case 'PclZip':
				$this->pclzip_file_list[] = array(
					PCLZIP_ATT_FILE_NAME          => $file_name,
					PCLZIP_ATT_FILE_NEW_FULL_NAME => $name_in_archive,
				);

				if ( count( $this->pclzip_file_list ) >= 100 ) {
					if ( 0 == $this->pclzip->add( $this->pclzip_file_list ) ) {
						trigger_error(
							sprintf(
							/* translators: The $1 is the tecnical error string from pclzip. */
								esc_html__( 'PclZip archive add error: %s', 'pixproof' ),
								$this->pclzip->errorInfo( true )
							),
							E_USER_ERROR
						);

						return false;
					}
					$this->pclzip_file_list = array();
				}
				break;
		}

		return true;
	}

	/**
	 * Add a empty Folder to archive
	 *
	 * @param string $folder_name     Name of folder to add to archive.
	 * @param string $name_in_archive The name of archive to use within the archive.
	 *
	 * @return bool
	 */
	public function add_empty_folder( $folder_name, $name_in_archive ) {

		$folder_name = trim( $folder_name );

		if ( empty( $folder_name ) ) {
			trigger_error(
				esc_html__( 'Folder name cannot be empty', 'pixproof' ),
				E_USER_WARNING
			);

			return false;
		}

		if ( ! is_dir( $folder_name ) || ! is_readable( $folder_name ) ) {
			trigger_error(
				sprintf(
				/* translators: $1 is the folder name */
					esc_html_x(
						'Folder %s does not exist or is not readable',
						'Folder path to add to archive',
						'pixproof'
					),
					$folder_name
				),
				E_USER_WARNING
			);

			return false;
		}

		if ( empty( $name_in_archive ) ) {
			return false;
		}

		// Remove reserved chars.
		$name_in_archive = remove_invalid_characters_from_directory_name( $name_in_archive );

		switch ( $this->method ) {
			case 'ZipArchive':
				if ( ! $this->ziparchive->addEmptyDir( $name_in_archive ) ) {
					trigger_error(
						sprintf(
						/* translators: $1 is the name of the archive. */
							esc_html__( 'Cannot add "%s" to zip archive!', 'pixproof' ),
							$name_in_archive
						),
						E_USER_WARNING
					);

					return false;
				}
				break;

			case 'PclZip':
				return true;
				break;
		}

		return true;
	}

	/**
	 * Output status of ZipArchive
	 *
	 * @return bool
	 */
	private function ziparchive_status() {

		if ( $this->ziparchive->status === 0 ) {
			return true;
		}

		trigger_error(
			sprintf(
			/* translators. $1 is the status returned by a call to a ZipArchive method. */
				esc_html_x( 'ZipArchive returns status: %s', 'Text of ZipArchive status Message', 'pixproof' ),
				$this->ziparchive->getStatusString()
			),
			E_USER_ERROR
		);

		return false;
	}

	/**
	 * Check Archive File size
	 *
	 * @param string $file_to_add THe file to check
	 *
	 * @return bool True if the file size is less than PHP_INT_MAX false otherwise.
	 */
	public function check_archive_filesize( $file_to_add = '' ) {

		$file_to_add_size = 0;

		if ( ! empty( $file_to_add ) ) {
			$file_to_add_size = filesize( $file_to_add );

			if ( $file_to_add_size === false ) {
				$file_to_add_size = 0;
			}
		}

		if ( is_resource( $this->filehandler ) ) {
			$stats        = fstat( $this->filehandler );
			$archive_size = $stats['size'];
		} else {
			$archive_size = filesize( $this->file );
			if ( $archive_size === false ) {
				$archive_size = PHP_INT_MAX;
			}
		}

		$archive_size = $archive_size + $file_to_add_size;
		if ( $archive_size >= PHP_INT_MAX ) {
			trigger_error(
				sprintf(
					esc_html__(
						'If %s will be added to your backup archive, the archive will be too large for operations with this PHP Version. You might want to consider splitting the backup job in multiple jobs with less files each.',
						'pixproof'
					),
					$file_to_add
				),
				E_USER_ERROR
			);

			return false;
		}

		return true;
	}

	/**
	 * Fopen
	 *
	 * @param string $filename The file to open in mode.
	 * @param string $mode     The mode to open the file.
	 *
	 * @return bool|resource The resources or false if file cannot be opened.
	 */
	private function fopen( $filename, $mode ) {

		$fd = fopen( $filename, $mode );

		if ( ! $fd ) {
			trigger_error(
				sprintf(
				/* translators: $1 is the filename to add into the archive. */
					esc_html__( 'Cannot open source file %s.', 'pixproof' ),
					$filename
				),
				E_USER_WARNING
			);
		}

		return $fd;
	}

	/**
	 * Write Content in File
	 *
	 * @param string $content The content to write into the file.
	 *
	 * @return int The number of bit wrote into the file.
	 */
	private function fwrite( $content ) {

		return (int) fwrite( $this->filehandler, $content );
	}

	/**
	 * Close file handler
	 *
	 * @return void
	 */
	private function fclose() {

		fclose( $this->filehandler );
	}
}
