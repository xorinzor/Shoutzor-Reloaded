
// Test using Dean McNamee's Pre3D renderer - see: http://deanm.github.com/pre3d/
// Pre3D and original Colorscube demo (c) by Dean McNamee
(function() {

var num_cubes;
var nc = 5;
var cubes = [ ];

Pre3dInit();

for (var i = 0; i < nc; ++i) {
for (var j = 0; j < nc; ++j) {
for (var k = 0; k < nc; ++k) {
  if (i == 0 || j == 0 || k == 0 ||
      i == nc-1 || j == nc-1 || k == nc-1) {
    var cube = Pre3d.ShapeUtils.makeCube(1);
    var transform = new Pre3d.Transform();
    var x = (i - nc/2) * 2;
    var y = (j - nc/2) * 2;
    var z = (k - nc/2) * 2;
    transform.translate(x, y, z);
    cubes.push({
      shape: cube,
      color: new Pre3d.RGBA(i / nc, j / nc, k / nc, 0.7),
      trans: transform,
      x : x, y : y, z : z, row : i
     });
  }
}
}
}

num_cubes = cubes.length;

Pre3dRenderer.camera.focal_length = 2.5;
Pre3dCamera(0, 0, -30, 0.40, -1.06, 0);

quality(0.75);

return function() {
    decay(1.0);

    var t = time/5;
    Pre3dCamera(0, 0, -30, 0.40, -1.06 + sin(t) * PI2, cos(t) * PI2);

    for (var i = 0; i < num_cubes; ++i) {
      var cube = cubes[i];

      var scale = 0.5 + pow(soundData.eqDataL[i / num_cubes * 256 >> 0], 0.5)

      var trans = cube.trans;
      trans.reset();
      trans.scale(scale,scale,scale);
      trans.translate(cube.x, cube.y, cube.z);

      Pre3dRenderer.fill_rgba = cube.color;
      Pre3dRenderer.transform = cube.trans;
      Pre3dRenderer.bufferShape(cube.shape);
    }

    Pre3dRenderer.drawBuffer();
    Pre3dRenderer.emptyBuffer();

    commit();
};

})();