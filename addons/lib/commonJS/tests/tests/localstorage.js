describe('LocalStorage', function() {
  describe('#set', function() {
    it('should stringify objects correctly', function() {
      var obj = {a: 2},
          strObj = JSON.stringify(obj),
          arr = [1,2],
          strArr = JSON.stringify(arr);

      LS.set('test', obj);
      assert.equal(localStorage.getItem('test'), strObj);
      LS.set('arr', arr);
      assert.equal(localStorage.getItem('arr'), strArr);
    })
  })

  describe('#get', function() {
    it('should parse objects correctly', function() {
      assert(_.isEqual(LS.get('test'), {a: 2}));
      assert(_.isEqual(LS.get('arr'), [1,2]));
    })
    it('should parse numbers correctly', function() {
      LS.set('num', 2);
      assert.equal(LS.get('num'), 2);
    })
    it('should not modify strings', function() {
      LS.set('string', 'salam');

      assert.equal(LS.get('string'), 'salam');
    })
  })

  describe('#push', function() {
    it('should push correctly', function() {
      LS.push('arr', 1, 2, 3);
      assert(_.isEqual(LS.get('arr'), [1,2,1,2,3]));
    })
  })
  
  describe('#extend', function() {
    it('should extend objects correctly', function() {
      LS.extend('test', {b: 3});
      assert(_.isEqual(LS.get('test'), {a: 2, b: 3}));
    })
  })
})