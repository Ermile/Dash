var BreadcrumbView = React.createClass({displayName: "BreadcrumbView",
  createItem: function(object) {
    return React.createElement("li", null, 
      React.createElement("i", {className: "fa fa-arrow-right"}), 
      React.createElement("a", {"data-fake": true, href: object.url}, object.name)
    );
  },
  render: function() {
    var _self = this;
    var url = '';

    var els = this.props.items.map(function(o) {
      url += '/' + o;
      return _self.createItem({
        name: o,
        url: url
      });
    });

    var home = React.createElement("li", null, 
      React.createElement("a", {"data-fake": true, href: "/"}, "Home")
    );

    els.unshift(home);

    return React.createElement("ul", {className: "fbreadcrumb"}, els);
  }
});
// React Views
// Marboot be bakhshe files.talambar.dev

var EXT_MAP = {
  text: {
    plain: 'text',
    html: 'code',
    css: 'code',
    'x-c': 'code',
    default: 'text'
  },
  audio: {
    default: 'audio'
  },
  video: {
    default: 'video'
  },
  image: {
    default: 'image'
  },
  application: {
    default: '',
    'pdf': 'pdf',
    'msword': 'word',
    'vnd.openxmlformats-officedocument.wordprocessingml.document': 'word',
    'vnd.openxmlformats-officedocument.wordprocessingml.template': 'word',
    'vnd.ms-excel': 'excel',
    'vnd.ms-powerpoint': 'powerpoint',

    'javascript': 'code',
    'json': 'code',

    'zip': 'archive',
    'x-7z-compressed': 'archive',
    'x-rar-compressed': 'archive',
    'x-tar': 'archive'
  }
};

var selected = [];
var selection = {
  startingPoint: 0,
  lastPoint: 0
}

function mouseUp(e) {
    var li = this.refs.li.getDOMNode();
    if ((e.ctrlKey || e.metaKey) && e.shiftKey) {
      var lastLi = $('[data-id="'+selection.lastPoint+'"]'),
          lastIndex = lastLi.index(),
          current = $(li).index();

      if (current < lastIndex) {
        var tmp = current;
        current = lastIndex;
        lastIndex = tmp;
      }

      lastIndex--;

      var between = this._owner.props.currentItems.slice(lastIndex, current),
          els = lastLi.parent().children().slice(lastIndex + 1, current + 1);

      var _ = this;
      selected = selected.concat(between.map(function(a) {
        return a.id;
      }));
      els.addClass('selected');
    }
    else if (e.ctrlKey || e.metaKey) {
      var index = selected.indexOf(this.props.id);
      if (index > -1) {
        selected.splice(index, 1);
        $(li).removeClass('selected');
      } else {
        selected.push(this.props.id);
        li.className += ' selected';
      }
      selection.lastPoint = this.props.id;
    } else if (e.shiftKey) {
      var lastLi = $('[data-id="'+selection.lastPoint+'"]'),
          lastIndex = lastLi.index(),
          current = $(li).index();

      if (current < lastIndex) {
        var tmp = current;
        current = lastIndex;
        lastIndex = tmp;
      }

      lastIndex--;

      var between = this._owner.props.currentItems.slice(lastIndex, current),
          els = lastLi.parent().children().slice(lastIndex + 1, current + 1);

      var _ = this;
      selected = between.map(function(a) {
        return a.id;
      });
      $('.selected').removeClass('selected');
      els.addClass('selected');
    } else {
      setTimeout(function() {
        $('.selected').removeClass('selected');
        li.className += ' selected';
        selected = [this.props.id];
        selection.lastPoint = this.props.id;
      }.bind(this), 1);
    }
}

var FileView = React.createClass({displayName: "FileView",
  render: function() {
    var typeClass;
    var mime = this.props.ext.split('/');
    if (mime[0] && mime[1]) {
      var ext = EXT_MAP[mime[0]];

      var cls = ext[mime[1]] || ext.default;
      if (cls) cls = '-' + cls;
      typeClass = 'file-ext fa fa-file' + cls + '-o';
    } else {
      typeClass = 'file-ext fa fa-file-o';
    }

    return React.createElement("li", {className: this.props.disabled ? 'file disabled' : 'file', draggable: true, 
            onDragStart: this.dragStart, 
            onDragEnd: this.dragEnd, 
            onMouseUp: this.mouseUp, 
            onDoubleClick: this.dbl, 
            ref: "li", "data-id": +this.props.id, draggable: "true"}, 
      React.createElement("span", {className: typeClass}), 
      React.createElement("span", null, this.props.name), 
      React.createElement("span", {className: "file-type"}, mime[0]), 
      React.createElement("span", {className: "file-size"}, this.props.size)
    );
  },

  mouseUp: mouseUp,
  dbl: function(e) {
    Navigate({
      url: this.props.href,
      fake: true
    });
  },
  dragStart: function(e) {
    e.dataTransfer.setData('text/plain', this.props.id);
    selected.dragging = true;
    console.log('true');
  },
  dragEnd: function(e) {
    selected.dragging = false;
  }
});

