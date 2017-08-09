<?php
namespace database\saloos;
class logs 
{
	public $id             = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $logitem_id     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'logitem'         ,'type'=>'smallint@5'                      ,'foreign'=>'logitems@id!logitem_title'];
	public $user_id        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $log_data       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'data'            ,'type'=>'varchar@200'];
	public $log_meta       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'mediumtext@'];
	public $log_status     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@enable,disable,expire,deliver'];
	public $log_createdate = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'datetime@'];
	public $date_modified  = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'modified'        ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------foreign
	public function logitem_id()
	{
		$this->form()->type('select')->name('logitem_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------foreign
	public function user_id()
	{
		$this->form()->type('select')->name('user_');
		$this->setChild();
	}

	public function log_data()
	{
		$this->form()->type('textarea')->name('data')->maxlength('200');
	}

	public function log_meta(){}

	public function log_status()
	{
		$this->form()->type('radio')->name('status');
		$this->setChild();
	}

	public function log_createdate()
	{
		$this->form()->type('text')->name('createdate')->required();
	}

	public function date_modified(){}
}
?>