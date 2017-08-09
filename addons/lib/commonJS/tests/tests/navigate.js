var containerTest = null;

describe('Navigate', function() {

  describe('#Navigate', function() {
    it('should change location correctly', function() {
      Navigate({url: '/test'});
      assert.equal(location.pathname, '/test');
    })
    it('should change document title correctly', function() {
      Navigate({title: 'Yo!'});
      assert.equal(document.title, 'Yo!');
    })
    it('should put HTML in the right container', function() {
      containerTest = new Navigate({
                                html: '<h2>Test</h2>',
                                container: '#stateContainer',
                                title: 'Container/Undo'
                              });

      assert.equal($('#stateContainer').html(), '<h2>Test</h2>');
    })
  })
  describe('#undo', function() {
    before(function() {
      containerTest.undo();
    });
    it('should bring document title back', function() {
      assert.equal(document.title, 'Yo!');
    })
    it('should bring location back', function() {
      assert.equal(location.pathname, '/');
    })
    it('should bring HTML back', function() {
      assert.equal($('#stateContainer').html(), '<h2>State Container</h2>');
    })
  })
  after(function() {
    Navigate({url: '/js/tests', title: 'Mocha Tests'});
  })
})