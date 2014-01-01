
(function() {

    var n = 60;
    var p = [];

    decay(0.05);
    
    quality(1.0);

    var r = 0.45;
    var stretchFactor = 1/1.1;

    var s = min(100, 50 + soundData.bass * 30);
    var rf = 0.05 * (0.8 + soundData.bass * 0.1) 

    var circles = [];

    for (var i=0;i<n;i++) {
      var wave = soundData.waveDataL[(i/n*256)>>0];

      var d = i/n * PI2 - PI*0.5;

      var radius = max(0.001, 0.005 + wave * rf);

      var x = .5 + cos(d) * r;
      var y = .5 + sin(d) * r;

      circles[i] = {
        x : x, y : y, radius : radius
      };

    }
    drawCircles(circles, "hsl(" + ((time*20000)>>360) + ",100%," + (70 * soundData.bass) + "%)");

    zoom(stretchFactor);
    if (soundData.bass > 1.0)
      rotate(2*PI2/n * (time%3 > 1.5 ? 1 : -1));
    commit();

});