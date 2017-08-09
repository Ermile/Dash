<?php
namespace database\saloos;
class users 
{
	public $id               = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'int@10'];
	public $user_mobile      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'mobile'          ,'type'=>'varchar@15'];
	public $user_email       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'email'           ,'type'=>'varchar@50'];
	public $user_pass        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'pass'            ,'type'=>'varchar@64'];
	public $user_displayname = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'displayname'     ,'type'=>'varchar@50'];
	public $user_meta        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'mediumtext@'];
	public $user_status      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@active,awaiting,deactive,removed,filter!awaiting'];
	public $user_permission  = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'permission'      ,'type'=>'smallint@5'];
	public $user_createdate  = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'datetime@'];
	public $date_modified    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'modified'        ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}

	public function user_mobile()
	{
		$this->form('#mobile')->type('text')->name('mobile')->maxlength('15')->required();
	}

	public function user_email()
	{
		$this->form('#email')->type('email')->name('email')->maxlength('50');
	}

	public function user_pass(){}

	public function user_displayname()
	{
		$this->form()->type('text')->name('displayname')->maxlength('50');
	}

	public function user_meta(){}

	public function user_status()
	{
		$this->form()->type('radio')->name('status');
		$this->setChild();
	}

	public function user_permission()
	{
		$this->form()->type('number')->name('permission')->min()->max('99999');
	}

	public function user_createdate()
	{
		$this->form()->type('text')->name('createdate')->required();
	}

	public function date_modified(){}
}
?>