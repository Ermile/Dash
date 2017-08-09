$.fn.dataTableExt.sErrMode = "throw"

class window.saloos.datatable
	first_make_data = true
	data_compile = Object()
	col_creat = Object()
	constructor : (el)->
		first_make_data = true
		if(el instanceof Element)
			try
				first_data = JSON.parse($("tbody td:first", el).text())
			catch e
				$("tbody td:first", el).html("<tr><td>Json paresError</td></tr>")
				console.log(e)
			if(first_data)
				$(el).empty();
				$(el).removeClass('hidden')
				run.call(el, first_data)

		else
			el.each ()->
				new window.saloos.datatable(@)
	run = (columns) ->

		o_columns = Array()
		if(columns.columns.id)
			columns.columns.id.table = true
		for cl of columns.columns
			if(columns.columns[cl]['table'])
				columns.columns[cl]['title'] = columns.columns[cl]['label']
				columns.columns[cl]['name'] = cl
				columns.columns[cl]['data'] = cl
				columns.columns[cl]['className'] = "col_"+columns.columns[cl]['value']
				obj = {
					title : columns.columns[cl]['label']
					name : cl
					data : cl
					className : "col_"+columns.columns[cl]['value']
					_resp : columns.columns[cl]
					createdCell : if col_creat[columns.columns[cl]['value']] then col_creat[columns.columns[cl]['value']] else null
					}
				if(cl == 'id')
					obj.className = "col_row"
				o_columns.push(obj)
		o_columns.push({
			orderable : false
			title : ""
			name : "id"
			data : "id"
			className : "col_actions"
			createdCell : if col_creat['action'] then col_creat['action'] else null
			})
		lang  = document.documentElement.lang.slice(0,2) + ".json"
		$(@).DataTable({
			language: { "url": (location.protocol)+"//"+(location.hostname).match(/([^\.]*)\.([^\.]*)$/)[0]+"/static/js/datatable/datatable-langs/" + lang}
			processing: true,
			serverSide: true,
			columns : o_columns,
			ajax: {
				cache: true,
				url : $(@).attr('data-tablesrc'),
				beforeSend : () ->
					if(!first_make_data)
						return 0
					first_make_data = false
					this.error = 0
					this.success(columns)
					return false
				data : (data) ->
					ret = Array()
					for d of data
						if(data_compile[d])
							val = data_compile[d](data[d], data)
							if(val)
								ret.push(val)
					return ret.join('&')
			}
			rowCallback : (row, data, index) ->
				# console.log(row, data, index)
			createdRow : (row, data, dataIndex)->
				# window.ffff = this
				# console.log(data)
				len = this.fnSettings()._iDisplayLength
				start = this.fnSettings()._iDisplayStart
				sort = this.fnSettings().aaSorting[0][1]
				total = this.fnSettings()._iRecordsDisplay
				if(sort == 'asc')
					num = dataIndex + start + 1
				else
					num = total - (dataIndex + start)
					data.num = num
				$('td:first', row).text(num)
				# console.log(this.fnSettings().aoColumns)
		}).on 'init.dt', () ->
			$this = $(this)
			$this.parents('.panel').fadeIn(300)

	data_compile.order = (order, data) ->
		col_name = data_compile.getColName(data, order[0]['column'])
		return "sortby=#{col_name}&order=#{order[0]['dir']}"

	data_compile.search = (search, data) ->
		if(search.value)
			return "search=#{search.value}"
	data_compile.length = (length) ->
		return "length=#{length}"
	data_compile.start = (start) ->
		return "start=#{start}"
	data_compile.draw = (draw) ->
		return "draw=#{draw}"
	data_compile.getColName = (data, col) ->
		return if data['columns'][col]['name'] then data['columns'][col]['name'] else col

	col_creat.action = (td, cellData, rowData, row, col) ->
		text = $(td).text()
		html = $("<span class=\"fa-stack fa-lg\">
            <i class=\"fa fa-square-o fa-stack-2x\"></i>
            <a class=\"fa fa-pencil fa-stack-1x label-default\" href=\"#{location.pathname}/edit=#{rowData.id}\"></a>
          </span>
          <span class=\"fa-stack fa-lg\">
            <i class=\"fa fa-square-o fa-stack-2x\"></i>
            <a class=\"fa fa-times fa-stack-1x label-danger\" href=\"#{location.pathname}/delete=#{rowData.id}\" data-data='{\"id\": #{rowData.id}}' data-method=\"post\" data-modal=\"delete-confirm\"></a>
          </span>")
		$(td).html(html)
	col_creat.title = (td, cellData, rowData, row, col) ->
		text = $(td).text()
		html = $("<a href='#{location.pathname}/edit=#{rowData.id}'>#{text}</a>")
		$(td).html(html)
	col_creat.url = (td, cellData, rowData, row, col)->
		text = $(td).text()
		root = $("meta[name='site:root']").attr('content')
		site_location = root + text
		html = $("<a href='#{site_location}?preview=yes' target='_blank'>#{text}</a>")
		$(td).html(html)
	col_creat.filetype = (td, cellData, rowData, row, col)->
		$(td).html('<i class="fa fa-2x fa-file-'+cellData.type+'-o"></i>')

route('*', () ->
	$("[data-tablesrc]", this).each () ->
		new window.saloos.datatable(@)
	)

