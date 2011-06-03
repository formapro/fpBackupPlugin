<?php

class fpCommandLoadSql extends fpExecCommandBase
{
  /**
   * 
   * @var string
   */
  protected $_options = array(
    'file' => false,
    'dsn' => false,
    'verbose' => false);
  
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
      throw new Exception('The `file` option is required. But the option `' . $opt['file'] . '` you gave is not readable');
    }
    
    $this->_doExec('mysql -?', false);
    $this->_doExec('gzip -h', false);
    $this->_doExec('du --help', false);
  }
  
  public function exec()
  {
    $dsn = $this->_options['dsn'];
    $file = $this->_options['file'];
    
    $stderr = '2>> log/fp:backup-err.log';
    $stdout = '>> log/fp:backup.log';
    
    $this->_doExecBackground("mysql -u{$dsn->user()} -p{$dsn->password()} -h{$dsn->host()} {$dsn->database()} {$stderr} < {$file}");
  }
}