var FolderView = React.createClass({displayName: "FolderView",
  render: function() {
    return React.createElement("li", {className: this.props.disabled ? 'folder disabled' : 'folder', draggable: true, droppable: true, 
            onDragStart: this.dragStart, 
            onDragEnd: this.dragEnd, 
            onDrop: this.drop, 
            onMouseUp: this.mouseUp, 
            onDoubleClick: this.dbl, 
            ref: "li", "data-id": this.props.id, draggable: "true"}, 
      React.createElement("span", {className: "fa fa-folder-o"}), 
      React.createElement("span", null, this.props.name), 
      React.createElement("span", {className: "folder-type"}, "Folder"), 
      React.createElement("span", {className: "folder-children"}, this.props.children)
    );
  },

  drop: function(e) {
    $('.selected').removeClass('selected');
    for(var i = 0, len = selected.length; i < len; i++) {
      var target = fileList.filterObject({id: selected[i]})[0];
      target.parent.set(this.props.id.toString());
    }
    selected = [];
    selected.dragging = false;
  },

  mouseUp: mouseUp,
  dbl: function(e) {
    Navigate({
      url: this.props.href,
      fake: true
    });
  },
  dragStart: function(e) {
    e.dataTransfer.setData('text/plain', this.props.id);
    selected.dragging = true;
  },
  dragEnd: function(e) {
    selected.dragging = false;
  }
})

var FileList = React.createClass({displayName: "FileList",
  pathFor: function(item) {
    var path = '/' + item.name;

    var parent = _.findWhere(this.props.items, {id: item.parent});
    while(parent) {
      path = '/' + parent.name + path;

      parent = _.findWhere(this.props.items, {id: parent.parent});
    }

    return path;
  },
  createItems: function() {
    if(this.props.parent === null) return null;
    var parent = _.matches({parent: this.props.parent || ''});

    var _self = this;

    var itemsList = _.filter(this.props.items, parent).sort(function(a, b) {
      return (+a.order||0) - (+b.order||0);
    })
    var items = itemsList.map(function(item,i) {
      if(item.type === 'folder')
        return React.createElement(FolderView, React.__spread({},  item, {href: _self.pathFor(item)}));
      else
        return React.createElement(FileView, React.__spread({},  item, {href: _self.pathFor(item)}));
    })

    if(!items || !items.length) return null;

    this.props.currentItems = itemsList;
    return items;
  },
  selectAll: function() {
    selected = this.props.items.map(function(a) {
      return a.id;
    });

    $('.file, .folder').not('#newform').addClass('selected');
  },
  render: function() {
    return React.createElement("ul", null, 
      React.createElement("li", {className: "folder hidden", ref: "newfolder", id: "newform"}, 
        React.createElement("form", {method: "post", action: "/newfolder", ref: "form"}, 
          React.createElement("span", {className: "fa fa-folder-o"}), 
          React.createElement("span", null, React.createElement("input", {type: "text", name: "address", type: "hidden"})), 
          React.createElement("span", null, React.createElement("input", {type: "text", name: "name"})), 
          React.createElement("span", {className: "folder-type"}, "Folder"), 
          React.createElement("span", {id: "createFolder"}, React.createElement("button", null, "Create")), 
          React.createElement("span", {id: "cancelCreateFolder"}, React.createElement("button", {type: "button", ref: "cancel"}, "Cancel"))
        )
      ), 

      this.createItems() || 'No Files Found'
    );
  },
  componentDidMount: function() {
    $form = $(this.refs.form.getDOMNode());
    $newfolder = $(this.refs.newfolder.getDOMNode());

    $form.submit(function(e) {
      $form.find('[name="address"]').val(location.pathname);
      $newfolder.addClass('hidden');

      fileList.push({
        name: $form.find('[name="name"]').val(),
        parent: component.props.parent,
        type: 'folder',
        disabled: true
      });
    });

    $(this.refs.cancel.getDOMNode()).click(function(e) {
      $newfolder.addClass('hidden').find('[name="name"]').val('');
    });
  }
});
var TreeView = React.createClass({displayName: "TreeView",
  createItem: function(object) {
    return React.createElement("li", null, 
      React.createElement("i", null), 
      React.createElement("a", {"data-fake": true, href: object.url, 
         onDrop: this.drop.bind(this, object), 
         onDragStart: this.dragStart.bind(this, object)}, object.name), 
      React.createElement("ul", null, 
        this.createItems(object.children)
      )
    );
  },
  drop: function(object, e) {
    if (object.type !== 'folder') return;
    var id = e.dataTransfer.getData('text/plain');

    var dragged = fileList.filterObject({id: id})[0];

    dragged.parent.set(object.id.toString());
  },
  dragStart: function(object, e) {
    e.dataTransfer.setData('text/plain', object.id);
  },
  createItems: function(list) {
    var _self = this;
    var els = [];
    for(var item in list) {
      els.push(_self.createItem(list[item]));
    }

    return els;
  },
  render: function() {
    var els = this.createItems(this.props.items);

    return React.createElement("ul", null, 
      React.createElement("li", null, 
        React.createElement("i", null), 
        React.createElement("a", {"data-fake": true, href: "/"}, "Home"), 
        React.createElement("ul", null, 
          els
        )
      )
    );
  }
});