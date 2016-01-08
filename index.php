<!DOCTYPE html>
<html ng-app="app">
    <head>
        <meta charset="UTF-8">
        <title>Visor de mapas</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="shortcun icon" href="./icono/icono.ico">
    </head>
    <body>
        <div id="headerWrapper" ng-controller="headerCtrl">
            <div class="nav-wrapper pull-left">
                <ul class="nav nav-pills">
                    <li><h4><span class="label label-default"><b>Seleccione el modo de vista</b></span>&nbsp;&nbsp;&nbsp;</h4></li>
                    <li ng-class="{active: nav.isActive('/image')}"><a href="#/image">Image</a></li>
                    <li ng-class="{active: nav.isActive('/canvas')}"><a href="#/canvas">Canvas</a></li>
                    <li ng-class="{active: nav.isActive('/svg')}"><a href="#/svg">SVG</a></li>
                </ul>
            </div>
        </div>
        <div ng-view></div>
             
        <script src="JS/angular.min.js"></script>
        <script src="JS/angular-route.min.js"></script>
        <script src="JS/angular-fullscreen.js"></script>
        <script src="JS/app.js"></script>
        <script src="image/imageController.js"></script>
        <script src="canvas/canvasController.js"></script>
        <script src="svg/svgController.js"></script>
    </body>
</html>