(function() {

    var n = 100;
    var p = [];

    decay(0.08);
    quality(0.75);

    var r = 0.2;

    var s = min(100, 50 + soundData.bass * 30);
    var p = [];
    for (var i=0;i<n;i++) {
      var wave = soundData.waveDataL[(i/n*256)>>0];

      var d = i/n * PI2 - PI*0.5;

      var radius = r + wave * 0.05 * (0.8 + soundData.bass * 0.1) ;

      var x = .5 + cos(d) * radius;
      var y = .5 + sin(d) * radius;

      p[i] = [x,y];
    }
    drawPath(p, true, null, "white", 0.5);

    if (soundData.bass > 0.5)
      rotate(2*PI2/n * sin(soundData.bass * PI2 + time));

    var sf = 1.0 + sin(time/2) * 0.2;
    stretch(sf, sf, 0.5, 0.5);
    commit();

    if (soundData.mid > 0.5) {
      drawRect(0,0,1.0,1.0,null,soundData.treb > 0.8 ? "green" : "white",40);

      deform(function(r, a, x, y) {
        return {
          zoom : 0.8 + r * 0.1 - soundData.bass * 0.2 * (1.5-r),
          rotate : r * 0.2 * soundData.bass - 0.15 * soundData.bass
        }
      }, 6, 6);
      commit();
    }

});