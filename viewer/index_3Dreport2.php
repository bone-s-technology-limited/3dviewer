<!DOCTYPE html>
<html lang="en">
  <head>
  <title>three.js webgl - loaders - vtk loader</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
  <link rel="stylesheet" href="//apps.bdimg.com/libs/jqueryui/1.10.4/css/jquery-ui.min.css">
  <script src="//apps.bdimg.com/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//apps.bdimg.com/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
  <style>
    body {
      font-family: Monospace;
      background-color: #000;
      color: #fff;
      margin: 0px;
      overflow: hidden;
    }


    .dg.main.a {
      width: 20% !important;
    }
    #inset  {
      width: 150px;
      height: 150px;
      background-color: transparent; /* or transparent; will show through only if renderer alpha: true */
      border: none; /* or none; */
      margin: 0;
      padding: 0px;
      position: absolute;
      left: 10px;
      bottom: 10px;
      /*z-index: 100;*/
    }

    #stl_model {
      width: auto;
      height: auto;
      /*border-left: 2px inset #ccc;*/
      /*border-bottom: 2px inset #ccc;*/
      background-color: transparent; /* or transparent; will show through only if renderer alpha: true */
      /*border: none;  or none; */
      margin: 0;
      padding: 0px;
      position: absolute;
      bottom: 28%;
      right: 25%;
      z-index: 100;
    }
    
  </style>

  <script type="text/javascript">
  </script>

</head>

