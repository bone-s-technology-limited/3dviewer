if ( ! Detector.webgl ) Detector.addGetWebGLMessage();
var camera, scene, renderer;

init();

function init() {

  scene = new THREE.Scene();
  scene.add( new THREE.AmbientLight( 0x999999 ) );

  camera = new THREE.PerspectiveCamera( 35, window.innerWidth / window.innerHeight, 1, 500 );

  // Z is up for objects intended to be 3D printed.

  camera.up.set( 0, 0, 1 );
  camera.position.set( 0, -9, 6 );

  camera.add( new THREE.PointLight( 0xffffff, 0.8 ) );

  scene.add( camera );

  var grid = new THREE.GridHelper( 25, 50, 0xffffff, 0x555555 );
  grid.rotateOnAxis( new THREE.Vector3( 1, 0, 0 ), 90 * ( Math.PI/180 ) );
  scene.add( grid );

  renderer = new THREE.WebGLRenderer( { antialias: true } );
  renderer.setClearColor( 0x999999 );
  renderer.setPixelRatio( window.devicePixelRatio );
  renderer.setSize( window.innerWidth, window.innerHeight );
  document.body.appendChild( renderer.domElement );

  var loader = new THREE.STLLoader();


  // Binary files
  var shader = THREE.FresnelShader;

  //uniforms[ "tCube" ].value = textureCube;

 // var uniforms = THREE.UniformsUtils.clone( shader.uniforms );

 // var material = new THREE.ShaderMaterial( {
  //    uniforms: uniforms,
    //  vertexShader: shader.vertexShader,
     // fragmentShader: shader.fragmentShader
    //  } );
  

 // var material = new THREE.MeshPhongMaterial( { color: 0x0e2045, specular: 0x111111, shininess: 100 } );
    var material = new THREE.MeshPhongMaterial({color:'rgb(175,238,238)',transparent:true,opacity:0.5,depthWrite: false});
    loader.load( './models/Model1.stl', function ( geometry ) {
    var mesh = new THREE.Mesh( geometry, material );

    mesh.position.set( 0, 0, 0 );
    mesh.rotation.set( 0, 0, 0 );
    mesh.scale.set( .1, .1, .1 );

    mesh.castShadow = true;
    mesh.receiveShadow = true;

    scene.add( mesh );
    render();
  });
    //
   var material2 = new THREE.MeshPhongMaterial({color:'rgb(255, 50, 50)',transparent:true,opacity:0.8,depthWrite: true});
   loader.load('./models/Model2.stl',function(geometry){
   var mesh = new THREE.Mesh(geometry,material2);
   mesh.position.set(0,0,0);
   mesh.rotation.set(0,0,0);
   mesh.scale.set(.1,.1,.1);
   mesh.castShadow = true;
   mesh.receiveShaow = true;
   scene.add(mesh);
   render();
  }); 

  var controls = new THREE.OrbitControls( camera, renderer.domElement );
  controls.addEventListener( 'change', render );
  controls.target.set( 0, 1.2, 2 );
  controls.update();
  window.addEventListener( 'resize', onWindowResize, false );

}

function onWindowResize() {

  camera.aspect = window.innerWidth / window.innerHeight;
  camera.updateProjectionMatrix();

  renderer.setSize( window.innerWidth, window.innerHeight );

  render();

}

function render() {

  renderer.render( scene, camera );

}
