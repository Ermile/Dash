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

var FileView = React.createClass({
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

    return <li className={this.props.disabled ? 'file disabled' : 'file'} draggable={true}
            onDragStart={this.dragStart}
            onDragEnd={this.dragEnd}
            onMouseUp={this.mouseUp}
            onDoubleClick={this.dbl}
            ref='li' data-id={+this.props.id} draggable='true'>
      <span className={typeClass}></span>
      <span>{this.props.name}</span>
      <span className="file-type">{mime[0]}</span>
      <span className="file-size">{this.props.size}</span>
    </li>;
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

var FolderView = React.createClass({
  render: function() {
    return <li className={this.props.disabled ? 'folder disabled' : 'folder'} draggable={true} droppable={true}
            onDragStart={this.dragStart}
            onDragEnd={this.dragEnd}
            onDrop={this.drop}
            onMouseUp={this.mouseUp}
            onDoubleClick={this.dbl}
            ref='li' data-id={this.props.id} draggable='true'>
      <span className='fa fa-folder-o'></span>
      <span>{this.props.name}</span>
      <span className="folder-type">Folder</span>
      <span className="folder-children">{this.props.children}</span>
    </li>;
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

var FileList = React.createClass({
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
        return <FolderView {...item} href={_self.pathFor(item)} />;
      else
        return <FileView {...item} href={_self.pathFor(item)} />;
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
    return <ul>
      <li className='folder hidden' ref='newfolder' id='newform'>
        <form method="post" action="/newfolder" ref='form'>
          <span className='fa fa-folder-o'></span>
          <span><input type='text' name='address' type='hidden' /></span>
          <span><input type='text' name='name' /></span>
          <span className="folder-type">Folder</span>
          <span id='createFolder'><button>Create</button></span>
          <span id='cancelCreateFolder'><button type='button' ref='cancel'>Cancel</button></span>
        </form>
      </li>

      {this.createItems() || 'No Files Found'}
    </ul>;
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