<body>

  
  <div id="inset"></div>
  <!-- <div id="screen1"></div>
  <div id="screen2"></div>
  <div id="screen3"></div> -->
  <div id="stl_model"></div>
  

  <script src="js/three.js"></script>

  <script src="js/controls/TrackballControls.js"></script>

  <script src="js/Volume.js"></script>
  <script src="js/VolumeSlice.js"></script>
  <script src="js/loaders/NRRDLoader.js"></script>
  <script src="js/loaders/VTKLoader.js"></script>
  <script src="js/old/STLLoader.js"></script>


  <script src="js/Detector.js"></script>
  <script src="js/libs/stats.min.js"></script>
  <script src="js/libs/zlib_and_gzip.min.js"></script>
  <script src="js/libs/dat.gui.min.js"></script>
  

  <script>

    if ( ! Detector.webgl ) Detector.addGetWebGLMessage();

    var container,
      stats,
      camera,
      controls,
      scene,
      renderer,
      gui,
      // scene1,
      // renderer1,
      // camera1,
      container2,
      renderer2,
      camera2,
      // camera3,
      axes2,
      scene2;
      // scene3;
    //var param = getQueryVariable("name");
    
    var param = '../' + <?php echo json_encode($_POST['path']) ?>;
    //var param = '../X7654321/2019-11-21' ;

    // var isChecked = document.getElementById("check").checked;
    // console.log('check', isChecked)
    initStl();
    // initNrrd();
    animate();
    var gui = new dat.GUI();

    function getQueryVariable(variable){
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
      }

    function initStl() {

      camera = new THREE.PerspectiveCamera( 12, window.innerWidth / window.innerHeight, 0.01, 1e10 );
      camera.position.z = 300;



      scene = new THREE.Scene();

      scene.add( camera );

      // light

      var dirLight = new THREE.DirectionalLight( 0xffffff );
      dirLight.position.set( 200, 200, 1000 ).normalize();

      camera.add( dirLight );
      camera.add( dirLight.target );

      var transversePlane = new THREE.Plane( new THREE.Vector3( 0, - 1, 0 ), 20 );
      var coronalPlane = new THREE.Plane( new THREE.Vector3( - 1, 0, 0 ), 30 );
	  var sagittalPlane = new THREE.Plane( new THREE.Vector3( 0, 0, -1 ), 30 );
      

      var stlloader = new THREE.STLLoader();
      var material = new THREE.MeshPhongMaterial({
        color:'rgb(190,238,248)',
        //transparent:true,
        transparent:false,
        //opacity:0.4,
        opacity:0.7,
        //what is depthWrite???
        //depthWrite: false,
        depthWrite: true,
        //clippingPlanes: [ transversePlane ]
      });
      stlloader.load( param +'/Model1.stl', function ( geometry ) {
        var mesh = new THREE.Mesh( geometry, material );

        mesh.position.set( 20, -12, 20);
        mesh.rotation.set( 0, 0, 0);
        mesh.rotateY( Math.PI / 2 );
        mesh.rotateX( Math.PI * 3/ 2 );

        // mesh.rotation.set(new THREE.Vector3( 0, 0, 0));
        mesh.scale.set( 0.7, 0.7, 0.7 );

        mesh.castShadow = true;
        mesh.receiveShadow = true;

        scene.add( mesh );
        var visibilityControl = {
          visible : true
        };
        gui.add(visibilityControl, "visible").name( "Model Visible").onChange( function () {
          mesh.visible = visibilityControl.visible;
          renderer.render( scene, camera );
        });
        
                    // Local Clipping 
                    /*var folderLocal = gui.addFolder( 'Transverse Clipping' ),
					propsLocal = {
						get 'Plane'() {

							return transversePlane.constant;

						},
						set 'Plane'( v ) {
                            renderer.localClippingEnabled = v
							transversePlane.constant = v;

						}

					},
                    */

					var folderGlobal = gui.addFolder( 'Coronal Clipping' ),
					propsGlobal = {
						get 'Plane'() {

						    return coronalPlane.constant;

						},
						set 'Plane'( v ) {
                            renderer.clippingPlanes = v ? globalPlanes : Empty;
							coronalPlane.constant = v;

                        }
                    },

                    folderGlobal1 = gui.addFolder( 'Sagittal Clipping' ),
                    propsGlobal1 = {
                        get 'Plane'() {

                            return sagittalPlane.constant;

                        },
                        set 'Plane'( v ) {
                        //renderer.clippingPlanes = v ? globalPlanes1 : Empty;
                            renderer.clippingPlanes = v ? globalPlanes : Empty;
                            sagittalPlane.constant = v;

                        },
                    },

                    folderGlobal2 = gui.addFolder( 'Transverse Clipping' ),
					propsGlobal2 = {
						get 'Plane'() {

						    return transversePlane.constant;

						},
						set 'Plane'( v ) {
                            renderer.clippingPlanes = v ? globalPlanes : Empty;
							transversePlane.constant = v;

                        }

                    };


				// folderLocal.add( propsLocal, 'Enabled' );
				// folderLocal.add( propsLocal, 'Shadows' );
				//folderLocal.add( propsLocal, 'Plane', -20, 20 );

				// folderGlobal.add( propsGlobal, 'Enabled' );
                folderGlobal.add( propsGlobal, 'Plane', -20, 30 );
				folderGlobal1.add( propsGlobal1, 'Plane', -20, 30 );
                folderGlobal2.add( propsGlobal2, 'Plane', -20, 20 );
        


      });
      // renderer

      var material2 = new THREE.MeshPhongMaterial({color:'rgb(255, 50, 50)',transparent:true,opacity:0.5,depthWrite: true});
      stlloader.load(param +'/Model2.stl',function(geometry){
        var mesh = new THREE.Mesh(geometry,material2);
        mesh.position.set( 20, -12, 20);
        mesh.rotation.set( 0, 0, 0);
        mesh.rotateY( Math.PI / 2 );
        mesh.rotateX( Math.PI * 3/ 2 );

        // mesh.rotation.set(new THREE.Vector3( 0, 0, 0));
        mesh.scale.set( 0.7, 0.7, 0.7 );

        mesh.castShadow = true;
        mesh.receiveShadow = true;
        scene.add(mesh);
      }); 


      var loader = new THREE.NRRDLoader();
      //var nrrdPath = param +'/model.nrrd';
      var nrrdPath = param +'/data.nrrd';

      console.log(nrrdPath)
      loader.load( nrrdPath, function ( volume ) {
        var geometry,
          canvas,
          canvasMap,
          material,
          plane,
          sliceZ,
          sliceY,
          sliceX;

        

        // box helper to see the extend of the volume
        var geometry = new THREE.BoxGeometry( volume.xLength, volume.yLength, volume.zLength );
        var material = new THREE.MeshBasicMaterial( {color: 0x00ff00} );
        var cube = new THREE.Mesh( geometry, material );
        //cube.scale.set( 0.5, 0.5, 0.5 )
        cube.visible = false;
        

        var box = new THREE.BoxHelper( cube );
        scene.add( box );
        box.applyMatrix(volume.matrix);
        scene.add( cube );


        //z plane

        var indexZ = 0;
        sliceZ = volume.extractSlice('z',Math.floor(volume.RASDimensions[2]/4));
        // scene1.add( sliceZ.mesh );
        scene.add(sliceZ.mesh)

        //y plane
        var indexY = 0;
        sliceY = volume.extractSlice('y',Math.floor(volume.RASDimensions[1]/2));
        // scene1.add( sliceY.mesh );
        // sliceY.mesh.rotateX( Math.PI / 2 ).rotateZ( Math.PI / 2 );
        scene.add(sliceY.mesh)


        //x plane
        var indexX = 0;
        sliceX = volume.extractSlice('x',Math.floor(volume.RASDimensions[0]/2));
        scene.add( sliceX.mesh );


        gui.add( sliceX, "index", 0, volume.RASDimensions[0], 1 ).name( "indexX" ).onChange( function () {sliceX.repaint.call(sliceX);} );
        gui.add( sliceY, "index", 0, volume.RASDimensions[1], 1 ).name( "indexY" ).onChange( function () {sliceY.repaint.call(sliceY);} );
        gui.add( sliceZ, "index", 0, volume.RASDimensions[2], 1 ).name( "indexZ" ).onChange( function () {sliceZ.repaint.call(sliceZ);} );

        gui.add( volume, "lowerThreshold", volume.min, volume.max, 1).name( "Lower Threshold").onChange( function () {
          volume.repaintAllSlices();
        });
        gui.add( volume, "upperThreshold", volume.min, volume.max, 1).name( "Upper Threshold").onChange( function () {
          volume.repaintAllSlices();
        });
        gui.add( volume, "windowLow", volume.min, volume.max, 1).name( "Window Low").onChange( function () {
          volume.repaintAllSlices();
        });
        gui.add( volume, "windowHigh", volume.min, volume.max, 1).name( "Window High").onChange( function () {
          volume.repaintAllSlices();
        });

      });


      renderer = new THREE.WebGLRenderer( { antialias: false, alpha: true } );
      renderer.setPixelRatio( window.devicePixelRatio );
      renderer.setSize( window.innerWidth/5*2, window.innerHeight/2 );

      // ***** Clipping setup (renderer): *****
      var globalPlanes = [ coronalPlane, sagittalPlane, transversePlane  ],
        //globalPlanes1 = [ sagittalPlane ],
			  Empty = Object.freeze( [] );
			//renderer.clippingPlanes = Empty; // GUI sets it to globalPlanes
			//renderer.localClippingEnabled = true;
      renderer.localClippingEnabled = false;

       container = document.getElementById('stl_model');
      // container = document.createElement('div')
      document.body.appendChild( container );
      container.appendChild( renderer.domElement );

      controls = new THREE.TrackballControls( camera, renderer.domElement );
      controls.rotateSpeed = 5.0;
      controls.zoomSpeed = 5;
      controls.panSpeed = 2;
      controls.noZoom = false;
      controls.noPan = false;
      controls.staticMoving = true;
      controls.dynamicDampingFactor = 0.3;

     
      

      

      stats = new Stats();
      container.appendChild( stats.dom );

      

      setupInset();

      window.addEventListener( 'resize', onWindowResize, false );

    }

    function onWindowResize() {

      camera.aspect = window.innerWidth / window.innerHeight;
      camera.updateProjectionMatrix();

      renderer.setSize( window.innerWidth/5*2, window.innerHeight/2 );

      controls.handleResize();

    }

    function animate() {

      requestAnimationFrame( animate );

      controls.update();

      // copy position of the camera into inset
      camera2.position.copy( camera.position );
      camera2.position.sub( controls.target );
      camera2.position.setLength( 400 );
      camera2.lookAt( scene2.position );

      // camera1.position.copy( camera.position );
      // camera1.position.sub( controls.target );
      // camera1.position.setLength( 400 );
      // camera1.lookAt( scene1.position );

      // camera3.position.copy( camera.position );
      // camera3.position.sub( controls.target );
      // camera3.position.setLength( 400 );
      // camera3.lookAt( scene3.position );

      // camera4.position.copy( camera.position );
      // camera4.position.sub( controls.target );
      // camera4.position.setLength( 400 );
      // camera4.lookAt( scene4.position );

      renderer.render( scene, camera );
      // renderer1.render( scene1, camera1);
      renderer2.render( scene2, camera2);
      // renderer3.render( scene3, camera3);
      // renderer4.render( scene4, camera4);
      // console.log('ssssss', camera4)


      stats.update();

    }
    

    // function initNrrd() {

    //   camera1 = new THREE.PerspectiveCamera( 10, window.innerWidth / window.innerHeight, 0.01, 1e10 );
    //   camera1.position.z = 300;

    //   camera3 = new THREE.PerspectiveCamera( 10, window.innerWidth / window.innerHeight, 0.01, 1e10 );
    //   camera3.position.z = 300;
    //   // camera3.position.y = 50;
    //   // camera3.rotation.set( -0.03, 0, 0.08)
    //   // camera3.rotateY( Math.PI / 2 )

    


    //   camera4 = new THREE.PerspectiveCamera( 10, window.innerWidth / window.innerHeight, 0.01, 1e10 );
    //   camera4.position.z = 300;
    //   // camera4.rotateZ( Math.PI / 2 )
    //   // camera4.position.y = 100;



    //   console.log(param)



    //   scene1 = new THREE.Scene();

    //   scene1.add( camera1 );

    //   scene3 = new THREE.Scene();

    //   scene3.add( camera3 );
     


    //   scene4 = new THREE.Scene();

    //   scene4.add( camera4 );

    //   // light

    //   var dirLight1 = new THREE.DirectionalLight( 0xffffff );
    //   dirLight1.position.set( 200, 200, 1000 ).normalize();

    //   camera1.add( dirLight1 );
    //   camera1.add( dirLight1.target);

    //   var loader = new THREE.NRRDLoader();
    //   var nrrdPath = param +'/model.nrrd'
    //   console.log(nrrdPath)
    //   loader.load( nrrdPath, function ( volume ) {
    //     var geometry,
    //       canvas,
    //       canvasMap,
    //       material,
    //       plane,
    //       sliceZ,
    //       sliceY,
    //       sliceX;

    //     //box helper to see the extend of the volume
    //     // var geometry = new THREE.BoxGeometry( volume.xLength, volume.yLength, volume.zLength );
    //     // var material = new THREE.MeshBasicMaterial( {color: 0x00ff00} );
    //     // var cube = new THREE.Mesh( geometry, material );
    //     // cube.visible = false;
    //     // var box = new THREE.BoxHelper( cube );
    //     // scene1.add( box );
    //     // box.applyMatrix(volume.matrix);
    //     // scene1.add( cube );

    //     //z plane

    //     var indexZ = 0;
    //     sliceZ = volume.extractSlice('z',Math.floor(volume.RASDimensions[2]/4));

    //     console.log(volume)
    //     console.log(sliceZ)
    //     // scene1.add( sliceZ.mesh );
    //     scene3.add(sliceZ.mesh)

    //     //y plane
    //     var indexY = 0;
    //     sliceY = volume.extractSlice('y',Math.floor(volume.RASDimensions[1]/2));
    //     // scene1.add( sliceY.mesh );
    //     // sliceY.mesh.rotateX( Math.PI / 2 ).rotateZ( Math.PI / 2 );
    //     sliceY.mesh;
    //     scene4.add(sliceY.mesh)


    //     //x plane
    //     var indexX = 0;
    //     sliceX = volume.extractSlice('x',Math.floor(volume.RASDimensions[0]/2));
    //     scene1.add( sliceX.mesh );

    //     gui.add( sliceX, "index", 0, volume.RASDimensions[0], 1 ).name( "indexX" ).onChange( function () {sliceX.repaint.call(sliceX);} );
    //     gui.add( sliceY, "index", 0, volume.RASDimensions[1], 1 ).name( "indexY" ).onChange( function () {sliceY.repaint.call(sliceY);} );
    //     gui.add( sliceZ, "index", 0, volume.RASDimensions[2], 1 ).name( "indexZ" ).onChange( function () {sliceZ.repaint.call(sliceZ);} );

    //     gui.add( volume, "lowerThreshold", volume.min, volume.max, 1).name( "Lower Threshold").onChange( function () {
    //       volume.repaintAllSlices();
    //     });
    //     gui.add( volume, "upperThreshold", volume.min, volume.max, 1).name( "Upper Threshold").onChange( function () {
    //       volume.repaintAllSlices();
    //     });
    //     gui.add( volume, "windowLow", volume.min, volume.max, 1).name( "Window Low").onChange( function () {
    //       volume.repaintAllSlices();
    //     });
    //     gui.add( volume, "windowHigh", volume.min, volume.max, 1).name( "Window High").onChange( function () {
    //       volume.repaintAllSlices();
    //     });

    // } );

