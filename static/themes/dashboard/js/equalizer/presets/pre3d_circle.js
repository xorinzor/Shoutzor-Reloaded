// Test using Dean McNamee's Pre3D renderer - see: http://deanm.github.com/pre3d/

(function() {

var boxes = [], n = 64;
var avgLevels = [];
for (var i=0;i<n;i++)
  avgLevels[i] = 0;

var bs = 0.05;

Pre3dInit();

for (var i=0;i<n;i++) {
  boxes.push(Pre3d.ShapeUtils.makeBox(bs, bs, bs));
}

Pre3dRenderer.camera.focal_length = 2.5;
Pre3dCamera(0, 0, -10, 0.5, 0, 0);

return function() {
    decay(0.75);
    quality(0.75);

    var levels = [];
    for (var i=0;i<n;i++)
      levels[i] = 0;
    for (var i=0;i<128;i++)
      levels[(i/128*n)>>0] += soundData.eqDataL[i];
    for (var i=0;i<n;i++)
      avgLevels[i] = avgLevels[i]*0.5 + levels[i]*0.5;

    var n1 = 1 / n, n2 = 1 / (n * 2);

    var transform = Pre3dRenderer.transform;

    for (var i=0;i<n;i++) {
      var theta = i/n * PI2 + time;

      transform.reset();
      transform.rotateY(theta);

      var level = 0;
      if (avgLevels[i] > 0)
        level = ((levels[i] - avgLevels[i]) + (levels[i] / avgLevels[i]) + (levels[i] * 0.4)) * 1.3; 
      var h = pow(max(0,level),0.75)*0.15;
      h = ((h / n2) >> 0) * n2 * 20;

      if (h < 2) h = 2;

      transform.translate(
	cos(theta) * (2 + soundData.bass * 0.15), 
        bs,
	sin(theta) * (2 + soundData.bass * 0.15)
      );
      transform.scale(1,h,1);
      transform.translate(0, -1, 0);

      var hue = (theta - PI/2) / PI2 * 360;
      var rgb = hsl2rgb(hue, 100, 20 + soundData.bass * 20);

      Pre3dRenderer.fill_rgba = new Pre3d.RGBA(rgb.r / 255, rgb.g / 255, rgb.b / 255, 1);
      Pre3dRenderer.bufferShape(boxes[i]);
    }

    Pre3dRenderer.drawBuffer();
    Pre3dRenderer.emptyBuffer();

    commit();
};

})();