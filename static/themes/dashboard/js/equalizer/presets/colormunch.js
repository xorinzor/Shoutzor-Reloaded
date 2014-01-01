(function() {

    decay(0.1);

    var n = ((time*12)%16)>>0, s = 1/16;
    for (var x=0;x<16;x++)
      for (var y=0;y<16;y++)
        drawRect(0.2 + x*s*0.6, 0.2 + (y)*s*0.6, s * 0.4, s * 0.4, ((x^y)<n) ? "white" : "black");


    drawRect(0.2, 0.2, 0.6, 0.6, "hsla(" + (time%2/2*360) + ",100%,50%,0.1)", "white", 2);

    var tb = time*2*soundData.bass;
    var tm = time*2*soundData.mid;
    deform(function(radius, angle, x, y){
      var rp = radius * PI;
      return {
        stretchX : 1.0 + sin(tb+rp*soundData.bass) * 0.2,
        stretchY : 1.0 - sin(tm+rp*soundData.treb) * 0.2,
      }
    }, 6, 6);

    commit();

});