// renderer

    // renderer1 = new THREE.WebGLRenderer( { antialias: false, alpha: true } );
    // renderer1.setPixelRatio( window.devicePixelRatio );
    // renderer1.setSize( window.innerWidth/5*2, window.innerHeight/2 );

    // container1 = document.getElementById('screen1');
    // container1.appendChild( renderer1.domElement );

    // renderer3 = new THREE.WebGLRenderer( { antialias: false, alpha: true } );
    // renderer3.setPixelRatio( window.devicePixelRatio );
    // renderer3.setSize( window.innerWidth/5*2, window.innerHeight/2 );

    // container3 = document.getElementById('screen2');
    // container3.appendChild( renderer3.domElement );

    // renderer4 = new THREE.WebGLRenderer( { antialias: false, alpha: true } );
    // renderer4.setPixelRatio( window.devicePixelRatio );
    // renderer4.setSize( window.innerWidth/5*2, window.innerHeight/2 );

    // container4 = document.getElementById('screen3');
    // container4.appendChild( renderer4.domElement );

// }


    function setupInset () {
      var insetWidth = 150,
        insetHeight = 150;
      container2 = document.getElementById('inset');
      container2.width = insetWidth;
      container2.height = insetHeight;

      // renderer
      renderer2 = new THREE.WebGLRenderer({alpha : true});
      renderer2.setClearColor( 0x000000, 0 );
      renderer2.setSize( insetWidth, insetHeight );
      container2.appendChild( renderer2.domElement );

      // scene
      scene2 = new THREE.Scene();

      // camera
      camera2 = new THREE.PerspectiveCamera( 50, insetWidth / insetHeight, 1, 1000 );
      camera2.up = camera.up; // important!

      // axes
      axes2 = new THREE.AxisHelper( 100 );
      scene2.add( axes2 );
    }


    


  </script>
 </body>
</html>