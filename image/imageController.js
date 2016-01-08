(function(){
    'use strict';
    
    angular
        .module('app')
        .controller("imageCtrl", function($scope, Fullscreen) {        
        $scope.imageSize = 'x=500&y=400';
        $scope.rowsColumns = '3';
        $scope.despX = 0.0;
        $scope.despY = 0.0;
        $scope.zoom = 0.0;
        $scope.mapas = [
            {
                id: 0,
                state: false,
                text: 'Distritos',
                color: '0, 178, 48',
                transparency: 10,
                type: 'type=Polygon',
                image: []
            },
            {
                id: 1,
                state: false,
                text: 'Rios',
                color: '30, 115, 190',
                transparency: 10,
                type: 'type=Line',
                image: []
            },
            {
                id: 2,
                state: false,
                text: 'Caminos',
                color: '229, 0, 0',
                transparency: 10,
                type: 'type=Line',
                image: []
            },
            {
                id: 3,
                state: false,
                text: 'Escuelas',
                color: '242, 117, 7',
                transparency: 10,
                type: 'type=Point',
                image: []
            },
            {
                id: 4,
                state: false,
                text: 'Hospitales',
                color: '191, 48, 153',
                transparency: 10,
                type: 'type=Point',
                image: []
            }
        ];
        
        $scope.goFullscreen = goFullscreen;        
        $scope.generateSubImage = generateSubImage;
        $scope.generateImage = generateImage;
        $scope.showHideMap = showHideMap;
        $scope.changeMapState = changeMapState;
        $scope.removeMap = removeMap;
        $scope.resetIdToMap = resetIdToMap;
        $scope.sortMap = sortMap;
        $scope.displacement = displacement;
        $scope.zoomMap = zoomMap;
        
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
         * Esta funcion se encarga de generar cada uno de los sectores de la imagen de una capa.
         * @param {type} capa: Capa a la cual se generan los sectores.
         * @returns {undefined}
         */
        function generateSubImage(capa) {
            capa.image = [];
            var row = {};
            for(var i=0; i<$scope.rowsColumns; i++) {
                for(var j=0; j<$scope.rowsColumns; j++) {
                    row[j] = {piece:'./PHP/imagen.php?'+capa.type+'&trans='+capa.transparency+'&capa='+capa.text+'&rowsColumns='+$scope.rowsColumns+'&'+$scope.imageSize+'&zoom='+$scope.zoom+'&despX='+$scope.despX+'&despY='+$scope.despY+'&i='+i+'&j='+j};
                }
                capa.image.push(row);
                row = {};
            }
        }
        
        /**
         * Esta funcion se encarga de generar para las capas visibibles los sectores.
         * @returns {undefined}
         */
        function generateImage() {
            angular.forEach($scope.mapas, function(value, key){
                if(value.state) {
                    generateSubImage(value);
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
            angular.forEach($scope.mapas, function(value, key){
                if(id === value.id) {
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
            $scope.mapas = $scope.mapas.filter(function(item) {
                return mapa !== item;
            });
        }
        
        /**
         * Esta funcion se encarga de resetear los id de todas las capas.
         * @returns {undefined}
         */
        function resetIdToMap() {
            var i = 0;
            angular.forEach($scope.mapas, function(value, key){
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
            if(action === 'up') {
                if(id !== 0) {
                    removeMap(mapa);
                    $scope.mapas.splice(id-1, 0, mapa);
                    resetIdToMap();
                }
            }
            else {
                if(id !== $scope.mapas.length-1) {
                    removeMap(mapa);
                    $scope.mapas.splice(id+1, 0, mapa);
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
            if(way === 'up') {
                $scope.despY = $scope.despY - 0.1;
            }
            else if(way === 'left') {
                $scope.despX += 0.1;
            }
            else if(way === 'right') {
                $scope.despX = $scope.despX - 0.1;
            }
            else if(way === 'down') {
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
    });
})();