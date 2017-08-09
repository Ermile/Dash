<?php
namespace database\saloos;
class notifications 
{
	public $id                   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $user_idsender        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'user sender'     ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_sende_displayname'];
	public $user_id              = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $notification_title   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'title'           ,'type'=>'varchar@50'];
	public $notification_content = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'content'         ,'type'=>'varchar@200'];
	public $notification_meta    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'mediumtext@'];
	public $notification_url     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'url'             ,'type'=>'varchar@100'];
	public $notification_status  = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@read,unread,expire!unread'];
	public $date_modified        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'modified'        ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------foreign
	public function user_idsender()
	{
		$this->form()->type('select')->name('user_sender');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------foreign
	public function user_id()
	{
		$this->form()->type('select')->name('user_')->required();
		$this->setChild();
	}

	public function notification_title()
	{
		$this->form('#title')->type('text')->name('title')->maxlength('50')->required();
	}

	public function notification_content()
	{
		$this->form()->type('textarea')->name('content')->maxlength('200');
	}

	public function notification_meta(){}

	public function notification_url()
	{
		$this->form()->type('text')->name('url')->maxlength('100');
	}

	public function notification_status()
	{
		$this->form()->type('radio')->name('status')->required();
		$this->setChild();
	}

	public function date_modified(){}
}
?>