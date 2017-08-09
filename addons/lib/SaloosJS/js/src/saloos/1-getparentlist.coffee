class window.saloos.getParentlist
	constructor : (el)->
		name = $(el).attr('name')
		$(el).removeAttr('name')
		$("<input id=\"hidden-parent\" type=\"hidden\" name=\"#{name}\" value=\"#{$(el).val()}\">").insertBefore($(el))
		$(el).change (change)
	change = () ->
		val = $(@).val()
		remove.call(@)
		if(val == '')
			val = $(@).prev('select').val()
			$("#hidden-parent").val(val)
			return
		$("#hidden-parent").val(val)
		$(@, $(@).parents(".panel")).attr('disabled', '')
		addr = location.pathname.replace(/\/[^\/]*$/, '') + "/options"
		$.ajax({
			context: @
			url : addr
			data : {parent:val, type: "getparentlist"}
			}).done (obj, header, xhr) ->
				if xhr.status != 200
					$("#hidden-parent").val('')
					return
				parent = $(@).parents(".panel")
				$(@, parent).removeAttr("disabled")
				if obj.data.length > 0
					select = $("<select class='input'></select>")
					select.insertAfter($(@))
					$("<option selected=\"selected\"></option>").appendTo(select)
					select.change (change)
					for i in [0...obj.data.length]
						$("<option value=\"#{obj.data[i].id}\">#{obj.data[i].title}</option>").appendTo(select)

				undefined
	remove = () ->
		_self = @
		parent = $(@).parents(".panel")
		start_remove = false
		$("select", parent).each ()->
			if(start_remove)
				$(@).remove()
			if $(@).is(_self)
				start_remove = true


route('*', ()->
	$("#sp-parent", this).each(()->
		new saloos.getParentlist(@)
		)
	)