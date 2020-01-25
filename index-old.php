<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="ColombiaHosting">
    <title>Bienvenido a <?php echo preg_replace('/^www\./','',$_SERVER['HTTP_HOST']); ?></title>
    <link rel="icon" href="https://www.colombiahosting.com.co/images/minilogo.png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,100' rel='stylesheet' type='text/css'>
    <!-- Estilos -->
    <style>
        body {width: 100%; height: 100%; font-family: 'Roboto', sans-serif}
        html {width: 100%; height: 100%}
        .navbar-brand img {width: auto; height: 50px}
        .navbar-default {background: rgb(255, 255, 255); background: rgba(255, 255, 255, 0.3); border-color: transparent; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3)}
        .navbar-default .navbar-toggle {border-color: transparent}
        .navbar-default .navbar-collapse, .navbar-default .navbar-form {border-color: transparent}
        .navbar-default .navbar-toggle:focus, .navbar-default .navbar-toggle:hover {background-color: transparent}
        .navbar-default .navbar-nav > li > a {color: #333}
        .navbar-default .navbar-toggle .icon-bar {background-color: #333}
        .encabezado {display: table; position: relative; width: 100%; height: 100%; background: url("https://www.colombiahosting.com.co/imagenes/fondo_bienvenida.jpg") no-repeat center center scroll; background-size: cover}
        .texto-encabezado {display: table-cell; text-align: center; vertical-align: middle}
        .texto-encabezado h1 {margin: 0; padding: 0; font-size: 3em; color: #fff; text-transform: uppercase}
        #contenido {margin-top: -81px; position: absolute}
        .contenido-a {padding: 50px 0; background-color: #f2f2f2}
        .contenido-b {padding: 50px 0; background-color: #d9d9d9}
        .contenido-a img, .contenido-b img {border: solid 1px #ccc}
        .separador {border: 0; height: 1px; width: 70%; background-image: linear-gradient(to right, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0))}
        .btn-1 {border: none; background: none; cursor: pointer; padding: 10px 80px; display: inline-block; margin: 15px 30px; font-weight: bold; position: relative; -webkit-transition: all 0.3s; -moz-transition: all 0.3s; transition: all 0.3s}
        .btn-1:after {content: ''; position: absolute; z-index: -1; -webkit-transition: all 0.3s; -moz-transition: all 0.3s; transition: all 0.3s}
        .btn-2 {border: none; background: none; cursor: pointer; padding: 10px 80px; display: inline-block; font-weight: bold; position: relative; -webkit-transition: all 0.3s; -moz-transition: all 0.3s; transition: all 0.3s}
        .btn-2:after {content: ''; position: absolute; z-index: -1; -webkit-transition: all 0.3s; -moz-transition: all 0.3s; transition: all 0.3s}
        .btn-1a {border: 3px solid #ecbb17; color: #ecbb17}
        .btn-1b {border: 3px solid #00548b; color: #00548b}
        .btn-1c {border: 3px solid #ca1c22; color: #ca1c22}
        .btn-1d {border: 3px solid #fff; color: #fff}
        .btn-2a {border: 3px solid #7e7e7e; color: #7e7e7e; margin-bottom: 25px}
        .btn-1a:hover, .btn-1a:active {color: #fff; background: #ecbb17}
        .btn-1b:hover, .btn-1b:active {color: #fff; background: #00548b}
        .btn-1c:hover, .btn-1c:active {color: #fff; background: #ca1c22}
        .btn-1d:hover, .btn-1d:active {background: rgb(255, 255, 255); background: rgba(255, 255, 255, 0.3); border-color: transparent; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3)}
        .btn-2a:hover, .btn-2a:active {color: #fff; background: #7e7e7e}
        a.btn-1a:hover, a.btn-1a:active, a.btn-1b:hover, a.btn-1b:active, a.btn-1c:hover, a.btn-1c:active, a.btn-2a:hover, a.btn-2a:active {text-decoration: none}
        a.btn-1d:hover, a.btn-1d:active {text-decoration: none; color: #333}
        .piedepagina {padding: 15px 0; background-color: #7e7e7e}
        .piedepagina a {color: #fff; text-decoration: none}
        @media(min-width:768px) {
            .navbar-fixed-top .nav {padding: 15px 0}
        }
        @media(max-width:768px) {
            .navbar-fixed-top .navbar-brand {padding: 0 15px}
            .navbar-collapse {box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) inset}
            .texto-encabezado h1 {margin-top: 25px}
            #contenido {margin-top: -51px;}
        }
    </style>
</head>
<body id="top">
    <!-- Menú -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Desplegar menú</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#top"><img src="https://cdn.colombiahosting.com.co/images/ColombiaHosting.png" alt="ColombiaHosting"></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#top">Inicio</a></li>
                    <li><a href="/cpanel">cPanel</a></li>
                    <li><a href="http://<?php echo preg_replace('/^www\./','',$_SERVER['HTTP_HOST']); ?>/webmail">Webmail Pro</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Encabezado -->
    <div class="encabezado">
        <div class="texto-encabezado">
            <h1>Bienvenido a <strong><?php echo preg_replace('/^www\./','',$_SERVER['HTTP_HOST']); ?></strong></h1>
            <hr class="separador">
            <h3>Este espacio está reservado para una página muy importante</h3><br>
            <a href="#contenido" class="btn-1 btn-1d" role="button">Ver más</a>
        </div>
    </div>
    <!-- Contenido -->
    <div id="contenido"></div>
    <div class="contenido-a">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-sm-6 text-center">
                    <h2><strong>Panel de administración cPanel</strong></h2>
                    <p class="lead">Desde aquí podrá administrar su cuenta de Hosting (archivos, cuentas de correo, bases de datos, etc.) con solo unos cuantos clics.</p>
                    <a class="btn-2 btn-2a" href="/cpanel" role="button">Ir al cPanel</a>
                </div>
                <div class="col-lg-5 col-lg-offset-2 col-sm-6">
                    <img class="img-responsive" src="https://www.colombiahosting.com.co/imagenes/vistaprevia-cpanel.jpg" alt="cPanel">
                </div>
            </div>
        </div>
    </div>
    <div class="contenido-b">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-lg-offset-1 col-sm-push-6 col-sm-6 text-center">
                    <h2><strong>Webmail Profesional</strong></h2>
                    <p class="lead">Desde aquí podrá ingresar a su cuenta de correo @<?php echo preg_replace('/^www\./','',$_SERVER['HTTP_HOST']); ?> a través de una interfaz moderna y amigable.</p>
                    <a class="btn-2 btn-2a" href="http://<?php echo preg_replace('/^www\./','',$_SERVER['HTTP_HOST']); ?>/webmail" role="button">Ir al Webmail Pro</a>
                </div>
                <div class="col-lg-5 col-sm-pull-6 col-sm-6">
                    <img class="img-responsive" src="https://www.colombiahosting.com.co/imagenes/vistaprevia-webmail.jpg" alt="Webmail Pro">
                </div>
            </div>
        </div>
    </div>
    <div class="contenido-a">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2><strong>¿Tienes dudas con los servicios de Hosting y Dominio?</strong></h2>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-4">
                                <h3>Inducción</h3>
                                <p>Material de ayuda para realizar operaciones frecuentes en su cuenta de hosting como subir su sitio web, crear correos corporativos @<?php echo preg_replace('/^www\./','',$_SERVER['HTTP_HOST']); ?>, etc.</p>
                                <a href="https://www.colombiahosting.com.co/induccion" class="btn-1 btn-1a" role="button">Ver más</a>
                            </div>
                            <div class="col-md-4">
                                <h3>Base de Conocimiento</h3>
                                <p>Artículos sobre el manejo de los servicios de hosting y dominio, configuración de cuentas de correo, información para solución de problemas generales y más.</p>
                                <a href="https://soporte.colombiahosting.com.co/Knowledgebase/List" class="btn-1 btn-1b" role="button">Ver más</a>
                            </div>
                            <div class="col-md-4">
                                <h3>Video Tutoriales</h3>
                                <p>Guías para administrar su cuenta de hosting a través del cPanel, configurar sus correos en programas como Outlook/Thunderbird o dispositivos con Android/iOS.</p>
                                <a href="https://www.colombiahosting.com.co/video_tutoriales" class="btn-1 btn-1c" role="button">Ver más</a>
                            </div>
                            <div class="col-md-4">
                                <h3>Sistema de Solicitudes</h3>
                                <p>Realice solicitudes en cualquier momento a ColombiaHosting, monitoréelas, cambie su estado, incremente su prioridad y envíe respuestas.</p>
                                <a href="https://soporte.colombiahosting.com.co/Tickets/Submit" class="btn-1 btn-1a" role="button">Ver más</a>
                            </div>
                            <div class="col-md-4">
                                <h3>Chat</h3>
                                <p>Comuníquese con uno de los asesores de ColombiaHosting que se encuentre disponible, simplemente seleccionando el departamento requerido.</p>
                                <a href="https://www.colombiahosting.com.co/chat" class="btn-1 btn-1b" role="button">Ver más</a>
                            </div>
                            <div class="col-md-4">
                                <h3>Sistema de Clientes</h3>
                                <p>Acceda al listado de servicios adquiridos con ColombiaHosting, revise fechas de vencimiento, realice pagos de renovaciones, actualice sus datos, etc.</p>
                                <a href="https://www.colombiahosting.com.co/sistema" class="btn-1 btn-1c" role="button">Ver más</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="contenido-b">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 text-center">
                    <p class="text-muted">Si usted es el administrador o propietario de este sitio web, y desea quitar esta página de bienvenida, puede eliminar el archivo <strong>index.php</strong> que se encuentra dentro de la carpeta <strong>public_html</strong></p>
                    <p class="text-muted">Puede hacerlo conectándose por FTP o desde el administrador de archivos en el <strong><a href="/cpanel">panel de administración cPanel</a></strong></p>
                </div>
            </div>
        </div>
    </div>
    <!-- Pie de página -->
    <div class="piedepagina">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1 text-center">
                    <span><strong><a href="https://www.colombiahosting.com.co/">ColombiaHosting</a></strong></span>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-1.12.2.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>
