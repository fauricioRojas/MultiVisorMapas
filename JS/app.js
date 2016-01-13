(function () {
    'use strict';

    angular
    .module('app', ['ngRoute', 'FBAngular'])
    .config(config)
    .controller('visorCtrl', function ($scope, $http, $location, Fullscreen) {
        $scope.size = 'x=500&y=400';
        $scope.despX = 0.0;
        $scope.despY = 0.0;
        $scope.zoom = 0.0;
        $scope.mapas;

        $scope.goFullscreen = goFullscreen;
        $scope.generateImage = generateImage;
        $scope.showHideMap = showHideMap;
        $scope.changeMapState = changeMapState;
        $scope.removeMap = removeMap;
        $scope.resetIdToMap = resetIdToMap;
        $scope.sortMap = sortMap;
        $scope.displacement = displacement;
        $scope.zoomMap = zoomMap;
        $scope.generateColor = generateColor;
        $scope.delayForLayers = delayForLayers;
        
        /**
         * Esta funcion se encarga de llevar a cabo el fullscreen del visor.
         * @returns {undefined}
         */
        function goFullscreen() {
            if (Fullscreen.isEnabled())
                Fullscreen.cancel();
            else
                Fullscreen.all();
        }

        /**
         * Esta funcion se encarga de generar para las capas visibibles los sectores.
         * @returns {undefined}
         */
        function generateImage() {
            if($location.path() === '/image') {
                angular.forEach($scope.mapas, function (value, key) {
                    if (value.state) {
                        value.image = './image/imagen.php?type='+value.type+'&'+crearStrConn()+'&schema='+value.schema+'&table='+value.table+'&column='+value.column+'&srid='+value.srid+'&'+$scope.size+'&r='+value.color.slice(0, 3)+'&g='+value.color.slice(4, 7)+'&b='+value.color.slice(8, 11)+'&trans='+value.transparency+'&zoom='+$scope.zoom+'&despX='+$scope.despX+'&despY='+$scope.despY; 
                    }
                });
            }
            else if($location.path() === '/canvas') {
                angular.forEach($scope.mapas, function (value, key) {
                    if(value.state) {
                        if(value.type === 'LINESTRING' || value.type === 'MULTILINESTRING') {
                            drawMultiLineString(value);
                        }
                        else if(value.type === 'POINT' || value.type === 'MULTIPOINT') {
                            drawPoint(value);
                        }
                        else if (value.type === 'POLYGON' || value.type === 'MULTIPOLYGON') {
                            drawMultiPolygon(value);
                        }
                    }
                });
            }
        }

        /**
         * Esta funcion se encarga de mostrar u ocultar una capa.
         * @param {type} id: Id de la capa.
         * @returns {undefined}
         */
        function showHideMap(id) {
            changeMapState(id);
            generateImage();
        }

        /**
         * Esta funcion se encarga de cambiar el estado de una capa.
         * @param {type} id: Id de la capa.
         * @returns {undefined}
         */
        function changeMapState(id) {
            angular.forEach($scope.mapas, function (value, key) {
                if (id === value.id) {
                    value.state = !value.state;
                }
            });
        }

        /**
         * Esta funcion elimina una capa del JSON.
         * @param {type} mapa: Capa.
         * @returns {undefined}
         */
        function removeMap(mapa) {
            $scope.mapas = $scope.mapas.filter(function (item) {
                return mapa !== item;
            });
        }

        /**
         * Esta funcion se encarga de resetear los id de todas las capas.
         * @returns {undefined}
         */
        function resetIdToMap() {
            var i = 0;
            angular.forEach($scope.mapas, function (value, key) {
                value.id = i;
                i++;
            });
        }

        /**
         * Esta funcion se encarga de ordenar las capas.
         * @param {type} action: Accion a realizar.
         * @param {type} id: Id de la capa.
         * @param {type} mapa: Capa.
         * @returns {undefined}
         */
        function sortMap(action, id, mapa) {
            if (action === 'up') {
                if (id !== 0) {
                    removeMap(mapa);
                    $scope.mapas.splice(id - 1, 0, mapa);
                    resetIdToMap();
                }
            }
            else {
                if (id !== $scope.mapas.length - 1) {
                    removeMap(mapa);
                    $scope.mapas.splice(id + 1, 0, mapa);
                    resetIdToMap();
                }
            }
        }

        /**
         * Esta funcion se encarga del desplazamiento de las capas.
         * @param {type} way: Forma del desplazamiento.
         * @returns {undefined}
         */
        function displacement(way) {
            if (way === 'up') {
                $scope.despY = $scope.despY - 0.1;
            }
            else if (way === 'left') {
                $scope.despX += 0.1;
            }
            else if (way === 'right') {
                $scope.despX = $scope.despX - 0.1;
            }
            else if (way === 'down') {
                $scope.despY += 0.1;
            }
            else {
                $scope.despX = 0.0;
                $scope.despY = 0.0;
            }

            generateImage();
        }

        /**
         * Esta funcion se encarga de hacer el zoom a la capa.
         * @param {type} mapa: Capa.
         * @returns {undefined}
         */
        function zoomMap(mapa) {
            $scope.zoom = -0.1;
            generateImage();
            removeMap(mapa);
            $scope.mapas.splice($scope.mapas.length, 0, mapa);
            resetIdToMap();
        }
        
        
        
        
        function generateColor() {
            var letters = '0123456789'.split('');
            var color = '';
            var number;
            for (var i = 0; i < 3; i++) {
                number = '';
                for (var j = 0; j < 3; j++) {
                    number += letters[Math.floor(Math.random() * 10)];
                }
                if(parseInt(number) <= 255)
                    color += number+',';
                else
                    i-=1;
            }
            
            return color.slice(0, color.length-1);
        }
        
        /// --------------------------------------- CANVAS
        $scope.drawMultiPolygon = drawMultiPolygon;
        $scope.drawPoint = drawPoint;
        $scope.drawLine = drawLine;
        $scope.drawMultiLineString = drawMultiLineString;
            
        CanvasRenderingContext2D.prototype.drawPolygon = function (Json, fillColor, strokeColor) 
        {
            for(var j = 0; j < Json.length; j++) {
                this.moveTo(Json[j].puntos[0].x, Json[j].puntos[0].y);

                for (var i = 0; i < Json[j].puntos.length; i++) {
                    this.lineTo(Json[j].puntos[i].x, Json[j].puntos[i].y);
                }
            }                

            if (strokeColor != null && strokeColor != undefined)
                this.strokeStyle = strokeColor;

            if (fillColor != null && fillColor != undefined) {
                this.fillStyle = fillColor;
                this.fill();
            }
        };
        
        function drawMultiPolygon(capa)
        {
            var canvas = document.getElementById(capa.table+"_"+capa.column);
            var context = canvas.getContext('2d');
            
            if(capa.puntos === null) {
                $http.get('../PHP/getJson.php?type='+capa.type+'&schema='+capa.schema+'&table='+capa.table+'&column='+capa.column+'&srid='+capa.srid+'&'+$scope.size+'&zoom='+$scope.zoom+'&despX='+$scope.despX+'&despY='+$scope.despY)
                .success(function(response) {
                    capa.puntos = response;
                    context.drawPolygon(capa.puntos, 'rgb('+capa.color+')', 'rgb('+capa.color+')');
                });
            }
            else {
                context.drawPolygon(capa.puntos, 'rgb('+capa.color+')', 'rgb('+capa.color+')');
            }
        }
        
        function runJsonPoint(capa) 
        {
            var canvas = document.getElementById(capa.table+"_"+capa.column);
            var context = canvas.getContext('2d');
            
            for(var i = 0; i < capa.puntos.length; i++)
            {
                for (var j = 0; j < capa.puntos[i].puntos.length; j++) 
                {
                    context.beginPath();
                    context.arc(capa.puntos[i].puntos[j].x, capa.puntos[i].puntos[j].y, 2, 0, 2*Math.PI);
                    context.fillStyle = 'rgb('+capa.color+')';
                    context.fill();
                }
            }
        }
        
        function drawPoint(capa){            
            if(capa.puntos === null) {
                console.log("cargo nueva po capa");
                $http.get('../PHP/getJson.php?type='+capa.type+'&schema='+capa.schema+'&table='+capa.table+'&column='+capa.column+'&srid='+capa.srid+'&'+$scope.size+'&zoom='+$scope.zoom+'&despX='+$scope.despX+'&despY='+$scope.despY)
                .success(function(response) {
                    capa.puntos = response;
                    runJsonPoint(capa);
                });
            }
            else {
                console.log("cargo po capa");
                runJsonPoint(capa);
            }
        }

        function drawLine(table, color, xi, yi, xf, yf) {
            var canvas = document.getElementById(table);
            console.log(canvas);
            var ctx = canvas.getContext('2d');
            ctx.beginPath();
            ctx.moveTo(xi, yi);
            ctx.lineTo(xf, yf);
            ctx.strokeStyle = 'rgb('+color+')';
            ctx.stroke();
        }
        
        function runJsonMultiLineString(capa) {
            for(var i = 0; i < capa.puntos.length; i++) {
                for (var j = 0; j < capa.puntos[i].puntosx.length-1; j++)  {
                    drawLine(capa.table+"_"+capa.column, capa.color, capa.puntos[i].puntosx[j], capa.puntos[i].puntosy[j], capa.puntos[i].puntosx[j+1], capa.puntos[i].puntosy[j+1]);
                }
            }
        }
        
        function drawMultiLineString(capa) {
            if(capa.puntos === null){
                console.log("cargo nueva l capa");
                $http.get('../PHP/getJson.php?type='+capa.type+'&schema='+capa.schema+'&table='+capa.table+'&column='+capa.column+'&srid='+capa.srid+'&'+$scope.size+'&zoom='+$scope.zoom+'&despX='+$scope.despX+'&despY='+$scope.despY)
                .success(function(response) {
                    capa.puntos = response;
                    runJsonMultiLineString(capa);
                });
            }
            else {
                console.log("cargo l capa");
                runJsonMultiLineString(capa);
            }
        }
        
        function delayForLayers() {
            setTimeout(function() {
                generateImage();
            }, 500);
        }
        
        
        
        
        /// --------------------------------------- PROBAR CONEXION
        $scope.strconn = {
            host: 'localhost',
            port: '5432',
            dbname: 'cursoGIS',
            user: 'postgres',
            password: '12345'
        };
        $scope.connectionStatus = {
            status: false,
            show: false
        }; 
        $scope.probarConexion = probarConexion;
        $scope.crearStrConn = crearStrConn;
        $scope.makeConnection = makeConnection;
        $scope.addAttributes = addAttributes;
        
        function probarConexion() {
            $http.get('./PHP/probarConexion.php?'+crearStrConn())
            .success(function(response) {   
                $scope.connectionStatus.show = true;
                if(response.status === 'ok') {
                    $scope.connectionStatus.status = true;
                }
                else {
                    $scope.connectionStatus.status = false;
                }
            });
        }
        
        function crearStrConn() {
            return 'strconn=host='+$scope.strconn.host+' port='+$scope.strconn.port+' dbname='+$scope.strconn.dbname+' user='+$scope.strconn.user+' password='+$scope.strconn.password;
        }
        
        function makeConnection() {
            $http.get('./PHP/getTables.php?'+crearStrConn())
            .success(function(response) {
                $scope.mapas = response;
                addAttributes();
            });
            $location.path('/image');
        };
        
        function addAttributes() {
            var i = 0;
            angular.forEach($scope.mapas, function (value, key) {
                value.id =  i;
                value.state = false;
                value.color = generateColor();
                value.transparency = 1;
                value.puntos = null;
                
                if($location.path() === '/image')
                    value.image = '';
                
                i++;
            });
        }
        
        /// --------------------------------------- MODO DEL VISOR
        $scope.nav = {};
        $scope.nav.isActive = function (path) {
            if (path === $location.path()) {
                return true;
            }

            return false;
        };
    });

    function config($routeProvider) {
        $routeProvider
            .when('/image', {
                controller: 'visorCtrl',
                templateUrl: './image/image.html'
            })
            .when('/canvas', {
                controller: 'visorCtrl',
                templateUrl: './canvas/canvas.html'
            })
            .when('/svg', {
                controller: 'visorCtrl',
                templateUrl: './svg/svg.html'
            });
    }
})();    
