<?php 

/**
 * 
 * @package fpBackupPlugin
 * @subpackage Command
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class fpCommandBackup extends fpBaseCommand
{
  /**
   * 
   * @var string
   */
  protected $_options = array(
    'file' => false,
    'dsn' => false);
  
  /**
   * 
   * @param fpDsn $dsn
   * @param string $backupFile
   * @param bool $isZip
   */
  protected function _initialize()
  {
    $opt = $this->_options;
    
    if (!$opt['dsn'] instanceof fpDsn) {
      throw new InvalidArgumentException('The `dsn` option is requered and should an instance of `fpDsn` class');
    }
    
    if (!($opt['file'])) {
      throw new Exception('The `file` option is required. But the option `' . $opt['file'] . '` you give is not valid or file is not writable');
    }
    
    $this->_doExec('mysqldump -? > /dev/null');
    $this->_doExec('gzip -h > /dev/null');
    $this->_doExec('du --help> /dev/null');
  }
  
  public function exec()
  {
    $dsn = $this->_options['dsn'];
    $file = $this->_options['file'];
    
    $stderr = '2>> log/fp:backup-err.log';
    $stdout = '>> log/fp:backup.log';
    
    
    $mysqlDumpCmd = "mysqldump -u{$dsn->user()} ";
    if ($dsn->password()) $mysqlDumpCmd .= "-p{$dsn->password()} ";
    $mysqlDumpCmd .= "-h{$dsn->host()} {$dsn->database()} {$stderr}";
    
    $this->_doExecBackground(
      "mysqldump -u{$dsn->user()} -p{$dsn->password()} -h{$dsn->host()} {$dsn->database()} {$stderr} | " .
      "gzip -9 -f > {$file} {$stderr}");
    
    $this->_doExecWhileChanging("du -sB M {$file} {$stderr}");
  }
}
