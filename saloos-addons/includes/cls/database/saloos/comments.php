<?php
namespace database\saloos;
class comments 
{
	public $id              = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $post_id         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'post'            ,'type'=>'bigint@20'                       ,'foreign'=>'posts@id!post_title'];
	public $comment_author  = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'author'          ,'type'=>'varchar@50'];
	public $comment_email   = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'email'           ,'type'=>'varchar@100'];
	public $comment_url     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'url'             ,'type'=>'varchar@100'];
	public $comment_content = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'content'         ,'type'=>'mediumtext@'];
	public $comment_meta    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'mediumtext@'];
	public $comment_status  = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@approved,unapproved,spam,deleted!unapproved'];
	public $comment_parent  = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'parent'          ,'type'=>'smallint@5'                      ,'foreign'=>'comments@id!comment_title'];
	public $user_id         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $visitor_id      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'visitor'         ,'type'=>'bigint@20'                       ,'foreign'=>'visitors@id!id'];
	public $date_modified   = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'modified'        ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------foreign
	public function post_id()
	{
		$this->form()->type('select')->name('post_');
		$this->setChild();
	}

	public function comment_author()
	{
		$this->form()->type('text')->name('author')->maxlength('50');
	}

	public function comment_email()
	{
		$this->form('#email')->type('email')->name('email')->maxlength('100');
	}

	public function comment_url()
	{
		$this->form()->type('text')->name('url')->maxlength('100');
	}

	public function comment_content()
	{
		$this->form()->type('textarea')->name('content')->required();
	}

	public function comment_meta(){}

	public function comment_status()
	{
		$this->form()->type('radio')->name('status')->required();
		$this->setChild();
	}

	public function comment_parent()
	{
		$this->form()->type('select')->name('parent');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------foreign
	public function user_id()
	{
		$this->form()->type('select')->name('user_');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------foreign
	public function visitor_id()
	{
		$this->form()->type('select')->name('visitor_');
		$this->setChild();
	}

	public function date_modified(){}
}
?>