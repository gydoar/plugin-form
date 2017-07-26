<?php
/**
* Plugin Name: Formulario e registro
* Plugin URI: https://andres-dev.com/
* Description: Formulario de registro (Prueba) para marketeros web
* Version: 1.0 
* Author: Andres Vega
* Author URI: https://andres-dev.com
*/

/*
* @description Hook que se ejecuta al activar el plugin
*/
register_activation_hook( __FILE__, 'guardar_base' );


function html_form_code() {
    echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
    echo '<p>';
    echo 'Nombre:<br />';
    echo '<input type="text" name="cf-name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["cf-name"] ) ? esc_attr( $_POST["cf-name"] ) : '' ) . '" size="40" />';
    echo '</p>';
    echo '<p>';
    echo 'Email<br />';
    echo '<input type="email" name="cf-email" value="' . ( isset( $_POST["cf-email"] ) ? esc_attr( $_POST["cf-email"] ) : '' ) . '" size="40" />';
    echo '</p>';

    echo '<p>';
    echo 'Identificación:<br />';
    echo '<input type="text" name="cf-ident" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["cf-ident"] ) ? esc_attr( $_POST["cf-ident"] ) : '' ) . '" size="40" />';
    echo '</p>';
    echo '<p><input type="submit" name="cf-submitted" value="Enviar"/></p>';
    echo '</form>';
}


//Enviar a la base de datos
function guardar_base() {
	global $wpdb;

	// El nombre de la tabla, utilizamos el prefijo de wordpress
    $table_name = $wpdb->prefix . 'form';
 
    // Declaramos la tabla que se creará
    $sql = "CREATE TABLE $table_name (
      `id` int(11) NOT NULL AUTO_INCREMENT,
	  `nombre` varchar(255) NOT NULL,
	  `email` varchar(255) NOT NULL,
	  `identificacion` varchar(255) NOT NULL,
	  `id_user` varchar(255) NOT NULL,
      UNIQUE KEY id (id)
    );";

	// upgrade contiene la función dbDelta la cuál revisará si existe la tabla o no
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Creamos la tabla
    dbDelta($sql);

    // Obtenemos el ID del usuario activo
	$cu = wp_get_current_user();
	$id_user = $cu->ID;

	// Si se ejecuta el boton submit se procesa
	if ( isset( $_POST['cf-submitted'] ) ) {

		$consulta_user = $wpdb->get_var("SELECT count(*) from wp_form where id_user = '$id_user'");

		if ($consulta_user <= 2) {
			$sql = "INSERT INTO `wp_form` (`nombre`,`email`,`identificacion`, `id_user`) 
				VALUES ('{$_POST['cf-name']}','{$_POST['cf-email']}','{$_POST['cf-ident']}','$id_user')";
        
	        $wpdb->query($sql);   


					//Los reseteamos en blanco
					$_POST['cf-name'] = '';
	                $_POST['cf-email'] = '';
	                $_POST['cf-ident'] = '';
	       

	         echo "se almacenaron";

			}else{
				echo "Solo puedes almacenar 2";
			}
	}
}

		// Creamos el shortcode
		function form_shortcode() {
			ob_start();
			guardar_base();
			html_form_code();

			return ob_get_clean();
	}

	add_shortcode( 'contact_form', 'form_shortcode' );
  