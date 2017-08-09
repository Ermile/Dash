<?php
namespace database\saloos;
class logitems 
{
	public $id               = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'smallint@5'];
	public $logitem_title    = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'title'           ,'type'=>'varchar@100'];
	public $logitem_desc     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'desc'            ,'type'=>'varchar@999'];
	public $logitem_meta     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'mediumtext@'];
	public $logitem_priority = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'priority'        ,'type'=>'enum@critical,high,medium,low!medium'];
	public $date_modified    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'modified'        ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}

	public function logitem_title()
	{
		$this->form('#title')->type('text')->name('title')->maxlength('100')->required();
	}

	public function logitem_desc()
	{
		$this->form('#desc')->type('textarea')->name('desc')->maxlength('999');
	}

	public function logitem_meta(){}

	public function logitem_priority()
	{
		$this->form()->type('radio')->name('priority')->required();
		$this->setChild();
	}

	public function date_modified(){}
}
?>