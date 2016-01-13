<!DOCTYPE html>
<html ng-app="app">
    <head>
        <meta charset="UTF-8">
        <title>Visor de mapas</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="shortcun icon" href="./icono/icono.ico">
    </head>
    <body ng-controller="visorCtrl" onload="click()">        
        <button id="modalShow" class="btn" data-toggle="modal" data-target="#conexion" style="display: none;"></button>
        <div class="modal fade" id="conexion" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Conectar al servidor</h3>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <div class="form-group">
                                <label>Host</label>
                                <div>
                                    <input type="text" class="form-control" ng-model="strconn.host" placeholder="Host">
                                </div>
                            </div> 
                            <div class="form-group">
                                <label>Puerto</label>
                                <div>
                                    <input type="text" class="form-control" ng-model="strconn.port" placeholder="Puerto">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Base de datos</label>
                                <div>
                                    <input type="text" class="form-control" ng-model="strconn.dbname" placeholder="Nombre de la base de datos">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Usuario</label>
                                <div>
                                    <input type="text" class="form-control" ng-model="strconn.user" placeholder="Usuario">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Contraseña</label>
                                <div>
                                    <input type="password" class="form-control" ng-model="strconn.password" ng-blur="probarConexion()" placeholder="Contraseña">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-8 row">
                            <h4 class="text-left" ng-show="connectionStatus.show && connectionStatus.status"><span class="label label-success">La conexión se ha realizado correctamente.</span></h4>
                            <h4 class="text-left" ng-show="connectionStatus.show && !connectionStatus.status"><span class="label label-warning">No se puede conectar con el servidor.</span></h4>
                        </div>
                        <button class="btn btn-success" data-dismiss="modal" ng-disabled="!connectionStatus.status" ng-click="makeConnection()">
                            <span class="glyphicon glyphicon-circle-arrow-right"></span> Listo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="layers" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3><span><img src="icono/layer.png" height="25" width="25"></span> Seleccione las capas a mostrar</h3>
                    </div>
                    <div class="modal-body">
                        <div style="overflow-y:auto; height:450px">
                            <table class="table table-striped">
                                <thead>
                                    <tr>          
                                        <th></th>
                                        <th>Schema</th>
                                        <th>Capa</th>
                                        <th>Columna</th>
                                        <th>Proyección</th>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="mapa in mapas">
                                        <td><input class="hand" type="checkbox" ng-click="showHideMap(mapa.id)"></td>
                                        <td>{{mapa.schema}}</td>
                                        <td>{{mapa.table}}</td>
                                        <td>{{mapa.column}}</td>
                                        <td>{{mapa.proj}}</td>
                                        <td>{{mapa.name}}</td>
                                        <td>{{mapa.type}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8" ng-view></div>

        <div class="col-md-4  row">
            <div class="col-md-12">
                <div class="col-md-12" style="overflow-y:auto; height:188px;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th colspan="5"><span class="glyphicon glyphicon-th-list"></span> Capas mostradas</th>
                                <th title="Capas disponibles" colspan="1"><span class="hand" id="modalLayers" data-toggle="modal" data-target="#layers"><img src="icono/layer.png" height="25" width="25"></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="mapa in mapas" ng-if="mapa.state">
                                <td><img width="20" height="20" ng-if="mapa.type === 'MULTILINESTRING' || mapa.type === 'LINESTRING'" src="./icono/line.png">
                                    <img width="20" height="20" ng-if="mapa.type === 'MULTIPOLYGON' || mapa.type === 'POLYGON'" src="./icono/polygon.png">
                                    <img width="20" height="20" ng-if="mapa.type === 'MULTIPOINT' || mapa.type === 'POINT'" src="./icono/point.png"></td>
                                <td>{{mapa.table}}</td>
                                <td><center><span title="Zoom a la capa" class="glyphicon glyphicon-screenshot hand" ng-click="zoomMap(mapa)"></span></center></td>
                                <td><span style="float:left;" title="Subir capa" class="glyphicon glyphicon-chevron-up hand" ng-click="sortMap('up', mapa.id, mapa)"></span>
                                    <span style="float:left;" title="Bajar capa" class="glyphicon glyphicon-chevron-down hand" ng-click="sortMap('down', mapa.id, mapa)"></span></td>
                                <td><input title="Transparencia" type="range" min="0" max="1" step="0.1" value="mapa.transparency" ng-model="mapa.transparency"></td>
                                <td><div style="width: 15px; height: 25px; background-color: rgb({{mapa.color}});"></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th colspan="3"><span class="glyphicon glyphicon-cog"></span> Configuraciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <ul class="nav nav-pills">
                                    <li ng-click="delayForLayers()" ng-class="{active: nav.isActive('/image')}"><a href="#/image">Image</a></li>
                                </ul>
                            </td>
                            <td>
                                <ul class="nav nav-pills">
                                    <li ng-click="delayForLayers()" ng-class="{active: nav.isActive('/canvas')}"><a href="#/canvas">Canvas</a></li>
                                </ul>
                            </td>
                            <td>
                                <ul class="nav nav-pills">
                                    <li ng-click="delayForLayers()" ng-class="{active: nav.isActive('/svg')}"><a href="#/svg">SVG</a></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td>Tamaño</td>
                            <td colspan="2">
                                <select id="size" class="form-control hand" ng-model="size" ng-change="generateImage()">
                                    <option value="x=500&y=400">500x400</option>
                                    <option value="x=640&y=480">640x480</option>
                                    <option value="x=760&y=600">760x600</option>
                                    <option value="x=880&y=720">880x720</option>
                                    <option value="x=1024&y=768">1024x768</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Zoom</td>                                
                            <td colspan="2"><center><input class="hand" type="range" min="0" max="0.9" step="0.1" value="0" ng-model="zoom" ng-change="generateImage();"></center></td>
                    </tr>
                    <tr>
                        <td>Fullscreen</td>
                        <td colspan="2"><span class="glyphicon glyphicon-fullscreen hand" ng-click="goFullscreen()"></span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th colspan="3"><span class="glyphicon glyphicon-move"></span> Desplazamiento de las capas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td><center><span class="glyphicon glyphicon-triangle-top hand" ng-click="displacement('up');"></span></center></td>
                    <td></td>
                    </tr>
                    <tr>
                        <td><center><span class="glyphicon glyphicon-triangle-left hand" ng-click="displacement('left');"></span></center></td>
                    <td><center><span title="Resetear desplazamiento" class="glyphicon glyphicon-refresh hand" ng-click="displacement('reset');"></span></center></td>
                    <td><center><span class="glyphicon glyphicon-triangle-right hand" ng-click="displacement('right');"></span></center></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><center><span class="glyphicon glyphicon-triangle-bottom hand" ng-click="displacement('down');"></span></center></td>
                    <td></td>
                    </tr>                            
                    </tbody>
                </table>
            </div>
        </div>

        <script src="JS/angular.min.js"></script>
        <script src="JS/angular-route.min.js"></script>
        <script src="JS/angular-fullscreen.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-animate.js"></script>
        <script src="//angular-ui.github.io/bootstrap/ui-bootstrap-tpls-0.14.3.js"></script>
        <script src="https://code.jquery.com/jquery.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script src="JS/app.js"></script>

        <script>
            function click() {
                document.getElementById('modalShow').click();
            }

            $('body').keypress(function (e) {
                if (e.which === 99) {
                    document.getElementById('modalShow').click();
                }
                else if (e.which === 109) {
                    document.getElementById('modalLayers').click();
                }
            });
        </script>
    </body>
</html>