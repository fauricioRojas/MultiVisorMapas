(function() {
    'use strict';

    angular
        .module('app', ['ngRoute', 'FBAngular'])
        .config(config)
        .controller('headerCtrl', function($scope, $location) {
            $scope.nav = {};
            $scope.nav.isActive = function(path) {
                if (path === $location.path()) {
                    return true;
                }

                return false;
            };
        });

    function config($routeProvider) {
        $routeProvider
                .when('/image', {
                    controller: 'imageCtrl',
                    templateUrl: './image/image.html'
                })
                .when('/canvas', {
                    controller: 'canvasCtrl',
                    templateUrl: './canvas/canvas.html'
                })
                .when('/svg', {
                    controller: 'svgCtrl',
                    templateUrl: './svg/svg.html'
                })
                .otherwise('/image');
    }
})();    