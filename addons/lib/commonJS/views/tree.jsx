var TreeView = React.createClass({
  createItem: function(object) {
    return <li>
      <i></i>
      <a data-fake href={object.url}
         onDrop={this.drop.bind(this, object)}
         onDragStart={this.dragStart.bind(this, object)}>{object.name}</a>
      <ul>
        {this.createItems(object.children)}
      </ul>
    </li>;
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

    return <ul>
      <li>
        <i></i>
        <a data-fake href="/">Home</a>
        <ul>
          {els}
        </ul>
      </li>
    </ul>;
  }
});