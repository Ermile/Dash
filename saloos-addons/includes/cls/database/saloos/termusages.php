<?php
namespace database\saloos;
class termusages 
{
	public $term_id           = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'term'            ,'type'=>'int@10'                          ,'foreign'=>'terms@id!term_title'];
	public $termusage_id      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'termusage'       ,'type'=>'bigint@20'];
	public $termusage_foreign = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'foreign'         ,'type'=>'enum@posts,products,attachments,files,comments'];
	public $termusage_order   = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'order'           ,'type'=>'smallint@5'];

	//--------------------------------------------------------------------------------foreign
	public function term_id()
	{
		$this->form()->type('select')->name('term_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------foreign
	public function termusage_id()
	{
		$this->form()->type('select')->name('termusage_')->required();
		$this->setChild();
	}

	public function termusage_foreign()
	{
		$this->form()->type('radio')->name('foreign');
		$this->setChild();
	}

	public function termusage_order()
	{
		$this->form()->type('number')->name('order')->min()->max('99999');
	}
}
?>