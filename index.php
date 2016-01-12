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
                        <h3><span class="glyphicon glyphicon-list"></span> Seleccione las capas a mostrar</h3>
                    </div>
                    <div class="modal-body">
                        <div style="overflow-y:auto; height:450px">
                            <table class="table table-striped">
                                <thead>
                                    <tr>          
                                        <th></th>
                                        <th>Schema</th>
                                        <th>Capa</th>
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
        
        <div ng-view></div>

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
            
            $('body').keypress(function(e){
                if(e.which === 99){
                    document.getElementById('modalShow').click();
                }
                else if(e.which === 109){
                    document.getElementById('modalLayers').click();
                }
            });
        </script>
    </body>
</html>