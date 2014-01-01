(function() {

// setup initial points
var points = [];
var nx = 8, ny = 8;
var sx = 1/nx * 0.5;
var sy = 1/ny * 0.5;
var i = 0;
for (var y=0;y<ny;y++) {
  for (var x=0;x<nx;x++) {
    var cx = 0.08 + 0.8 * x/nx + sx;
    var cy = 0.08 + 0.8 * y/ny + sy;
    points[i] = {x : cx, y : cy, ox : cx, oy : cy, radius : 0.02};
    i++;
  }
}

var mode = 0;
var maxMode = 2;

return function() {

    //quality(0.75);
    quality(1);
    decay(0.25);

    if (time%4 > 3 && soundData.bass % 0.5 > 0.25) {
      mode = round(random() * maxMode)
    }

    // modify point radius
    var i = 0;
    for (var y=0;y<ny;y++) {
      for (var x=0;x<nx;x++) {
        if (x < nx*0.5)
          var spec = pow(soundData.eqDataR[(x/nx*256)  >>0], 1.2);
        else
          var spec = pow(soundData.eqDataL[((1-x/nx)*256)>>0], 1.2);

        var wave = pow(soundData.waveDataL[(y/ny*256)>>0], 2.5) * 1.5;

        points[i].radius = 0.01 + soundData.mid * 0.005;

        if (mode == 1) {
          points[i].y = points[i].oy + (i%2 ? 1 : -1) * 0.025;
        } else {
          points[i].y = points[i].oy;
        }

        i++;
      }
    }

    // draw points
    drawCircles(points, null, "white", 5 * soundData.treb);

    var stretchFactor = 0.4 + 0.2 * soundData.bass;

    deform(function(r, a, x, y) {
      return {
        zoom : 1.0 + r * abs(y-0.5) * 0.2 * soundData.bass - r * soundData.mid * 0.1,
        stretchY : mode == 2 ? (1 - (time%4 > 2 ? x : 1-x) * stretchFactor) : 1.0,
        stretchX : mode == 2 ? stretchFactor : 1.0,
        rotate : mode == 1 && time%5>4 ? 0.1 * sign(sin(time)) : 0
      };
    }, 5, 5);

    commit();
}

})();