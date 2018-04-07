<?php
namespace content_enter\hook;

class controller extends  \mvc\controller
{

	public function ready()
	{
		$this->post('user')->ALL();

		$this->delete('chat_id')->ALL();
	}

}
?>