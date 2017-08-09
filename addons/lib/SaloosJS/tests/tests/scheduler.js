describe('Scheduler', function() {
  describe('#parseInputs', function() {
    it('should parse Persian date and time correctly', function() {
      var date = Schedule.parseInputs('#date', '#time');
      assert.equal(date.gDate.valueOf(), new pDate([1393,05,2,18,20]).gDate.valueOf());
    })
  })
  describe('#Schedule', function() {
    it('should fire at the correct time', function(done) {
      var date = new Date(new Date().valueOf() + 1000);
      var x = false;
      Schedule(date, function() {
        x = true;
      });
      setTimeout(function() {
        if(x == true) done(new Error('fired early'));
      }, 999);
      setTimeout(function() {
        if(x == true) done();
        else done(new Error("didn't fire at the correct time"));
      }, 1000)
    })
  })
})
