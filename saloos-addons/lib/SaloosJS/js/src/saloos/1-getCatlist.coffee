class window.saloos.getCatlist
	constructor : (el)->
		$('.panel-body .item span', el).unbind 'click.getCatlist'
		$('.panel-body .item span', el).bind 'click.getCatlist', () ->
			parent = $(@).parents('.panel')
			if $(parent).hasClass('data-disabled')
				return
			parent.addClass('data-disabled')
			$(":checkbox", parent).attr('disabled', '')
			id = $(@).parents('.item').find(':checkbox').val()
			ajax.call(@, id)
	ajax = (id) ->
		if(!/^\d+$/.test(id))
			id = ''
			parent = $(@).parents('.panel')
			$('.panel-heading span', parent).remove();
		addr = location.pathname.replace(/\/[^\/]*$/, '') + "/options"
		$.ajax({
			context: @
			url : addr
			data : {parent:id, type: "getcatlist"}
			}).done (obj, header, xhr) ->
				if xhr.status != 200
					return
				$('.cat-list').empty()
				parent = $(@).parents('.panel')
				if(!parent[0])
					parent = $(".cats")

				parent.removeClass('data-disabled')
				$('.panel-heading i', parent).removeClass('hidden')
				$('.panel-heading span', parent).remove();
				if(id != '')
					$("<span> × #{$(@).text()} </span>").appendTo($('.panel-heading', parent))
					$('.panel-heading span', parent).click ajax
				$(":checkbox", parent).removeAttr('disabled')
				$('#cat-list', parent).empty()
				for i in [0...obj.data.length]
					ch = $(":checkbox[value=#{obj.data[i].id}]", parent)
					if(ch.length)
						continue
					label = $("<label class='item'><input type='checkbox' name='categories[]' value='#{obj.data[i].id}' data-slug='book-index/haj'> <span>#{obj.data[i].title}</span></label>")
					label.appendTo($('#cat-list', parent))
				cat_selected()
				new saloos.getCatlist(parent[0])
				undefined
		return false;

route('*', ()->
	$(".cats", this).each(()->
		new saloos.getCatlist(@)
		)
	)