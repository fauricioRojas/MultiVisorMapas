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
            angular.forEach($scope.mapas, function (value, key) {
                if (value.state) {
                    value.image = './image/imagen.php?type='+value.type+'&'+crearStrConn()+'&table='+value.schema+'.'+value.table+'&column='+value.column+'&srid='+value.srid+'&'+$scope.size+'&r='+value.color.slice(0, 3)+'&g='+value.color.slice(4, 7)+'&b='+value.color.slice(8, 11)+'&trans='+value.transparency+'&zoom='+$scope.zoom+'&despX='+$scope.despX+'&despY='+$scope.despY; 
                }
            });
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
            });
            
            setTimeout(addAttributes, 2000);
            
            $location.path('/image');
        };
        
        function addAttributes() {
            var i = 0;
            angular.forEach($scope.mapas, function (value, key) {
                value.id =  i;
                value.state = false;
                value.color = generateColor();
                value.transparency = 10;
                
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