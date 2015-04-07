<?php
/**
 *  Copyright 2013 Gabriel Nicolás González Ferreira <gabrielinuz@gmail.com> 
 *
 *  Permission is hereby granted, free of charge, to any person obtaining
 *  a copy of this software and associated documentation files (the
 *  "Software"), to deal in the Software without restriction, including
 *  without limitation the rights to use, copy, modify, merge, publish,
 *  distribute, sublicense, and/or sell copies of the Software, and to
 *  permit persons to whom the Software is furnished to do so, subject to
 *  the following conditions:
 *
 *  The above copyright notice and this permission notice shall be
 *  included in all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 *  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 *  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 *  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 *  LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 *  OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 *  WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 **/

//FOR ERROR MESSAGES
    define('NO_MESSAGE', 'No se configuró un mensaje para este error, consulte al administrador del sistema');

//FOR SINGLETTON
    define('NOT_CLONE', 'Operación no válida: No se puede clonar una instancia de ');
    define('NOT_DESERIALIZE', 'Operación no válida: No se puede deserializar una instancia de ');

//FOR VALIDATOR
    define('SEARCH_NO_RESULT', 'Su búsqueda no ha obtenido resultados');
    define('NO_SPECIAL_CHARS', 'El siguiente campo no debe contener caracteres especiales: ');
    define('EXCEEDED_CHARS', 'El siguiente campo ha superado el número de caracteres permitidos: ');
    define('UNIQUE', 'El siguiente campo debe ser único, este ya existe en el sistema: ');
    define('EQUALS', 'Los siguientes campos deben ser iguales: ');
    define('REQUIRED', 'El siguiente campo es requerido, no puede estar vacío: ');
    define('DATETIME', 'El siguiente campo debe ser una fecha y hora: ');
    define('DATE', 'El siguiente campo debe ser una fecha válida: ');
    define('EMAIL', 'El siguiente campo debe ser un email válido: ');
    define('INTEGER_NUMBER', 'El siguiente campo sólo debe contener números enteros: ');
    define('NOT_VALID_ID', 'Identificador no válido');
    define('PASSWORDS_NOT_MATCH', 'Las contraseñas no coinciden');
    define('NOT_DELETE_ADMIN', 'No se puede borrar al administrador');
    define('EXISTING_USER', 'El usuario ya existe');
    define('EXISTING_ROLE', 'El rol ya existe');
    define('NO_VALID_USER', '¡El usuario no es válido!');
    define('NO_AUTHORIZED_ACTION', 'No está autorizado para esta acción :(');
    define('GO_BACK', '¡Ir Atrás!');
	define('USED_ROLE', 'No se puede borrar, hay usuarios utilizando este rol.');

//FOR REQUEST DISPATCHER
    define('I_DONT_UNDERSTAND', "No entiendo el siguiente mensaje: ");
    define('METHOD', 'El método ');
    define('NOT_IMPLEMENTED', ' aún no ha sido implementado :(');

//FOR PAGINATION
    define('FIRST', 'principio');    
    define('LAST', 'final');
    define('NEXT', 'siguiente');
    define('PREVIOUS', 'anterior');
    define('ROWS_PER_PAGE', 'Filas por página: ');
    define('APPLY', 'aplicar');
	define('GO_TO', 'Ir a: ');
	define('SET_PAGE', 'Definir página: ');
    define('PAGE', 'Página: ');
    define('OF', ' de ');
?>